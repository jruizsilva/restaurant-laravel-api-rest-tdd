<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowRestaurantTest extends TestCase
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
    public function a_user_can_see_their_restaurant(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('restaurants.show', [
            'restaurant' => $this->restaurant->id,
        ]));
        $response->assertStatus(200);
        $response->assertJsonPath('status', 200);
    }

    #[Test]
    public function an_authenticated_user_cannot_see_a_restaurant_that_does_not_belongs_to_him(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('restaurants.show', [
            'restaurant' => $this->anotherRestaurant->id,
        ]));
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
    }

    #[Test]
    public function a_guest_cannot_see_a_restaurant(): void
    {
        $response = $this->getJson(route('restaurants.show', [
            'restaurant' => $this->restaurant->id,
        ]));
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
    }

    #[Test]
    public function throw_an_exception_if_the_restaurant_does_not_exist(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('restaurants.show', [
            'restaurant' => 9999,
        ]));
        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }

}