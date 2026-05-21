<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@wedding.local'],
            [
                'name'     => 'Admin',
                'email'    => 'admin@wedding.local',
                'password' => Hash::make('wedding2025'),
            ]
        );
    }
}
