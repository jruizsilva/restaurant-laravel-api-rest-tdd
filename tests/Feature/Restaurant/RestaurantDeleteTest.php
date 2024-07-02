<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestaurantDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }
    #[Test]
    public function a_user_can_delete_their_restaurant(): void
    {
        $response = $this->actingAs(User::find(1))->deleteJson('api/v1/restaurants/1');
        $response->assertNoContent();
        $this->assertDatabaseMissing('restaurants', [
            'id' => 1,
        ]);
    }

    #[Test]
    public function a_user_cannot_delete_another_users_restaurant(): void
    {
        $response = $this->actingAs(User::find(1))->deleteJson('api/v1/restaurants/11');
        $response->assertUnauthorized();
        $this->assertDatabaseHas('restaurants', [
            'id' => 11,
        ]);
    }
}