<?php

namespace App\Http\Requests;

use App\Rules\MenuDishRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
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
            'name' => 'required|string|max:65',
            'description' => 'required|string|max:100',
            'restaurantId' => 'required|integer|exists:restaurants,id',
            'dishes' => 'nullable|array',
            'dishes.*' => ['nullable','integer','exists:dishes,id', new MenuDishRule()],
        ];
    }
}
