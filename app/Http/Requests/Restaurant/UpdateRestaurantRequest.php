<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int         $id
 * @property int         $userId
 * @property string      $code
 * @property string      $name
 * @property string      $description
 * @property string      $address
 * @property string      $phone
 * @property string      $email
 * @property string      $website
 * @property string      $openingHour
 * @property string      $closingHour
 * @property string|null $logo
 * @property string|null $image
 */
class UpdateRestaurantRequest extends FormRequest
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
                'userId' => 'required|exists:users,id',
                'code' => 'required|string|max:255|unique:restaurants',
                'name' => 'required|string|max:65',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:65',
                'email' => 'required|string|email|max:255|unique:restaurants',
                'description' => 'required|string|max:255',
                'openingHour' => 'required|date_format:H:i',
                'closingHour' => 'required|date_format:H:i|after:openingHour',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'website' => 'nullable|string|max:65',
            ];

        }else{
            return [
                'userId' => 'sometimes|exists:users,id',
                'code' => 'sometimes|string|max:255|unique:restaurants',
                'name' => 'sometimes|string|max:65',
                'address' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:65',
                'email' => 'sometimes|string|email|max:255|unique:restaurants',
                'description' => 'sometimes|nullable|string|max:255',
                'openingHour' => 'sometimes|date_format:H:i',
                'closingHour' => 'sometimes|date_format:H:i|after:openingHour',
                'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'logo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'website' => 'sometimes|nullable|string|max:65',
            ];
        }
    }

    protected function prepareForValidation(): void
    {
        if($this->userId){
            $this->merge([
                'user_id' => $this->userId,
            ]);
        }
        if($this->openingHour){
            $this->merge([
                'opening_hour' => $this->openingHour,
            ]);
        }
        if($this->closingHour){
            $this->merge([
                'closing_hour' => $this->closingHour,
            ]);
        }
    }
}
