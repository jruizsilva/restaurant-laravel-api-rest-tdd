<?php

namespace Tests\Feature\Plate;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditPlateTest extends TestCase
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
    public function an_authenticated_user_can_edit_a_plate_that_belongs_to_his_restaurant(): void
    {
        $data = [
            'name' => 'Plate 1 edited',
            'description' => 'Description 1 edited',
            'price' => 11.99
        ];
        $response = $this->actingAs($this->user)->putJson(
            route('restaurant.plates.update', [
                'restaurant' => $this->restaurant->id,
                'plate' => $this->plate->id
            ]),
            $data
        );
        $responseData = $response->json('data');

        $response->assertStatus(200);
        $response->assertJsonPath('data', [
            ...$responseData,
            ...$data
        ]);
        $this->assertDatabaseHas('plates', $data);
    }

    #[Test]
    public function an_authenticated_user_cannot_edit_a_plate_that_does_not_belong_to_his_restaurant(): void
    {
        $data = [
            'name' => 'Plate 2 edited',
            'description' => 'Description 2 edited',
            'price' => 13.99
        ];
        $response = $this->actingAs($this->user)->putJson(
            route('restaurant.plates.update', [
                'restaurant' => $this->anotherRestaurant->id,
                'plate' => $this->anotherPlate->id
            ]),
            $data
        );
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_edit_a_plate(): void
    {
        $data = [
            'name' => 'Plate 1 edited',
            'description' => 'Description 1 edited',
            'price' => 11.99
        ];
        $response = $this->putJson(
            route('restaurant.plates.update', [
                'restaurant' => $this->restaurant->id,
                'plate' => $this->plate->id
            ]),
            $data
        );
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
    }

    #[Test]
    public function an_authenticated_user_cannot_edit_a_plate_that_does_not_exist(): void
    {
        $data = [
            'name' => 'Plate 1 edited',
            'description' => 'Description 1 edited',
            'price' => 11.99
        ];
        $response = $this->actingAs($this->user)->putJson(
            route('restaurant.plates.update', [
                'restaurant' => $this->restaurant->id,
                'plate' => 999
            ]),
            $data
        );
        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }

    #[Test]
    public function an_authenticated_user_cannot_edit_a_plate_of_a_restaurant_that_does_not_exist()
    {
        $data = [
            'name' => 'Plate 1 edited',
            'description' => 'Description 1 edited',
            'price' => 11.99
        ];
        $response = $this->actingAs($this->user)->putJson(
            route('restaurant.plates.update', [
                'restaurant' => 999,
                'plate' => $this->plate->id
            ]),
            $data
        );
        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }

    #[Test]
    public function an_authenticated_user_cannot_edit_a_plate_that_does_not_belongs_to_his_restaurant()
    {
        $data = [
            'name' => 'Plate 1 edited',
            'description' => 'Description 1 edited',
            'price' => 11.99
        ];
        $response = $this->actingAs($this->user)->putJson(
            route('restaurant.plates.update', [
                'restaurant' => $this->restaurant->id,
                'plate' => $this->anotherPlate->id
            ]),
            $data
        );
        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }
}