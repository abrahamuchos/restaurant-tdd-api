<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreRestaurantRequest;
use App\Http\Requests\Restaurant\UpdateRestaurantRequest;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'perPage' => 'nullable|integer|min:1|max:50',
        ]);

        $user = auth()->user();
        $restaurants = Restaurant::where('user_id', $user->id)
            ->paginate($request->perPage ?? 15, ['*'], 'page', $request->page ?? 1);

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
     * @param Restaurant $restaurant
     *
     * @return RestaurantResource
     * @throws AuthorizationException
     */
    public function show(Restaurant $restaurant): RestaurantResource
    {
        Gate::authorize('view', $restaurant);

        return new RestaurantResource($restaurant);
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
     * @param Restaurant $restaurant
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Restaurant $restaurant): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('delete', $restaurant);
        $wasDeleted = $restaurant->delete();

        return $wasDeleted ? response()->json([], 204) : response()->json([], 404);
    }
}
