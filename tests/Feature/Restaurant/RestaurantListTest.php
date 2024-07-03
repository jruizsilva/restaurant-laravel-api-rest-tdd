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
    protected $user;

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
        for ($i = 1; $i <= 150; $i++) {
            Restaurant::factory()->create([
                'user_id' => $this->user->id,
                'name' => "Restaurant $i",
            ]);
        }
    }

    #[Test]
    public function a_user_can_see_their_restaurants(): void
    {
        $response = $this->actingAs($this->user)->get(route("restaurants.index"));
        $responseData = $response->json('data.data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['data' => [['name', 'description']]]
        ]);
        $response->assertJsonCount(15, 'data.data');
        foreach ($responseData as $restaurant) {
            $this->assertEquals($this->user->id, $restaurant['user_id']);
        }
    }

    #[Test]
    public function a_guest_cannot_see_restaurants(): void
    {
        $response = $this->getJson(route("restaurants.index"));
        $response->assertStatus(401);
    }


}