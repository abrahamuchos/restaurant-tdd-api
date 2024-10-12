<?php

namespace App\Http\Resources\Dish;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int                              $id
 * @property int                              $restaurant_id
 * @property string                           $name
 * @property string|null                      $description
 * @property float                            $price
 * @property bool                             $is_available
 * @property \Illuminate\Support\Carbon|null  $created_at
 * @property \Illuminate\Support\Carbon|null  $updated_at
 * @property-read \App\Models\Restaurant|null $restaurant
 */
class DishResource extends JsonResource
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
//            'restaurantId',
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'isAvailable' => $this->is_available,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
