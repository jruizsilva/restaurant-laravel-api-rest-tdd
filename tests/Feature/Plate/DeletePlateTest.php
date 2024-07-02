<?php

namespace Tests\Feature\Plate;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeletePlateTest extends TestCase
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
    public function an_authenticated_user_can_delete_a_plate_that_belongs_to_his_restaurant(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('restaurant.plates.destroy', [
                'restaurant' => $this->restaurant->id,
                'plate' => $this->plate->id
            ]));

        $response->assertStatus(200);
        $response->assertJsonPath('status', 200);
        $this->assertDatabaseMissing('plates', [
            'id' => $this->plate->id
        ]);
    }

    #[Test]
    public function an_authenticated_user_cannot_delete_a_plate_that_does_not_belongs_to_his_restaurant(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('restaurant.plates.destroy', [
                'restaurant' => $this->anotherRestaurant->id,
                'plate' => $this->anotherPlate->id
            ]));

        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
        $this->assertDatabaseHas('plates', [
            'id' => $this->anotherPlate->id
        ]);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_delete_a_plate(): void
    {
        $response = $this->deleteJson(route('restaurant.plates.destroy', [
            'restaurant' => $this->restaurant->id,
            'plate' => $this->plate->id
        ]));

        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
        $this->assertDatabaseHas('plates', [
            'id' => $this->plate->id
        ]);
    }

    #[Test]
    public function an_authenticated_user_cannot_delete_a_plate_that_does_not_exist(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('restaurant.plates.destroy', [
                'restaurant' => $this->restaurant->id,
                'plate' => 999
            ]));

        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }

    #[Test]
    public function an_authenticated_user_cannot_delete_a_plate_of_a_restaurant_that_does_not_exist()
    {
        $response = $this->actingAs($this->user)->deleteJson(
            route('restaurant.plates.update', [
                'restaurant' => 999,
                'plate' => $this->plate->id
            ]),
        );
        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }

    #[Test]
    public function an_authenticated_user_cannot_delete_a_plate_that_does_not_exist_in_his_restaurant(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('restaurant.plates.destroy', [
                'restaurant' => $this->restaurant->id,
                'plate' => $this->anotherPlate->id
            ]));

        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }
}