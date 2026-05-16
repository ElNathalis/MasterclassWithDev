@extends('layouts.app')

@section('title', 'Вход')

@section('top_line', '1')

@section('content')
    <div class="row">
        <div class="row--small">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h2>Вход в систему</h2>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email') <small style="color:red;" class="error">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                    @error('password') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <button class="btn" type="submit">Войти</button>
                </div>
                <p>Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a></p>
            </form>
        </div>
    </div>
@endsection