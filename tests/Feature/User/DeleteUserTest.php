<?php

namespace Tests\Feature\User;

use App\Enums\Roles;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $anotherUser;
    protected Restaurant $restaurant;
    protected Restaurant $anotherRestaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole(Roles::OWNER->name);
        $this->anotherUser = User::factory()->create();
        $this->anotherUser->assignRole(Roles::USER->name);

    }

    #[Test]
    public function owner_can_delete_any_user(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route("users.destroy", [
            "user" => $this->anotherUser->id
        ]));
        dd($response->json());

        $response->assertStatus(200);
    }
}