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
 * @property string|null                                                          $qr
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
            'type' => 'menus',
            'id' => $this->id,
            'restaurantId' => $this->restaurant_id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'self' => route('restaurants.menus.show', [$this->restaurant_id, $this->id]),
                'parent' => route('restaurants.show', $this->restaurant_id),
                'public' => route('public.menus.show', $this->id),
                'qr' => $this->qr,
            ],
            'relationships' => [
                'dishes' => DishResource::collection($this->whenLoaded('dishes')),
                'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            ],
        ];
    }
}
