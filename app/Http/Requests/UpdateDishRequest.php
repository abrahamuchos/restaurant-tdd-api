<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        if($this->method() === 'PUT'){
            return [
                'restaurantId' => 'required|exists:restaurants,id',
                'name' => 'required|string|max:65',
                'description' => 'nullable|string|max:100',
                'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|min:0.01',
                'isAvailable' => 'required|boolean',
            ];

        }else{
            return [
                'restaurantId' => 'sometimes|exists:restaurants,id',
                'name' => 'sometimes|string|max:65',
                'description' => 'sometimes|nullable|string|max:100',
                'price' => 'sometimes|numeric|regex:/^\d+(\.\d{1,2})?$/|min:0.01',
                'isAvailable' => 'sometimes|boolean',
            ];
        }
    }
}
