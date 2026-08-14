<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mystore.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+201111111111',
            'image' => null,
            'order_count' => 0,
            'order_sum' => 0,
        ]);

        // Regular users
        $users = [
            ['name' => 'Ahmed Mohamed', 'email' => 'ahmed@example.com', 'phone' => '+201222222222'],
            ['name' => 'Sara Ali', 'email' => 'sara@example.com', 'phone' => '+201333333333'],
            ['name' => 'Omar Hassan', 'email' => 'omar@example.com', 'phone' => '+201444444444'],
        ];

        foreach ($users as $user) {
            User::create(array_merge($user, [
                'password' => Hash::make('password'),
                'role' => 'user',
                'image' => null,
                'order_count' => 0,
                'order_sum' => 0,
            ]));
        }
    }
}
