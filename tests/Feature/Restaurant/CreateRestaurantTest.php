<?php

namespace Tests\Feature\Restaurant;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateRestaurantTest extends TestCase
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
    }

    #[Test]
    public function a_user_can_create_a_restaurant(): void
    {
        $data = [
            'name' => 'Test Restaurant',
            'description' => 'This is a test restaurant',
        ];

        $response = $this->actingAs($this->user)->postJson(route('restaurants.store'), $data);
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

    #[Test]
    public function field_name_is_required()
    {
        $data = [
            'description' => 'This is a test restaurant',
        ];
        $response = $this->actingAs($this->user)->postJson(route('restaurants.store'), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function name_must_be_at_least_4_characters()
    {
        $data = [
            'name' => 'Tes',
            'description' => 'This is a test restaurant',
        ];

        $response = $this->actingAs($this->user)->postJson(route('restaurants.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function field_description_is_required()
    {
        $data = [
            'name' => 'Test name',
        ];
        $response = $this->actingAs($this->user)->postJson(route('restaurants.store'), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('description');
    }

    #[Test]
    public function description_must_be_at_least_4_characters()
    {
        $data = [
            'name' => 'Test name',
            'description' => 'Th3',
        ];

        $response = $this->actingAs($this->user)->postJson(route('restaurants.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('description');
    }
}