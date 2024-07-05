<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SortTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $restaurantA;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurantD = Restaurant::factory()->create([
            "user_id" => $this->user->id,
            "name" => "Restaurant D",
            "description" => "Description for Restaurant D"
        ]);
        $this->restaurantB = Restaurant::factory()->create([
            "user_id" => $this->user->id,
            "name" => "Restaurant B",
            "description" => "Description for Restaurant B"
        ]);
        $this->restaurantC = Restaurant::factory()->create([
            "user_id" => $this->user->id,
            "name" => "Restaurant C",
            "description" => "Description for Restaurant C"
        ]);
        $this->restaurantA = Restaurant::factory()->create([
            "user_id" => $this->user->id,
            "name" => "Restaurant A",
            "description" => "Description for Restaurant A"
        ]);

    }
    #[Test]
    public function it_sorts_by_name_asc(): void
    {
        $response = $this->actingAs($this->user)->get(route("restaurants.index", [
            "sortBy" => "name",
            "sortDirection" => "asc"
        ]));
        $response->assertJsonPath("data.data.0.name", "Restaurant A");
        $response->assertJsonPath("data.data.1.name", "Restaurant B");
        $response->assertJsonPath("data.data.2.name", "Restaurant C");
        $response->assertJsonPath("data.data.3.name", "Restaurant D");
        $response->assertStatus(200);
        $response->assertJsonPath("status", 200);
    }
    #[Test]
    public function it_sorts_by_name_desc(): void
    {
        $response = $this->actingAs($this->user)->get(route("restaurants.index", [
            "sortBy" => "name",
            "sortDirection" => "desc"
        ]));
        $response->assertJsonPath("data.data.0.name", "Restaurant D");
        $response->assertJsonPath("data.data.1.name", "Restaurant C");
        $response->assertJsonPath("data.data.2.name", "Restaurant B");
        $response->assertJsonPath("data.data.3.name", "Restaurant A");
        $response->assertStatus(200);
        $response->assertJsonPath("status", 200);
    }
    #[Test]
    public function it_sorts_by_description_asc(): void
    {
        $response = $this->actingAs($this->user)->get(route("restaurants.index", [
            "sortBy" => "description",
            "sortDirection" => "asc"
        ]));
        $response->assertJsonPath("data.data.0.description", "Description for Restaurant A");
        $response->assertJsonPath("data.data.1.description", "Description for Restaurant B");
        $response->assertJsonPath("data.data.2.description", "Description for Restaurant C");
        $response->assertJsonPath("data.data.3.description", "Description for Restaurant D");
        $response->assertStatus(200);
        $response->assertJsonPath("status", 200);
    }
    #[Test]
    public function it_sorts_by_description_desc(): void
    {
        $response = $this->actingAs($this->user)->get(route("restaurants.index", [
            "sortBy" => "description",
            "sortDirection" => "desc"
        ]));
        $response->assertJsonPath("data.data.0.description", "Description for Restaurant D");
        $response->assertJsonPath("data.data.1.description", "Description for Restaurant C");
        $response->assertJsonPath("data.data.2.description", "Description for Restaurant B");
        $response->assertJsonPath("data.data.3.description", "Description for Restaurant A");
        $response->assertStatus(200);
        $response->assertJsonPath("status", 200);
    }
}