<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MenuDishRule implements ValidationRule
{
    /**
     * Validate if restaurant has this dish
     * @param string  $attribute
     * @param mixed   $value - Dish id
     * @param Closure $fail
     *
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!request()->restaurant->dishes->contains($value)) {
            $fail('The dish is not belong to this restaurant');
        }
    }
}
