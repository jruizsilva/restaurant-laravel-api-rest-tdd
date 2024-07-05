<?php

namespace Tests\Feature\Menu;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListMenuTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $anotherUser;
    protected $restaurant;
    protected $anotherRestaurant;
    protected $plates;
    protected $anotherPlates;
    protected $menus;
    protected $anotherMenu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->anotherRestaurant = Restaurant::factory()->create(['user_id' => $this->anotherUser->id]);
        $this->menus = Menu::factory(5)
            ->hasAttached(Plate::factory()->count(5)->create(['restaurant_id' => $this->restaurant->id]))->create(['restaurant_id' => $this->restaurant->id]);
        $this->anotherMenu = Menu::factory(2)
            ->hasAttached(Plate::factory()->count(5)->create(['restaurant_id' => $this->restaurant->id]))->create(['restaurant_id' => $this->anotherRestaurant->id]);
    }

    #[Test]
    public function an_authenticated_user_can_see_the_menu_list_of_his_restaurant(): void
    {
        $response = $this->actingAs($this->user)->get(route('restaurant.menus.index', [
            'restaurant' => $this->restaurant->id
        ]));
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.data');
        $response->assertJsonPath('status', 200);
    }
}