<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Database\Factories\RestaurantFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('id', '!=', 1)->get();

        $users->each(function(User $user){
            $cant = rand(1, 5);
           Restaurant::factory($cant)->create([
               'user_id' => $user->id,
           ]);
        });
    }
}
