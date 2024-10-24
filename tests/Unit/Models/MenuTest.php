<?php

namespace Tests\Unit\Models;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use \Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;
    protected Menu|Collection $menus;
    protected \App\Models\Restaurant $restaurant;
    protected \App\Models\Dish|Collection $dishes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->menus = Menu::factory(10)->create();
        $this->restaurant = $this->menus[0]->restaurant;
        $this->dishes = Dish::factory(5)->create([
            'restaurant_id' => $this->restaurant->id
        ]);
    }

    public function test_relationship_menus_with_restaurant()
    {
        $this->assertInstanceOf(Restaurant::class, $this->restaurant);
    }

    public function test_relationship_menus_with_dishes()
    {
        $this->menus->first()->dishes()->attach($this->dishes);

        $this->assertInstanceOf(Collection::class, $this->dishes);
        $this->assertInstanceOf(\App\Models\Dish::class, $this->dishes->first());
    }

}
