<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OwnerTest extends TestCase
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
        $this->anotherUser = User::factory()->create();
        $this->user->assignRole(Roles::OWNER->name);
        $this->restaurant = Restaurant::factory()->create(["user_id" => $this->user->id]);
        $this->anotherRestaurant = Restaurant::factory()->create(["user_id" => $this->anotherUser->id]);

    }

    #[Test]
    public function owner_can_delete_any_restaurant(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route("restaurants.destroy", [
            "restaurant" => $this->anotherRestaurant->id
        ]));
        $response->assertStatus(200);
        $response->assertJsonPath("status", 200);
    }
}