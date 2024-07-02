<?php

namespace Tests\Feature\Plate;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlateShowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $anotherUser;
    protected $restaurant;
    protected $anotherRestaurant;
    protected $plate;
    protected $anotherPlate;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => $this->user->id
        ]);
        $this->anotherRestaurant = Restaurant::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $this->plate = $this->restaurant->plates()->create([
            'name' => 'Plate 1',
            'description' => 'Description 1',
            'price' => 10.99
        ]);
        $this->anotherPlate = $this->anotherRestaurant->plates()->create([
            'name' => 'Plate 2',
            'description' => 'Description 2',
            'price' => 12.99
        ]);
    }

    #[Test]
    public function an_authenticated_user_can_see_the_plates_of_his_restaurant(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('restaurant.plates.show', [
            'restaurant' => $this->restaurant->id,
            'plate' => $this->plate->id
        ]));
        $responseData = $response->json('data');

        $response->assertJsonPath('data', [
            ...$responseData,
            'name' => 'Plate 1',
            'description' => 'Description 1',
            'price' => 10.99
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', 200);
    }

    #[Test]
    public function an_authenticated_user_cannot_see_the_plates_of_a_restaurant_that_does_not_belogs_to_him(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('restaurant.plates.show', [
            'restaurant' => $this->anotherRestaurant->id,
            'plate' => $this->plate->id
        ]));
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_see_the_plates_of_a_restaurant(): void
    {
        $response = $this->getJson(route('restaurant.plates.show', [
            'restaurant' => $this->restaurant->id,
            'plate' => $this->plate->id
        ]));
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
    }

    #[Test]
    public function an_authenticated_user_cannot_see_a_plate_that_does_not_belogs_to_his_restaurant(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('restaurant.plates.show', [
            'restaurant' => $this->restaurant->id,
            'plate' => $this->anotherPlate->id
        ]));
        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }
}