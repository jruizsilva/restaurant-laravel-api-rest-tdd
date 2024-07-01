<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditRestaurantTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }
    #[Test]
    public function an_user_can_update_their_restaurant(): void
    {
        $data = [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs(User::find(1))->putJson('api/v1/restaurants/1', $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ]);
        $this->assertDatabaseHas('restaurants', [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ]);
    }

    #[Test]
    public function slug_is_generated_automatically(): void
    {
        $data = [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs(User::find(1))->putJson('api/v1/restaurants/1', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure(['slug']);
    }

    #[Test]
    public function an_user_cannot_update_other_users_restaurant(): void
    {
        $data = [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs(User::find(1))->putJson('api/v1/restaurants/11', $data);
        $response->assertStatus(401);
        $this->assertDatabaseMissing('restaurants', [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ]);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_update_a_restaurant(): void
    {
        $data = [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ];
        $response = $this->putJson('api/v1/restaurants/1', $data);
        $this->assertDatabaseMissing('restaurants', [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ]);
        $response->assertStatus(401);
    }

    #[Test]
    public function name_is_required(): void
    {
        $data = [
            'name' => '',
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs(User::find(1))->putJson('api/v1/restaurants/1', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function description_is_required(): void
    {
        $data = [
            'name' => 'New Restaurant Name',
            'description' => '',
        ];
        $response = $this->actingAs(User::find(1))->putJson('api/v1/restaurants/1', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('description');
    }

    #[Test]
    public function name_must_have_at_least_4_characters(): void
    {
        $data = [
            'name' => 'abc',
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs(User::find(1))->putJson('api/v1/restaurants/1', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function description_must_have_at_least_4_characters(): void
    {
        $data = [
            'name' => 'New Restaurant Name',
            'description' => 'abc',
        ];
        $response = $this->actingAs(User::find(1))->putJson('api/v1/restaurants/1', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('description');
    }

    #[Test]
    public function name_must_have_at_most_100_characters(): void
    {
        $data = [
            'name' => str_repeat('a', 100),
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs(User::find(1))->putJson('api/v1/restaurants/1', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
}