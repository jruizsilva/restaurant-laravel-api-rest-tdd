<?php

namespace Database\Seeders;

use App\Models\Plate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $name = 'Plate ' . $i;
            Plate::factory()->create([
                'restaurant_id' => 1,
                'name' => $name,
            ]);
        }
        for ($i = 10; $i <= 20; $i++) {
            $name = 'Restaurant ' . $i;
            Plate::factory()->create([
                'restaurant_id' => 2,
                'name' => $name,
            ]);
        }
    }
}