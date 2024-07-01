<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Example',
            'last_name' => 'Example',
            'email' => 'example@example.com',
        ]);
        User::factory()->create([
            'name' => 'Example2',
            'last_name' => 'Example2',
            'email' => 'example2@example.com',
        ]);
    }
}