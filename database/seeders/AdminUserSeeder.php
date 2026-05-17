<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',                 // будет перезаписано full_name
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Администратор Системы',
            'phone' => '+70000000000',
            'role' => 'admin',
        ]);
    }
}
