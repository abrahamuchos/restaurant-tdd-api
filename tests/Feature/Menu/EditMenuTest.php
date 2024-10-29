<?php

namespace Tests\Feature\Menu;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

class EditMenuTest extends TestCase
{
    use RefreshDatabase;

    protected \App\Models\User $user;
    protected Restaurant $restaurant;
    protected Dish|Collection $dishes, $newDishes;
    protected Menu $menu;
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        $this->dishes = Dish::factory(10)->create([
            'restaurant_id' => $this->restaurant->id
        ]);
        $this->menu = Menu::factory()
            ->hasAttached($this->dishes)
            ->create([
                'restaurant_id' => $this->restaurant->id
            ]);
        $this->newDishes = Dish::factory(5)->create([
            'restaurant_id' => $this->restaurant->id
        ]);
        $this->data = [
            'name' => 'Menu 1 to Edit',
            'description' => 'Menu 1 description edited',
            'restaurantId' => $this->restaurant->id,
            'dishes' => $this->newDishes->pluck('id')->toArray(),
        ];
    }

    public function test_unauthenticated_user_cannot_edit_menu()
    {
        $response = $this->putJson(
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(401);
    }

    public function test_authenticated_user_put_menu_with_valid_data()
    {
        $response = $this->apiAs(
            $this->user,
            'put',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'restaurantId',
                'dishes' => [
                    '*' => ['id', 'name', 'description', 'price', 'restaurantId']
                ]
            ]
        ]);
        $response->assertJsonPath('data.name', $this->data['name']);
        $this->assertDatabaseHas('menus', [
            'id' => $this->menu->id,
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'restaurant_id' => $this->data['restaurantId'],
        ]);
    }

    public function test_authenticated_user_patch_menu_with_valid_data()
    {
        $menuName = ['name' => 'Hello'];

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $menuName
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'restaurantId',
                'dishes' => [
                    '*' => ['id', 'name', 'description', 'price', 'restaurantId']
                ]
            ]
        ]);
        $this->assertDatabaseHas('menus', [
            'id' => $this->menu->id,
            'name' => $menuName['name'],
            'description' => $this->menu->description,
            'restaurant_id' => $this->menu->restaurant_id,
        ]);
    }


    public function test_authenticated_user_can_edit_dishes_of_menu()
    {
        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'restaurantId',
                'dishes' => [
                    '*' => ['id', 'name', 'description', 'price', 'restaurantId']
                ]
            ]
        ]);
        $response->assertJsonCount($this->newDishes->count(), 'data.dishes');
        foreach ($this->newDishes as $dish) {
            $this->assertDatabaseHas('dish_menu', [
                'dish_id' => $dish->id,
                'menu_id' => $this->menu->id,
            ]);
        }

    }

    public function test_authenticated_user_can_edit_menu_without_edit_dishes()
    {
        unset($this->data['dishes']);

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'restaurantId',
                'dishes' => [
                    '*' => ['id', 'name', 'description', 'price', 'restaurantId']
                ]
            ]
        ]);
        $response->assertJsonCount($this->dishes->count(), 'data.dishes');
        foreach ($this->dishes as $dish) {
            $this->assertDatabaseHas('dish_menu', [
                'dish_id' => $dish->id,
                'menu_id' => $this->menu->id,
            ]);
        }
    }

    public function test_authenticated_user_can_edit_menu_remove_dishes()
    {
        $this->data['dishes'] = [];

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'restaurantId',
                'dishes' => [
                    '*' => ['id', 'name', 'description', 'price', 'restaurantId']
                ]
            ]
        ]);
        $response->assertJsonCount(0, 'data.dishes');
    }

    public function test_menu_plates_should_not_be_duplicates()
    {
        $this->data['dishes'] = [$this->newDishes[0]->id, $this->newDishes[0]->id];

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(200);
        $this->assertDatabaseCount('dish_menu', 1);
    }

    public function test_name_must_be_string()
    {
        $this->data['name'] = 1234;

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
    }

    public function test_name_max_length_is_65_characters()
    {
        $this->data['name'] = Str::random(66);

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
    }

    public function test_description_must_be_string()
    {
        $this->data['description'] = 1234;

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
    }

    public function test_description_max_length_is_100_characters()
    {
        $this->data['description'] = Str::random(101);

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
    }

    public function test_dishes_must_be_array()
    {
        $this->data['dishes'] = '1234';

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['dishes']]);
    }

    public function test_restaurant_must_exist()
    {
        $this->data['restaurantId'] = 100001;

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['restaurantId']]);
    }

    public function test_dishes_must_exist()
    {
        $this->data['dishes'] = [10001, 293384, 29992723];

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['dishes.0']]);
    }

    public function test_dishes_must_be_integer()
    {
        $this->data['dishes'] = ['111', '222', '333'];

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['dishes.0']]);
    }

}
