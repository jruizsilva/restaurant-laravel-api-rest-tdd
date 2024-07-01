<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Restaurant::factory()->count(10)->create([
            'user_id' => 1,
        ]);
        Restaurant::factory()->count(10)->create([
            'user_id' => 2,
        ]);
    }
}