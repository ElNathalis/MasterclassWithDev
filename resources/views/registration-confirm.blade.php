@extends('layouts.app')

@section('title', 'Подтверждение записи')

@section('content')
<div class="row">
    <div class="row--small">
        <h2>Подтверждение записи на мастер-класс</h2>
        
        <table style="margin: 30px 0;">
            <tr>
                <td><strong>ФИО:</strong></td>
                <td>{{ $user->full_name }}</td>
            </tr>
            <tr>
                <td><strong>Вид творчества:</strong></td>
                <td>{{ $masterClass->category->name }}</td>
            </tr>
            <tr>
                <td><strong>Мастер:</strong></td>
                <td>{{ $masterClass->user->full_name }}</td>
            </tr>
            <tr>
                <td><strong>Дата:</strong></td>
                <td>{{ $masterClass->date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td><strong>Время:</strong></td>
                <td>{{ $masterClass->time }}</td>
            </tr>
            <tr>
                <td><strong>Стоимость:</strong></td>
                <td>{{ $masterClass->price > 0 ? $masterClass->price . ' руб.' : 'Бесплатно' }}</td>
            </tr>
        </table>
        <a href="{{ url()->previous() }}" >Отмена</a>
        
        <div style="display: flex; gap: 15px;">
            <form action="{{ route('registration.store', $masterClass->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn" style="background: #20416c; color: #fff;">Подтвердить запись</button>
            </form>
        </div>
    </div>
</div>
@endsection