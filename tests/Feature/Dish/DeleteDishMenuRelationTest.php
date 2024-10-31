<?php

namespace Tests\Feature\Dish;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteDishMenuRelationTest extends TestCase
{
    use RefreshDatabase;

    protected Dish $dish;
    protected Menu $menu;
    protected \App\Models\User $user;
    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        $this->menu = Menu::factory()->create(['restaurant_id' => $this->restaurant->id]);
        $this->dish = Dish::factory()
            ->hasAttached($this->menu)
            ->create(['restaurant_id' => $this->restaurant->id]);
    }

    public function test_authenticated_user_can_delete_their_dish_with_menu_relation()
    {
        $response = $this->apiAs(
            $this->user,
            'delete',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}"
        );

        $response->assertStatus(204);
        $this->assertDatabaseMissing('dishes', ['id' => $this->dish->id]);
        $this->assertDatabaseMissing('dish_menu', [
            'dish_id' => $this->dish->id,
            'menu_id' => $this->menu->id,
        ]);
    }

    public function test_authenticated_user_can_delete_their_dish_with_menu_relation_cannot_delete_menu()
    {
        $response = $this->apiAs(
            $this->user,
            'delete',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}"
        );

        $response->assertStatus(204);
        $this->assertDatabaseMissing('dishes', ['id' => $this->dish->id]);
        $this->assertDatabaseHas('menus', ['id' => $this->menu->id]);
        $this->assertDatabaseMissing('dish_menu', [
            'dish_id' => $this->dish->id,
            'menu_id' => $this->menu->id,
        ]);
    }
}
