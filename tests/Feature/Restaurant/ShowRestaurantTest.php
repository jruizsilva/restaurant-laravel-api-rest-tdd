<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }
    #[Test]
    public function a_user_can_see_their_restaurant(): void
    {
        $response = $this->actingAs(User::find(1))->getJson("api/v1/restaurants/1");
        $response->assertStatus(200);
        $response->assertJsonPath('status', 200);
    }

    #[Test]
    public function a_user_cannot_see_others_restaurant(): void
    {
        $response = $this->actingAs(User::find(1))->getJson("api/v1/restaurants/11");
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
    }

    #[Test]
    public function a_guest_cannot_see_a_restaurant(): void
    {
        $response = $this->getJson("api/v1/restaurants/1");
        $response->assertStatus(401);
        $response->assertJsonPath('status', 401);
    }

    #[Test]
    public function throw_an_exception_if_the_restaurant_does_not_exist(): void
    {
        $response = $this->actingAs(User::find(1))->getJson("api/v1/restaurants/100");
        $response->assertStatus(404);
        $response->assertJsonPath('status', 404);
    }

}