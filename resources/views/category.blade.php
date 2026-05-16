@extends('layouts.app')

@section('title', $category->name)

@section('content')

    <div class="row">
        {{-- Фоновая картинка категории --}}
        <div class="hover"
            style="background-image: url('{{ $category->background_image ? asset('img/' . $category->background_image) : asset('img/hover.png') }}')">
        </div>
        <div class="title">{{ $category->name }}</div>

        {{-- Блок с описанием и меню --}}
        <div class="row--small grid between">
            <div class="content" style="margin-top: -110px;">
                @if($category->image)
                    <img src="{{ asset('img/' . $category->image) }}" alt="{{ $category->name }}"
                        style="width: 150px; height: 150px; object-fit: cover; float: left; margin-right: 20px;">
                @endif
                {!! $category->description !!}
            </div>
            <ul class="menu">
                @foreach(\App\Models\Category::all() as $cat)
                    <li><a href="{{ route('category.show', $cat->id) }}">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>

        {{-- Расписание --}}
        @if($masterClasses->count())
            <div class="row shedule">
                <div class="row--small">
                    <h2>Расписание</h2>

                    {{-- Сообщения --}}
                    @if(session('success'))
                        <div style="color: lightgreen; margin-bottom: 15px; text-align: center;">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div style="color: #ffaaaa; margin-bottom: 15px; text-align: center;">{{ session('error') }}</div>
                    @endif

                    <div class="drivers">
                        @foreach($masterClasses as $mc)
                            <div class="driver grid">
                                <div class="driver-left grid">
                                    <div class="driver-photo">
                                        <img src="{{ $mc->user->photo ? asset('img/' . $mc->user->photo) : asset('img/driver1.png') }}"
                                            alt="{{ $mc->user->full_name }}">
                                    </div>
                                    <div class="driver-text">
                                        <div class="driver-name">{{ $mc->user->full_name }}</div>
                                        <div class="driver-desc">{{ $mc->title }} — {{ $mc->description }}</div>
                                        <div style="font-size: 14px; margin-top: 5px;">
                                            Цена: {{ (int) $mc->price }} руб. |
                                            Свободных мест: {{ $mc->max_participants - $mc->registrations_count }} из
                                            {{ $mc->max_participants }}
                                        </div>
                                    </div>
                                </div>
                                <div class="driver-right" style="width: 182px;">
                                    @auth
                                        @if(auth()->user()->role === 'visitor')
                                            @if($mc->registrations_count < $mc->max_participants)
                                                <a href="{{ route('registration.show', $mc->id) }}" class="driver-btn"
                                                    style="display: inline-block; width: 182px; text-align: center;">
                                                    записаться
                                                </a>
                                                <form id="reg-form-{{ $mc->id }}" action="{{ route('registration.store', $mc->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            @else
                                                <div class="driver-btn"
                                                    style="color: #ff9999; border-color: #ff9999; cursor: default; text-align: center; width: 182px;">
                                                    Мест нет
                                                </div>
                                            @endif
                                        @else
                                            <div class="driver-btn"
                                                style="color: #ccc; border-color: #ccc; cursor: default; text-align: center; width: 182px;">
                                                Только для
                                                посетителей</div>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="driver-btn">Войдите, чтобы
                                            записаться</a>
                                    @endauth
                                    <div class="driver-time">
                                        {{ \Carbon\Carbon::parse($mc->date)->format('d.m.Y') }} {{ $mc->time }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <p style="text-align: center; margin-top: 30px;">В этой категории пока нет мастер-классов.</p>
        @endif
    </div>
@endsection