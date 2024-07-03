<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlateListTest extends TestCase
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
        for ($i = 1; $i <= 150; $i++) {
            $name = 'Plate ' . $i;
            Plate::factory()->create([
                'restaurant_id' => $this->restaurant->id,
                'name' => $name,
            ]);
        }
        for ($i = 150; $i <= 300; $i++) {
            $name = 'Plate ' . $i;
            Plate::factory()->create([
                'restaurant_id' => $this->anotherRestaurant->id,
                'name' => $name,
            ]);
        }

    }

    #[Test]
    public function an_authenticated_user_can_see_the_plates_of_their_restaurant(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route("restaurant.plates.index", [
                'restaurant' => $this->restaurant->id
            ]));
        $responseData = $response->json('data.data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => ['name', 'description']
                ]
            ],
        ]);
        $response->assertJsonPath('status', 200);
        $response->assertJsonCount(15, 'data.data');
        foreach ($responseData as $plate) {
            $this->assertEquals(1, $plate['restaurant_id']);
        }
    }

    #[Test]
    public function an_unauthenticated_user_cannot_see_plates_of_some_restaurant(): void
    {
        $response = $this->getJson(route("restaurant.plates.index", [
            'restaurant' => $this->restaurant->id
        ]));
        $response->assertStatus(401);
    }

    #[Test]
    public function a_user_cannot_see_plates_of_a_restaurant_that_does_not_belong_to_him(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route("restaurant.plates.index", [
                'restaurant' => $this->anotherRestaurant->id
            ]));
        $response->assertStatus(401);
    }
}