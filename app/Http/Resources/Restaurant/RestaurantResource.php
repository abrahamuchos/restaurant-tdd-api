<?php

namespace App\Http\Resources\Restaurant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int                             $id
 * @property int                             $userId
 * @property string                          $code
 * @property string                          $name
 * @property string                          $description
 * @property string                          $address
 * @property string                          $phone
 * @property string                          $email
 * @property string                          $website
 * @property string                          $openingHour
 * @property string                          $closingHour
 * @property string|null                     $logo
 * @property string|null                     $image
 * @property \Illuminate\Support\Carbon|null $createdAt
 * @property \Illuminate\Support\Carbon|null $updatedAt
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
            'userId' => $this->userId,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'openingHour' => $this->openingHour,
            'closingHour' => $this->closingHour,
            'logo' => $this->logo,
            'image' => $this->image,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,

        ];
    }
}
