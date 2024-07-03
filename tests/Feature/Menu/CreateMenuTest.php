<?php

namespace Tests\Feature\Menu;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateMenuTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $anotherUser;
    protected $restaurant;
    protected $anotherRestaurant;
    protected $plates;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->anotherRestaurant = Restaurant::factory()->create(['user_id' => $this->anotherUser->id]);
        $this->plates = $this->restaurant->plates()->createMany([
            ['name' => 'Plate 1', 'description' => 'description 1', 'price' => 10.99],
            ['name' => 'Plate 2', 'description' => 'description 2', 'price' => 12.99],
            ['name' => 'Plate 3', 'description' => 'description 3', 'price' => 8.99],
        ]);
    }

    #[Test]
    public function an_authenticated_user_can_create_a_menu_with_some_plates(): void
    {
        $data = [
            'name' => 'Menu 1',
            'description' => 'Description 1',
            'plates' => $this->plates->pluck('id')->toArray(),
        ];
        $response = $this->actingAs($this->user)
            ->postJson(route('restaurant.menus.store', [
                'restaurant' => $this->restaurant->id
            ]), $data);

        $response->assertStatus(201);
        $response->assertJsonPath("data.restaurant.id", $this->restaurant->id);
        $response->assertJsonPath("status", 201);
        $response->assertJsonCount(3, 'data.plates');
        $this->assertDatabaseHas('menus', [
            'name' => 'Menu 1',
            'description' => 'Description 1',
            'restaurant_id' => $this->restaurant->id,
        ]);
        foreach ($this->plates as $plate) {
            $this->assertDatabaseHas('menu_plate', [
                'menu_id' => 1,
                'plate_id' => $plate->id
            ]);
        }
    }
}