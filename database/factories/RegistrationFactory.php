<?php

namespace Database\Factories;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\MasterClass;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'master_class_id' => MasterClass::factory(),
        ];
    }
}
