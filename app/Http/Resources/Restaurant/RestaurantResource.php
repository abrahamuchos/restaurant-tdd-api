<?php

namespace App\Http\Resources\Restaurant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int                             $id
 * @property int                             $user_id
 * @property string                          $code
 * @property string                          $name
 * @property string                          $description
 * @property string                          $address
 * @property string                          $phone
 * @property string                          $email
 * @property string                          $website
 * @property string|null                     $logo
 * @property string|null                     $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string                          $opening_hour
 * @property string                          $closing_hour
 */
class RestaurantResource extends JsonResource
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
            'userId' => $this->user_id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'openingHour' => $this->opening_hour,
            'closingHour' => $this->closing_hour,
            'logo' => $this->logo,
            'image' => $this->image,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'links' => [
                'self' => route('restaurants.show', $this->id),
                'menus' => route('restaurants.menus.index', $this->id),
                'dishes' => route('restaurants.dishes.index', $this->id),
            ]

        ];
    }
}
