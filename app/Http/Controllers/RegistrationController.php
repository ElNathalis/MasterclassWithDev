<?php

namespace App\Http\Controllers;

use App\Models\MasterClass;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    public function show(MasterClass $masterClass): View|RedirectResponse
    {
        $user = auth()->user();

        // Проверки перед показом формы
        if ($user->role !== 'visitor') {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Только посетители могут записываться на мастер-классы.');
        }

        if ($masterClass->user_id === $user->id) {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Вы не можете записаться на свой собственный мастер-класс.');
        }

        if ($masterClass->date < now()->toDateString()) {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Нельзя записаться на прошедший мастер-класс.');
        }

        $currentRegistrations = $masterClass->registrations()->count();
        if ($currentRegistrations >= $masterClass->max_participants) {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Все места заняты.');
        }

        $alreadyRegistered = Registration::where('user_id', $user->id)
            ->where('master_class_id', $masterClass->id)
            ->exists();

        if ($alreadyRegistered) {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Вы уже записаны на этот мастер-класс.');
        }

        // Если все проверки пройдены - показываем форму подтверждения
        return view('registration-confirm', compact('masterClass', 'user'));
    }

    public function store(Request $request, MasterClass $masterClass): RedirectResponse
    {
        $user = auth()->user();

        // Все проверки (как выше)
        if ($user->role !== 'visitor') {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Только посетители могут записываться.');
        }

        if ($masterClass->user_id === $user->id) {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Нельзя записаться на свой мастер-класс.');
        }

        if ($masterClass->date < now()->toDateString()) {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Мастер-класс уже прошёл.');
        }

        // Проверка существующей записи
        $exists = Registration::where('user_id', $user->id)
            ->where('master_class_id', $masterClass->id)
            ->exists();

        if ($exists) {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Вы уже записаны на этот мастер-класс.');
        }

        // Проверка мест
        $currentCount = $masterClass->registrations()->count();

        if ($currentCount >= $masterClass->max_participants) {
            return redirect()->route('category.show', $masterClass->category_id)
                ->with('error', 'Все места заняты.');
        }

        // Создаём запись
        Registration::create([
            'user_id' => $user->id,
            'master_class_id' => $masterClass->id,
        ]);

        return redirect()->route('category.show', $masterClass->category_id)
            ->with('success', 'Вы успешно записались на мастер-класс!');
    }
}