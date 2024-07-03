<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();
        return [
            'name' => $name,
            'slug' => str($name)->slug() . "-" . uniqid(),
            'description' => fake()->text(100),
            'user_id' => fn() => User::factory()->create()->id,
        ];
    }
}