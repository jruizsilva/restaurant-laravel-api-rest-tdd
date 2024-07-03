<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteMenuTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $anotherUser;
    protected $restaurant;
    protected $anotherRestaurant;
    protected $plates;
    protected $anotherPlates;
    protected $menu;
    protected $anotherMenu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->anotherRestaurant = Restaurant::factory()->create(['user_id' => $this->anotherUser->id]);
        $this->plates = Plate::factory()->count(3)->create(['restaurant_id' => $this->restaurant->id]);
        $this->anotherPlates = Plate::factory()->count(3)->create(['restaurant_id' => $this->anotherRestaurant->id]);
        $this->menu = Menu::factory()->hasAttached($this->plates)->create(['restaurant_id' => $this->restaurant->id]);
        $this->anotherMenu = Menu::factory()->hasAttached($this->anotherPlates)->create(['restaurant_id' => $this->anotherRestaurant->id]);
    }

    #[Test]
    public function an_authenticated_user_can_delete_a_menu_of_his_restaurant(): void
    {
        $this->withExceptionHandling();
        $response = $this->actingAs($this->user)->deleteJson(route('restaurant.menus.destroy', [
            'restaurant' => $this->restaurant->id,
            'menu' => $this->menu->id
        ]));

        $response->assertStatus(200);
        $response->assertJsonPath("status", 200);
        $this->assertDatabaseMissing('menus', [
            'id' => $this->menu->id
        ]);
        $this->assertDatabaseMissing('menu_plate', [
            'menu_id' => $this->menu->id
        ]);
    }
}