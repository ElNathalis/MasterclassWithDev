@extends('layouts.app')

@section('title', 'Главная')

@section('body_class', 'dp') {{-- класс как на странице категории для фона? Возможно, не нужен --}}

@section('content')
    <div class="row">
        <div class="hover" style="background-image: url('{{ asset('img/hover_arch.png') }}')"></div>{{-- общий фон --}}
        <div class="title">Добро пожаловать!</div>
        <div class="row--small grid between">
            <div class="content">
                <p>Клуб любителей творчества «ОчУмелые ручки» приглашает всех желающих освоить различные виды
                    декоративно-прикладного искусства: архитектурное моделирование, кулинарию, резьбу по дереву и многое
                    другое.</p>
                <p>Наши мастер-классы проводятся опытными мастерами. Присоединяйтесь!</p>
                @auth
                    @if($myRegistrations->count())
                        <div style="margin-top: 40px;">
                            <h3>Мои записи на мастер-классы</h3>
                            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                                <thead>
                                    <tr>
                                        <th>Вид творчества</th>
                                        <th>Мастер-класс</th>
                                        <th>Мастер</th>
                                        <th>Дата</th>
                                        <th>Время</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myRegistrations as $reg)
                                        <tr>
                                            <td>{{ $reg->masterClass->category->name }}</td>
                                            <td>{{ $reg->masterClass->title }}</td>
                                            <td>{{ $reg->masterClass->user->full_name }}</td>
                                            <td>{{ $reg->masterClass->date->format('d.m.Y') }}</td>
                                            <td style="text-align: right;">{{ $reg->masterClass->time}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endauth
            </div>
            <ul class="menu">
                @foreach($categories as $category)
                    <li><a href="{{ route('category.show', $category->id) }}">{{ $category->name }}</a></li>
                @endforeach
            </ul>

        </div>

    </div>
@endsection