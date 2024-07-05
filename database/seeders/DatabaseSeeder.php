<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);
        $user = User::factory()->create([
            'name' => 'Owner',
            'email' => 'owner@gmail.com'
        ]);
        $user->assignRole(Roles::OWNER->name);
    }
}