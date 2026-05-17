<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\'-]+(\s+[\p{L}\'-]+)+$/u'],
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone', 'regex:/^\+?[0-9]{10,15}$/'],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'full_name.required' => 'ФИО обязательно для заполнения',
            'full_name.string' => 'ФИО должно быть строкой',
            'full_name.max' => 'Максимум 255 символов',
            'full_name.regex' => 'Минимум два слова',

            'email.required' => 'Почта обязательна для заполнения',
            'email.email' => 'Введите корректный email',
            'email.unique' => 'Email уже зарегистрирован',

            'phone.required' => 'Телефон обязателен для заполнения',
            'phone.max' => 'Номер телефона не должен превышать 20 символов',
            'phone.unique' => 'Телефон уже зарегистрирован',
            'phone.regex' => '10-15 цифр, может начинаться с "+"',

            'password.required' => 'Пароль обязателен для заполнения',
            'password.min' => 'Минимум 8 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'role' => 'visitor',
        ]);

        Auth::login($user);

        $route = ($user->role === 'master') ? route('cabinet') : route('home');

        return redirect()->intended($route)->with('success', 'Регистрация прошла успешно!');
    }
}
