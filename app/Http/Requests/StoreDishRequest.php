<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
class StoreDishRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'restaurantId' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:65',
            'description' => 'nullable|string|max:100',
            'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|min:0.01',
            'isAvailable' => 'required|boolean',
        ];
    }
}
