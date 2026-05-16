<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ОчУмелые ручки')</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="@yield('body_class')">
    <div class="header">
        <div class="row grid middle between">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="Логотип">
                </a>
            </div>
            <div class="title">
                Клуб любителей творчества «ОчУмелые ручки»
            </div>
            <div class="auth">
                @auth
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.index') }}">Админ</a>
                        @endif
                        @if(auth()->user()->role === 'master')
                            <a href="{{ route('cabinet') }}">Кабинет</a>
                        @endif
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            style="display: flex; align-items: center; gap: 5px;">
                            <img src="{{ asset('img/arrow.png') }}" alt="" style="width: 15px; height: 10px;">
                            Выход
                        </a>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" style="display: flex; align-items: center; gap: 5px;">
                        <img src="{{ asset('img/arrow.png') }}" alt="" style="width: 15px; height: 10px;">
                        Вход
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="row row--nogutter">
        <div class="menu-burger">
            <div class="burger">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    @hasSection('top_line')
        <div class="row row--nogutter top-line">
            <div class="line"></div>
        </div>
    @endif

    <div class="main">
        @yield('content')
    </div>

    <div class="row row--nogutter">
        <div class="line"></div>
    </div>

    <div class="footer">
        <div class="row">
            <div class="row--small grid between">
                <div class="address">Наш адрес: ВДНХ, 120в</div>
                <div class="tel">Тел: 89123456765</div>
                <div class="copy">(с) Copyright, 2017</div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>

</html>