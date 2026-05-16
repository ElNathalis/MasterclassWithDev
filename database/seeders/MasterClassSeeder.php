<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MasterClass;
use Illuminate\Support\Facades\Hash;

class MasterClassSeeder extends Seeder
{
    public function run()
    {
        // Создаём тестового ведущего, если его ещё нет
        $master = User::firstOrCreate(
            ['email' => 'master@example.com'],
            [
                'name' => 'Иванова Ольга Ивановна',
                'full_name' => 'Иванова Ольга Ивановна',
                'phone' => '+71234567890',
                'password' => Hash::make('password'),
                'role' => 'master',
            ]
        );

        // Получаем категории
        $categories = \App\Models\Category::all();

        // Данные мастер-классов
        $masterClasses = [
            [
                'category_id' => $categories->where('name', 'Архитектурное моделирование')->first()->id,
                'title' => 'Моделирование моделей транспорта',
                'description' => 'Мастер-класс научит основам моделирования различных видов транспортных средств.',
                'date' => '2026-06-05',
                'time' => '17:00',
                'max_participants' => 10,
                'price' => 500,
            ],
            [
                'category_id' => $categories->where('name', 'Кулинария')->first()->id,
                'title' => 'Шоколадные поделки',
                'description' => 'Шоколадные фонтаны, фруктовые пальмы, приготовление шоколадных конфет.',
                'date' => '2026-06-14',
                'time' => '17:00',
                'max_participants' => 8,
                'price' => 700,
            ],
            [
                'category_id' => $categories->where('name', 'Резьба по дереву')->first()->id,
                'title' => 'Геометрическая резьба по дереву',
                'description' => 'Мастер-класс для начинающих, знакомство с геометрической резьбой.',
                'date' => '2026-06-05',
                'time' => '15:00',
                'max_participants' => 5,
                'price' => 300,
            ],
        ];

        foreach ($masterClasses as $mc) {
            MasterClass::create([
                'user_id' => $master->id,
                'category_id' => $mc['category_id'],
                'title' => $mc['title'],
                'description' => $mc['description'],
                'date' => $mc['date'],
                'time' => $mc['time'],
                'max_participants' => $mc['max_participants'],
                'price' => $mc['price'],
            ]);
        }
    }
}