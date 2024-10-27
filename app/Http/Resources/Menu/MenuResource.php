<?php

namespace App\Http\Resources\Menu;

use App\Http\Resources\Dish\DishResource;
use App\Http\Resources\Restaurant\RestaurantResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int                                                                  $id
 * @property int                                                                  $restaurant_id
 * @property string                                                               $name
 * @property string                                                               $description
 * @property \Illuminate\Support\Carbon|null                                      $created_at
 * @property \Illuminate\Support\Carbon|null                                      $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dish> $dishes
 * @property-read int|null                                                        $dishes_count
 * @property-read \App\Models\Restaurant                                          $restaurant
 */
class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurantId' => $this->restaurant_id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'dishes' => $this->whenLoaded('dishes', DishResource::collection($this->dishes)),
            'restaurant' => $this->whenLoaded('restaurant', new RestaurantResource($this->restaurant)),
        ];
    }
}
