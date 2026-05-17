<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $users = User::where('id', '!=', auth()->id())->get();
        return view('admin', compact('users'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'role' => 'required|in:visitor,master,admin',
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        // Если пользователь был ведущим, но теперь нет — удаляем его мастер-классы
        if ($oldRole === 'master' && $request->role !== 'master') {
            $user->masterClasses()->delete();
        }

        return back()->with('success', 'Роль пользователя обновлена.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя удалить самого себя.');
        }

        $user->delete();

        return back()->with('success', 'Пользователь удалён.');
    }
}