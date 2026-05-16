@extends('layouts.app')

@section('title', 'Редактировать мастер-класс')

@section('top_line', '1')

@section('content')
    <div class="row">
        <div class="row--small">
            <form method="POST" action="{{ route('masterclass.update', $masterClass->id) }}">
                @csrf
                @method('PATCH')
                <h2>Редактирование мастер-класса</h2>

                <p><strong>{{ $masterClass->title }}</strong> ({{ $masterClass->date->format('d.m.Y') }}
                    {{ $masterClass->time }})</p>

                <div class="form-group">
                    <label>Описание мастер-класса</label>
                    <textarea name="description" required>{{ old('description', $masterClass->description) }}</textarea>
                    @error('description') <small style="color:red;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Стоимость (руб.)</label>
                    <input type="text" name="price" value="{{ old('price', (int) $masterClass->price) }}" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    @error('price') <small style="color:red;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Сохранить</button>
                    <a href="{{ route('cabinet') }}" class="btn" style="margin-left: 10px;">Отмена</a>
                </div>
            </form>
        </div>
    </div>
@endsection