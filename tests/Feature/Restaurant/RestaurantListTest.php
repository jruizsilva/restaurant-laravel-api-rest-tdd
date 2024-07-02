<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
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
        for ($i = 10; $i <= 160; $i++) {
            $name = 'Restaurant ' . $i;
            $slug = str($name)->slug() . "-" . uniqid();
            Restaurant::factory()->create([
                'user_id' => 2,
                'name' => $name,
                'slug' => $slug
            ]);
        }
    }

    #[Test]
    public function a_user_can_see_their_restaurants(): void
    {
        $response = $this->actingAs(User::find(2))->get(route("restaurants.index"));
        $responseData = $response->json('data.data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['data' => [['name', 'description']]]
        ]);
        $response->assertJsonCount(15, 'data.data');
        foreach ($responseData as $restaurant) {
            $this->assertEquals(2, $restaurant['user_id']);
        }
    }

    #[Test]
    public function a_guest_cannot_see_restaurants(): void
    {
        $response = $this->getJson(route("restaurants.index"));
        $response->assertStatus(401);
    }


}