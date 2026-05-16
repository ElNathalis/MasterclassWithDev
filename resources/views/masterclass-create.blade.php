@extends('layouts.app')

@section('title', 'Добавить мастер-класс')

@section('top_line', '1')

@section('content')
    <div class="row">
        <div class="row--small">
            <form method="POST" action="{{ route('masterclass.store') }}">
                @csrf
                <h2>Форма добавления мастер-класса</h2>

                <div class="form-group">
                    <label>Вид творчества</label>
                    <select name="category_id" required>
                        <option value="">Выберите вид</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <small style="color:red;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Название мастер-класса</label>
                    <input type="text" name="title" value="{{ old('title') }}" required>
                    @error('title') <small style="color:red;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Описание мастер-класса</label>
                    <textarea name="description" required>{{ old('description') }}</textarea>
                    @error('description') <small style="color:red;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Дата</label>
                    <input type="date" name="date" id="date" value="{{ old('date') }}" required>
                    @error('date') <small style="color:red;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Время</label>
                    <select name="time" id="time" required>
                        <option value="">Сначала выберите дату</option>
                        <option value="09:00" {{ old('time') == '09:00' ? 'selected' : '' }}>09:00</option>
                        <option value="11:00" {{ old('time') == '11:00' ? 'selected' : '' }}>11:00</option>
                        <option value="13:00" {{ old('time') == '13:00' ? 'selected' : '' }}>13:00</option>
                        <option value="15:00" {{ old('time') == '15:00' ? 'selected' : '' }}>15:00</option>
                    </select>
                    @error('time')
                        <div class="error" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Количество человек в группе</label>
                    <input type="text" name="max_participants" value="{{ old('max_participants') }}" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    @error('max_participants') <small style="color:red;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Стоимость (руб.)</label>
                    <input type="text" name="price" value="{{ old('price') }}" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    @error('price') <small style="color:red;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Отправить</button>
                </div>
            </form>
        </div>
    </div>

@endsection

{{-- для блокировки времени --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('date');
            const timeSelect = document.getElementById('time');

            function checkSlots(date) {
                fetch(`/masterclass/check-slots?date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        const occupied = data.occupied_slots || [];
                        Array.from(timeSelect.options).forEach(option => {
                            if (option.value === '') return; // плейсхолдер
                            option.disabled = occupied.includes(option.value);
                        });
                        // Сброс выбранного значения, если оно теперь занято
                        if (timeSelect.selectedIndex !== -1 && timeSelect.options[timeSelect.selectedIndex].disabled) {
                            timeSelect.value = '';
                        }
                    })
                    .catch(error => console.error('Ошибка проверки слотов:', error));
            }

            dateInput.addEventListener('change', function () {
                if (this.value) checkSlots(this.value);
            });

            if (dateInput.value) checkSlots(dateInput.value);
        });
    </script>
@endpush