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

class ShowMenuTest extends TestCase
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
    public function an_authenticated_user_can_see_a_menu_of_his_restaurant(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('restaurant.menus.show', [
            'restaurant' => $this->restaurant->id,
            'menu' => $this->menu->id,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'plates' => [
                    [
                        'id',
                        'name',
                        'description',
                        'price',
                    ],
                ],
            ]
        ]);
    }
}