<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private const SEED_USERS = [
        [
            'name' => 'Admin',
            'email' => 'admin@larapi.com',
            'password' => 'admin',
        ],
        [
            'name' => 'Alex',
            'email' => 'alex@larapi.com',
            'password' => 'user1',
        ],
        [
            'name' => 'Ryan',
            'email' => 'ryan@larapi.com',
            'password' => 'user2',
        ],
        [
            'name' => 'Dave',
            'email' => 'dave@larapi.com',
            'password' => 'user3',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hashedUsers = array_map(function ($user) {
            $user['password'] = isset($user['password']) ? Hash::make($user['password']) : '';

            return $user;
        }, self::SEED_USERS);

        User::factory()->createMany($hashedUsers);
    }
}
