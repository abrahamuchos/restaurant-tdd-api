<?php

namespace Tests\Feature\Menu;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ShowPublicMenuTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected Dish|Collection $dishes;
    protected Menu|Collection $menu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::factory()->create();
        $this->dishes = Dish::factory()->count(15)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->menu = Menu::factory()
            ->hasAttached($this->dishes)
            ->create([
                'restaurant_id' => $this->restaurant->id,
            ]);

    }

    public function test_public_menu_is_returned_with_dishes()
    {
        $response = $this->getJson(route('public.menus.show', $this->menu->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'name',
                'description',
            ]
        ]);
        $response->assertJsonPath('data.name', $this->menu->name);
    }
}
