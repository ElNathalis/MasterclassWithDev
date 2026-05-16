@extends('layouts.app')

@section('title', 'Личный кабинет')

@section('body_class', 'dp')

@section('content')
    <div class="row">
        {{-- Фоновая подложка как на странице кабинета (общий hover) --}}
        <div class="hover" style="background-image: url('{{ asset('img/hover_arch.png') }}')"></div>{{-- общий фон --}}
        <div class="title"></div> {{-- пустой, т.к. в макете заголовка нет --}}

        <div class="row--small grid between">
            <div class="content driver-page">
                {{-- Фото ведущего (используем поле photo, если есть, иначе заглушка) --}}
                <div class="driver-page-photo">
                    <img src="{{ $user->photo ? asset('img/' . $user->photo) : asset('img/driver-page.png') }}"
                        alt="{{ $user->full_name }}">
                </div>

                {{-- Имя выводится абсолютным позиционированием --}}
                <div class="driver-page-name">{{ $user->full_name }}</div>

                <div class="driver-page-text">
                    <div class="driver-page-my">Мои мастер-классы</div>
                    <table class="driver-page-table">
                        <tbody>
                            @forelse($masterClasses as $mc)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($mc->date)->format('d.m.Y') }} {{ \Carbon\Carbon::parse($mc->time)->format('H:i') }}</td>
                                    <td>
                                        <b>{{ $mc->title }}</b>
                                        @if($mc->registrations->count())
                                            @foreach($mc->registrations as $reg)
                                                <p>
                                                    {{ $loop->iteration }}. {{ $reg->user->full_name }}
                                                    ({{ \Carbon\Carbon::parse($reg->user->created_at)->format('d.m.Y') }})<br>
                                                    email: {{ $reg->user->email }}<br>
                                                    тел: {{ $reg->user->phone }}
                                                </p>
                                            @endforeach
                                        @else
                                            <p><em>Нет записавшихся</em></p>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="driver-page-btn-wrapper">
                                            <a href="{{ route('masterclass.edit', $mc->id) }}"
                                                style="font-size: 12px; margin-left: 10px;">✏️ ред.</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">У вас пока нет мастер-классов.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="driver-page-btn-wrapper">
                    <a href="{{ route('masterclass.create') }}" class="driver-page-btn btn">Добавить мастер-класс</a>
                </div>
            </div>

            {{-- Боковое меню категорий (как на других страницах) --}}
            <ul class="menu">
                @foreach(\App\Models\Category::all() as $cat)
                    <li><a href="{{ route('category.show', $cat->id) }}">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection