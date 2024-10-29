<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\Menu\MenuResource;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * @param StoreMenuRequest $request
     * @param Restaurant       $restaurant
     *
     * @return MenuResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreMenuRequest $request, Restaurant $restaurant): MenuResource
    {
        Gate::authorize('createMenu', $restaurant);

        /**
         * @type Menu $menu
         */
        $menu = $restaurant->menus()->create($request->only('name', 'description'));
        $menu->dishes()->sync($request->input('dishes', []));

        return new MenuResource($menu->load('dishes', 'restaurant'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * @param UpdateMenuRequest $request
     * @param Restaurant        $restaurant
     * @param Menu              $menu
     *
     * @return MenuResource
     */
    public function update(UpdateMenuRequest $request, Restaurant $restaurant, Menu $menu): MenuResource
    {
        $menu->update($request->only('name', 'description'));

        if( $request->has('dishes') ) {
            $menu->dishes()->sync($request->input('dishes'));
        }

        return new MenuResource($menu->load('dishes', 'restaurant'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        //
    }
}
