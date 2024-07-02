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
            $name = 'Restaurant ' . $i;
            $slug = str($name)->slug() . "-" . uniqid();
            Restaurant::factory()->create([
                'user_id' => 1,
                'name' => $name,
                'slug' => $slug
            ]);
        }
        for ($i = 10; $i <= 20; $i++) {
            $name = 'Restaurant ' . $i;
            $slug = str($name)->slug() . "-" . uniqid();
            Restaurant::factory()->create([
                'user_id' => 2,
                'name' => $name,
                'slug' => $slug
            ]);
        }

    }
}