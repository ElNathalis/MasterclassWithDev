@extends('layouts.app')

@section('title', 'Регистрация')

@section('top_line', '1')

@section('content')
    <div class="row">
        <div class="row--small">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <h2>Форма регистрации</h2>

                <div class="form-group">
                    <label>ФИО</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required>
                    @error('full_name') <small style="color:red;" class="error">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <small style="color:red;" class="error">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                    @error('password') <small style="color:red;" class="error">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label>Подтверждение пароля</label>
                    <input type="password" name="password_confirmation" required>
                </div>
                <div class="form-group">
                    <label>Номер телефона</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required pattern="\+?[0-9]{10,15}"
                        title="Номер телефона должен содержать от 10 до 15 цифр и может начинаться с +"
                        oninput="let val = this.value.replace(/[^0-9+]/g, ''); this.value = val.replace(/(?!^)\+/g, '').substring(0, 20);">
                    @error('phone') <small style="color:red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <button class="btn" type="submit">Зарегистрироваться</button>
                </div>
            </form>
        </div>
    </div>
@endsection