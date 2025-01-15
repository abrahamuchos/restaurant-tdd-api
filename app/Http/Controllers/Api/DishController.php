<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Base64Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Http\Resources\Dish\DishResource;
use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DishController extends Controller
{
    /**
     * @param Restaurant $restaurant
     * @param Request    $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(Restaurant $restaurant, Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'perPage' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $dishes = $restaurant
            ->dishes()
            ->search()
            ->sort()
            ->paginate($request->perPage ?? 15, ['*'], 'page', $request->page ?? 1);

        return DishResource::collection($dishes);
    }

    /**
     * @param Restaurant       $restaurant
     * @param StoreDishRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(Restaurant $restaurant, StoreDishRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            if ($request->image) {
                list($data, $extension) = Base64Helper::getDataImage($request->image);
                $filename = uniqid() . '.' . $extension;
                $path = 'restaurants/' . $restaurant->id . '/dishes/' . $filename;
                Storage::disk('public')->put($path, $data);
            }

            Dish::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'restaurant_id' => $restaurant->id,
                'image' => $filename ?? null,
                'image_path' => $path ?? null,
            ]);
            DB::commit();

            return response()->json([], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'code' => 5050,
                'message' => 'Menu not created',
                'details' => $e->getMessage(),
            ], 500);
        }
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

        return new DishResource($dish->load(['restaurant', 'menus']));
    }

    /**
     * @param UpdateDishRequest $request
     * @param Dish              $dish
     * @param Restaurant        $restaurant
     *
     * @return DishResource
     * @throws AuthorizationException
     * @throws \Throwable
     */
    public function update(UpdateDishRequest $request, Restaurant $restaurant, Dish $dish): DishResource|JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->except('image');
            if ($request->image) {
                list($dataImg, $extension) = Base64Helper::getDataImage($request->image);
                $filename = uniqid() . '.' . $extension;
                $path = 'restaurants/' . $restaurant->id . '/dishes/' . $filename;
                $data['image'] = $filename;
                Storage::disk('public')->put($path, $dataImg);

                //Remove old image
                if ($dish->image_path) {
                    Storage::disk('public')->delete($dish->image_path);
                }
            }
            $dish->update($data);
            DB::commit();

            return new DishResource($dish->load('menus'));

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'code' => 5050,
                'message' => 'Menu not created',
                'details' => $e->getMessage(),
            ], 500);

        }

    }

    /**
     * @param Restaurant $restaurant
     * @param Dish       $dish
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Restaurant $restaurant, Dish $dish): JsonResponse
    {
        Gate::authorize('deleteDishes', [$restaurant, $dish]);

        $dish->menus()->detach();
        $dish->delete();

        return response()->json([], 204);
    }
}
