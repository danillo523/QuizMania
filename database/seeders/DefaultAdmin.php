<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(
            [
                'name' => 'Admin User',
                'email' => 'adminuser@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
