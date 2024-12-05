<?php

namespace Tests\Unit\Models;

use App\Models\Restaurant;
use PHPUnit\Framework\TestCase;

class IsUsingHasSortTraitTest extends TestCase
{
   public function test_restaurant_model_is_using_has_sort_trait()
   {
       $restaurant = new Restaurant();

       $this->assertTrue(method_exists($restaurant, 'scopeSort'));
   }

    public function test_dish_model_is_using_has_sort_trait()
    {
        $dish = new Restaurant();

        $this->assertTrue(method_exists($dish, 'scopeSort'));
    }

    public function test_menu_model_is_using_has_sort_trait()
    {
        $menu = new Restaurant();

        $this->assertTrue(method_exists($menu, 'scopeSort'));
    }

}
