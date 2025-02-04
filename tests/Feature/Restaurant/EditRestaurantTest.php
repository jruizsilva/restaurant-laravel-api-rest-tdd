<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $anotherUser;
    protected $restaurant;
    protected $anotherRestaurant;

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
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => "Restaurant 1",
        ]);
        $this->anotherRestaurant = Restaurant::factory()->create([
            'user_id' => $this->anotherUser->id,
            'name' => "Restaurant 2",
        ]);
    }
    #[Test]
    public function an_user_can_update_their_restaurant(): void
    {
        $data = [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
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
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
        $responseData = $response->json('data');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['slug']]);
        $this->assertStringContainsString('new-restaurant-name', $responseData['slug']);
    }

    #[Test]
    public function slug_must_not_change_if_name_is_the_same_name(): void
    {
        $data = [
            'name' => 'Restaurant 1',
            'description' => 'New Restaurant Description',
        ];
        $restaurant = Restaurant::find(1);
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
        $responseData = $response->json('data');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['slug']]);
        $this->assertTrue($restaurant->slug === $responseData['slug']);
    }

    #[Test]
    public function an_user_cannot_update_other_users_restaurant(): void
    {
        $data = [
            'name' => 'New Restaurant Name',
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->anotherRestaurant->id
        ]), $data);
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
        $response = $this->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
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
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
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
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
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
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
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
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('description');
    }

    #[Test]
    public function name_must_have_at_most_100_characters(): void
    {
        $data = [
            'name' => str_repeat('a', 101),
            'description' => 'New Restaurant Description',
        ];
        $response = $this->actingAs($this->user)->putJson(route('restaurants.update', [
            'restaurant' => $this->restaurant->id
        ]), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
}