<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         //admin
         User::updateOrCreate(
            ['email' => 'admin@grtech.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        //usr
        User::updateOrCreate(
            ['email' => 'user@grtech.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'role' => 'user'
            ]
        );
    }
}
