<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
        ]);
        $admin->assignRole(Roles::ADMIN);

        $user = User::factory()->create([
            'name' => 'Abraham Gonzalez',
            'email' => 'abraham@mail.com',
        ]);
        $user->assignRole(Roles::USER);
        $user->givePermissionTo([
            'DELETE_RESTAURANT',
            'EDIT_RESTAURANT',
            'CREATE_RESTAURANT',
        ]);

        User::factory(10)->create()->each(function ($user) {
            $user->assignRole(Roles::USER);
        });
    }
}
