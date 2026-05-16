@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
<div class="row">
    <div class="row--small">
        <h2>Управление пользователями</h2>

        @if(session('success'))
            <div style="color: green; margin-bottom: 15px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="color: red; margin-bottom: 15px;">{{ session('error') }}</div>
        @endif

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr style="border-bottom: 1px solid #ccc;">
                        <td>{{ $u->full_name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->phone }}</td>
                        <td>{{ $u->role }}</td>
                        <td>
                            <form action="{{ route('admin.updateRole', $u->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="role" value="master">
                                <button type="submit" class="btn" style="padding: 5px 10px; font-size: 12px;">Сделать ведущим</button>
                            </form>
                            <form action="{{ route('admin.updateRole', $u->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="role" value="visitor">
                                <button type="submit" class="btn" style="padding: 5px 10px; font-size: 12px;">Разжаловать</button>
                            </form>
                            <form action="{{ route('admin.destroy', $u->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Удалить пользователя?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="padding: 5px 10px; font-size: 12px; border-color: red; color: red;">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection