<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchRestaurantTest extends TestCase
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
                'name' => "Restaurant name $i",
                'description' => "Restaurant description $i",
            ]);
        }
    }

    #[Test]
    public function should_filter_the_results_that_matches_with_the_search_parameter(): void
    {
        $search = "Restaurant name 15";
        $response = $this->actingAs($this->user)->get(route("restaurants.index", [
            'search' => $search
        ]));

        $response->assertJsonPath('data.data.0.name', $search);
        $response->assertStatus(200);
        $response->assertJsonPath("status", 200);
    }
}