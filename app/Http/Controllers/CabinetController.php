<?php

namespace App\Http\Controllers;

use App\Models\MasterClass;
use Illuminate\View\View;

class CabinetController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if ($user->role !== 'master') {
            abort(403, 'Доступ только для ведущих.');
        }

        $masterClasses = MasterClass::where('user_id', $user->id)
            ->with(['registrations.user']) // загружаем записи и их авторов
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('cabinet', compact('user', 'masterClasses'));
    }
}