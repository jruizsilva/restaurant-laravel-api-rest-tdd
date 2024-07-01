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
        for ($i = 1; $i <= 10; $i++) {
            Restaurant::factory()->create([
                'user_id' => 1,
                'name' => 'Restaurant ' . $i
            ]);
        }
        for ($i = 1; $i <= 10; $i++) {
            Restaurant::factory()->create([
                'user_id' => 2,
                'name' => 'Restaurant ' . $i
            ]);
        }
    }
}