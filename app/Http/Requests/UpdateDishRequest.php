<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int                              $id
 * @property int                              $restaurantId
 * @property string                           $name
 * @property string|null                      $description
 * @property float                            $price
 * @property string|null                      $image
 * @property bool                             $isAvailable
 * @property-read \App\Models\Restaurant|null $restaurant
 */
class UpdateDishRequest extends FormRequest
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
        if ($this->method() === 'PUT') {
            return [
                'restaurantId' => 'required|exists:restaurants,id',
                'name' => 'required|string|max:65',
                'description' => 'nullable|string|max:100',
                'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|min:0.01',
                'image' => 'nullable|string',
                'isAvailable' => 'required|boolean',
            ];

        } else {
            return [
                'restaurantId' => 'sometimes|exists:restaurants,id',
                'name' => 'sometimes|string|max:65',
                'description' => 'sometimes|nullable|string|max:100',
                'price' => 'sometimes|numeric|regex:/^\d+(\.\d{1,2})?$/|min:0.01',
                'image' => 'sometimes|nullable|string',
                'isAvailable' => 'sometimes|boolean',
            ];
        }
    }

    protected function prepareForValidation(): void
    {
        if ($this->restaurantId) {
            $this->merge([
                'restaurant_id' => $this->restaurantId,
            ]);
        }

        if ($this->isAvailable) {
            $this->merge([
                'is_available' => $this->isAvailable,
            ]);
        }
    }

}
