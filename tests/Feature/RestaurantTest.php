<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    #[Test]
    public function a_user_can_create_a_restaurant(): void
    {
        $data = [
            'name' => 'Test Restaurant',
            'description' => 'This is a test restaurant',
        ];

        $response = $this->apiAs(User::find(1), 'post', 'api/v1/restaurants', $data);
        $responseData = $response->json('data');

        $response->assertStatus(201);
        $response->assertJsonFragment([
            ...$responseData,
            'name' => 'Test Restaurant',
            'description' => 'This is a test restaurant',
        ]);
        $this->assertDatabaseHas('restaurants', [
            'name' => 'Test Restaurant',
            'description' => 'This is a test restaurant',
        ]);
        $this->assertDatabaseCount('restaurants', 1);
        $this->assertStringContainsString('test-restaurant', $responseData['slug']);
    }
}