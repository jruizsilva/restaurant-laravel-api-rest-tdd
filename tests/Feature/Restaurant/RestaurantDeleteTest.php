<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestaurantDeleteTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $anotherUser;
    protected $restaurant;
    protected $anotherRestaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Example',
            'last_name' => 'Example',
            'email' => 'example@example.com',
        ]);
        $this->anotherUser = User::factory()->create([
            'name' => 'Another',
            'last_name' => 'User',
            'email' => 'another@example.com',
        ]);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => "Restaurant 1",
        ]);
        $this->anotherRestaurant = Restaurant::factory()->create([
            'user_id' => $this->anotherUser->id,
            'name' => "Restaurant 2",
        ]);
    }
    #[Test]
    public function a_user_can_delete_their_restaurant(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route("restaurants.destroy", [
            'restaurant' => $this->restaurant->id,
        ]));
        $response->assertStatus(200);
        $response->assertJsonPath('status', 200);
        $this->assertDatabaseMissing('restaurants', [
            'id' => 1,
        ]);
    }

    #[Test]
    public function a_user_cannot_delete_another_users_restaurant(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route("restaurants.destroy", [
            'restaurant' => $this->anotherRestaurant->id,
        ]));
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
        $this->assertDatabaseHas('restaurants', [
            'id' => $this->anotherRestaurant->id,
        ]);
    }
}