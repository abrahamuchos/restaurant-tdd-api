<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreRestaurantRequest;
use App\Http\Requests\Restaurant\UpdateRestaurantRequest;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $user = auth()->user();
        $restaurants = Restaurant::where('user_id', $user->id)->get();

        return RestaurantResource::collection($restaurants);
    }

    /**
     * @param StoreRestaurantRequest $request
     *
     * @return RestaurantResource
     */
    public function store(StoreRestaurantRequest $request): RestaurantResource
    {
        $restaurant = Restaurant::create([
            'user_id' => $request->userId,
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'opening_hour' => $request->openingHour,
            'closing_hour' => $request->closingHour,
            'logo' => $request->logo,
            'image' => $request->image,
        ]);


        return new RestaurantResource($restaurant);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        //
    }

    /**
     * Update the specified restaurant in storage.
     *
     * @param UpdateRestaurantRequest $request
     * @param Restaurant              $restaurant
     *
     * @return RestaurantResource
     * @throws AuthorizationException
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant): RestaurantResource
    {
        Gate::authorize('update', $restaurant);

        $restaurant->update($request->all());

        return new RestaurantResource($restaurant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        //
    }
}
