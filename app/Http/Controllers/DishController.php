<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Http\Resources\Dish\DishResource;
use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class DishController extends Controller
{
    /**
     * @param Restaurant $restaurant
     *
     * @return AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
     * Store a newly created resource in storage.
     */
    public function store(StoreDishRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dish $dish)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDishRequest $request, Dish $dish)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dish $dish)
    {
        //
    }
}
