<?php

namespace App\Policies;

use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RestaurantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Restaurant $restaurant): bool
    {
        return  $user->id === $restaurant->user_id;
    }

    public function viewDishes(User $user, Restaurant $restaurant): bool
    {
        return  $user->id === $restaurant->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    public function createDishes(User $user, Restaurant $restaurant): bool
    {
        return  $user->id === $restaurant->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Restaurant $restaurant): bool
    {
       return  $user->id === $restaurant->user_id;
    }

    public function updateDishes(User $user, Restaurant $restaurant): bool
    {
       return  $user->id === $restaurant->user_id;
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Restaurant $restaurant): bool
    {
        return  $user->id === $restaurant->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Restaurant $restaurant): bool
    {
        //
    }

    public function deleteDishes(User $user, Restaurant $restaurant, Dish $dish): bool
    {
        return  $user->id === $restaurant->user_id && $dish->restaurant_id === $restaurant->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Restaurant $restaurant): bool
    {
        //
    }
}
