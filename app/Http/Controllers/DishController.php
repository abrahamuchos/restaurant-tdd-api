<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Http\Resources\Dish\DishResource;
use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class DishController extends Controller
{
    /**
     * @param Restaurant $restaurant
     * @param Request    $request
     *
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Restaurant $restaurant, Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewDishes', $restaurant);
        $request->validate([
          'perPage' => 'nullable|integer|min:1|max:100',
          'page' => 'nullable|integer|min:1',
        ]);

        $dishes = $restaurant
            ->dishes()
            ->paginate($request->perPage ?? 15, ['*'], 'page', $request->page ?? 1);

        return DishResource::collection($dishes);
    }

    /**
     * @param Restaurant       $restaurant
     * @param StoreDishRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function store(Restaurant $restaurant, StoreDishRequest $request): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('createDishes', $restaurant);

        Dish::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'restaurant_id' => $restaurant->id,
        ]);

        return response()->json([], 201);
    }

    /**
     * @param Restaurant $restaurant
     * @param Dish       $dish
     *
     * @return DishResource
     * @throws AuthorizationException
     */
    public function show(Restaurant $restaurant, Dish $dish): DishResource
    {
        Gate::authorize('viewDishes', $restaurant);

        return new DishResource($dish->load('restaurant'));
    }

    /**
     * @param UpdateDishRequest $request
     * @param Dish              $dish
     * @param Restaurant        $restaurant
     *
     * @return DishResource
     * @throws AuthorizationException
     */
    public function update(UpdateDishRequest $request, Restaurant $restaurant, Dish $dish)
    {
        Gate::authorize('updateDishes', $restaurant);

        $dish->update($request->validated());

        return new DishResource($dish);
    }

    /**
     * @param Restaurant $restaurant
     * @param Dish       $dish
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Restaurant $restaurant, Dish $dish): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('deleteDishes', [$restaurant, $dish]);

        $dish->delete();

        return response()->json([], 204);
    }
}
