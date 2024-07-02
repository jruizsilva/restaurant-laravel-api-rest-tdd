<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestaurantListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }

    #[Test]
    public function a_user_can_see_their_restaurants(): void
    {
        $response = $this->actingAs(User::find(1))->get(route("restaurants.index"));
        $responseData = $response->json('data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [['name', 'description']]
        ]);
        $response->assertJsonCount(10, 'data');
        foreach ($responseData as $restaurant) {
            $this->assertEquals(1, $restaurant['user_id']);
        }
    }

    #[Test]
    public function a_guest_cannot_see_restaurants(): void
    {
        $response = $this->getJson(route("restaurants.index"));
        $response->assertStatus(401);
    }


}