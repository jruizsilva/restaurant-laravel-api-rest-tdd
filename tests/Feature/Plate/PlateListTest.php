<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlateListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
        for ($i = 1; $i <= 150; $i++) {
            $name = 'Plate ' . $i;
            Plate::factory()->create([
                'restaurant_id' => 2,
                'name' => $name,
            ]);
        }
    }

    #[Test]
    public function a_user_can_see_the_plates_of_their_restaurant(): void
    {
        $response = $this->actingAs(User::find(2))->get(route("restaurant.plates.index", ['restaurant' => 2]));
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
            $this->assertEquals(2, $plate['restaurant_id']);
        }
    }

    #[Test]
    public function a_guest_cannot_see_plates_of_some_restaurant(): void
    {
        $response = $this->getJson(route("restaurant.plates.index", ['restaurant' => 2]));
        $response->assertStatus(401);
    }

    #[Test]
    public function a_user_cannot_see_plates_of_a_restaurant_that_does_not_belong_to_him(): void
    {
        $response = $this->actingAs(User::find(1))->getJson(route("restaurant.plates.index", ['restaurant' => 11]));
        $response->assertStatus(401);
    }
}