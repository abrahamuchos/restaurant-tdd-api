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
        $this->dishes = Dish::factory()->count(100)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->menu = Menu::factory(150)
            ->hasAttached($this->dishes->random(10))
            ->create([
                'restaurant_id' => $this->restaurant->id,
            ]);

    }
}
