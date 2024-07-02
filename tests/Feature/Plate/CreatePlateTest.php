<?php

namespace Tests\Feature\Plate;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreatePlateTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $anotherUser;
    protected $restaurant;
    protected $anotherRestaurant;

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
    }

    #[Test]
    public function an_authenticated_user_can_add_a_plate_to_his_restaurant(): void
    {
        $data = [
            'name' => 'Plate 1',
            'description' => 'Plate 1 description',
            'price' => 10.99,
        ];
        $response = $this->actingAs($this->user)
            ->postJson(route('restaurant.plates.store', ['restaurant' => $this->restaurant->id]), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('plates', $data);
    }

    #[Test]
    public function an_authenticated_user_cannot_add_a_plate_to_a_restaurant_that_does_not_belongs_to_him(): void
    {
        $data = [
            'name' => 'Plate 1',
            'description' => 'Plate 1 description',
            'price' => 10.99,
        ];
        $response = $this->actingAs($this->user)
            ->postJson(route('restaurant.plates.store', ['restaurant' => $this->anotherRestaurant->id]), $data);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('plates', $data);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_add_a_plate_to_a_restaurant(): void
    {
        $data = [
            'name' => 'Plate 1',
            'description' => 'Plate 1 description',
            'price' => 10.99,
            'restaurant_id' => $this->restaurant->id
        ];
        $response = $this->postJson(route('restaurant.plates.store', ['restaurant' => $this->restaurant->id]), $data);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('plates', $data);
    }

    #[Test]
    public function a_plate_requires_a_name(): void
    {
        $data = [
            'description' => 'Plate 1 description',
            'price' => 10.99,
            'restaurant_id' => $this->restaurant->id,
            'name' => ''
        ];
        $response = $this->actingAs($this->user)
            ->postJson(route('restaurant.plates.store', ['restaurant' => $this->restaurant->id]), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
    #[Test]
    public function a_plate_requires_a_description(): void
    {
        $data = [
            'name' => 'Plate 1',
            'price' => 10.99,
            'restaurant_id' => $this->restaurant->id,
            'description' => ''
        ];
        $response = $this->actingAs($this->user)
            ->postJson(route('restaurant.plates.store', ['restaurant' => $this->restaurant->id]), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('description');
    }

    #[Test]
    public function a_plate_requires_a_price(): void
    {
        $data = [
            'name' => 'Plate 1',
            'description' => 'Plate 1 description',
            'restaurant_id' => $this->restaurant->id,
            'price' => ''
        ];
        $response = $this->actingAs($this->user)
            ->postJson(route('restaurant.plates.store', ['restaurant' => $this->restaurant->id]), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('price');
    }
}