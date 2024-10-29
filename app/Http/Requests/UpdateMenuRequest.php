<?php

namespace App\Http\Requests;

use App\Rules\MenuDishRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
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
        if($this->method() === 'put'){
            return [
                'name' => 'required|string|max:65',
                'description' => 'required|string|max:100',
                'restaurantId' => 'required|integer|exists:restaurants,id',
                'dishes' => 'nullable|array',
                'dishes.*' => ['nullable','integer','exists:dishes,id', new MenuDishRule()],
            ];

        }else{
            return [
                'name' => 'sometimes|string|max:65',
                'description' => 'sometimes|string|max:100',
                'restaurantId' => 'sometimes|integer|exists:restaurants,id',
                'dishes' => 'sometimes|array',
                'dishes.*' => ['sometimes','integer','exists:dishes,id', new MenuDishRule()],
            ];
        }
    }
}
