<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\Menu\MenuResource;
use App\Jobs\GenerateQrJob;
use App\Models\Menu;
use App\Models\Restaurant;

class MenuController extends Controller
{
    /**
     * @param Restaurant $restaurant
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Restaurant $restaurant): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return MenuResource::collection($restaurant->menus()->search()->sort()->paginate());
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
        /**
         * @type Menu $menu
         */
        $menu = $restaurant->menus()->create($request->only('name', 'description'));
        $menu->dishes()->sync($request->input('dishes', []));

        //Generate QR code for menu
        GenerateQrJob::dispatch($menu);

        return new MenuResource($menu->load('dishes', 'restaurant'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Menu $menu): MenuResource
    {
        return new MenuResource($menu->load('dishes', 'restaurant'));
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

        if ($request->has('dishes')) {
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
