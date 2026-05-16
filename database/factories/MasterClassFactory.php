<?php

namespace Database\Factories;

use App\Models\MasterClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;

/**
 * @extends Factory<MasterClass>
 */
class MasterClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->master(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->text(100),
            'date' => now()->addDays(7)->format('Y-m-d'),
            'time' => fake()->randomElement(MasterClass::TIME_SLOTS),
            'max_participants' => 10,
            'price' => 1000,
        ];
    }
}
