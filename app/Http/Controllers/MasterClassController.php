<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MasterClass;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class MasterClassController extends Controller
{
    public function create(): View
    {
        if (auth()->user()->role !== 'master') {
            abort(403, 'Только ведущие могут добавлять мастер-классы.');
        }
        $occupiedSlots = MasterClass::where('user_id', auth()->id())
            ->where('date', '>=', now())
            ->get(['date', 'time'])
            ->groupBy('date')
            ->map(fn($items) => $items->pluck('time')->toArray())
            ->toArray();

        $categories = Category::all();
        return view('masterclass-create', compact('categories', 'occupiedSlots'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->role !== 'master') {
            abort(403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:100|min:3',
            'description' => 'required|string|max:255|min:3',
            'max_participants' => 'required|integer|min:1|max:1000',
            'price' => 'required|integer|min:0|max:999999',
            'time' => ['required', 'in:' . implode(',', MasterClass::TIME_SLOTS)],
            'date' => [
                'required',
                'date_format:Y-m-d', // СНАЧАЛА проверяем формат
                'after:today',
                'before_or_equal:' . now()->addYear()->format('Y-m-d'),
            ]
        ], [
            'category_id.required' => 'Выберите вид творчества.',
            'category_id.exists' => 'Выбранный вид творчества не существует.',

            'title.required' => 'Введите название мастер-класса.',
            'title.max' => 'Название не должно превышать 100 символов.',
            'title.min' => 'Минимум 3 символа.',

            'description.required' => 'Введите описание мастер-класса.',
            'description.string' => 'Описание должно быть текстом.',
            'description.max' => 'Описание не должно превышать 255 символов.',
            'description.min' => 'Минимум 3 символа.',

            'date.required' => 'Введите дату проведения.',
            'date.date_format' => 'Используйте формат ДД-ММ-ГГГГ.',
            'date.after' => 'Дата должна быть не ранее завтрашнего дня.',
            'date.before_or_equal' => 'Дата не может быть позже, чем через год от текущей даты.',

            'time.required' => 'Введите время проведения.',
            'time.in' => 'Время должно быть одним из: ' . implode(', ', MasterClass::TIME_SLOTS),

            'max_participants.required' => 'Введите количество человек в группе.',
            'max_participants.integer' => 'Количество человек должно быть целым числом.',
            'max_participants.min' => 'Минимальное количество участников — 1.',
            'max_participants.max' => 'Максимальное количество участников — 1000.',

            'price.required' => 'Укажите стоимость.',
            'price.integer' => 'Цена должна быть целым числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'price.max' => 'Цена не может превышать 999 999 руб.',
        ]);

        // после успешной валидации формата даты, проверяем уникальность
        $exists = MasterClass::where('user_id', auth()->id())
            ->whereDate('date', $validated['date'])
            ->where('time', 'like', $validated['time'] . '%')
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['date' => 'У вас уже есть мастер-класс на эту дату и время.']);
        }

        MasterClass::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'max_participants' => $validated['max_participants'],
            'price' => $validated['price'] ?? null,
        ]);

        return redirect()->route('cabinet')->with('success', 'Мастер-класс успешно добавлен.');
    }
    public function edit(MasterClass $masterClass): View
    {
        // Только владелец может редактировать
        if ($masterClass->user_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::all();
        return view('masterclass-edit', compact('masterClass', 'categories'));
    }

    public function update(Request $request, MasterClass $masterClass): RedirectResponse
    {
        if ($masterClass->user_id !== auth()->id()) {
            abort(403);
        }

        // Разрешено редактировать только описание и цену
        $validated = $request->validate([
            'description' => 'required|string|max:255|min:3',
            'price' => 'required|regex:/^\d+$/|integer|min:0|max:999999',
        ], [
            'description.required' => 'Введите описание мастер-класса.',
            'description.string' => 'Описание должно быть текстом.',
            'description.max' => 'Описание не должно превышать 255 символов.',
            'description.min' => 'Минимум 3 символа.',

            'price.required' => 'Укажите стоимость.',
            'price.integer' => 'Цена должна быть целым числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'price.max' => 'Цена не превышает 999 999 руб.',
            'price.regex' => 'Цена должна быть числом.',
        ]);

        $masterClass->update($validated);

        return redirect()->route('cabinet')->with('success', 'Мастер-класс обновлён.');
    }

    public function checkOccupiedSlots(Request $request)
    {
        if (auth()->user()->role !== 'master') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $request->validate([
            'date' => 'required|date'
        ]);

        $user_id = Auth::id();
        $date = $request->date;

        $occupiedSlots = MasterClass::where('user_id', $user_id)
            ->whereDate('date', $date)
            ->pluck('time')
            ->map(function ($time) {
                // Обрезаем секунды: '09:00:00' -> '09:00'
                return \Carbon\Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'occupied_slots' => $occupiedSlots,
            'date' => $date
        ]);
    }
}