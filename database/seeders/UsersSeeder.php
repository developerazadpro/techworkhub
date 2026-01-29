<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Client One',
                'email' => 'client@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Technician One',
                'email' => 'tech1@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Technician Two',
                'email' => 'tech2@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Technician Three',
                'email' => 'tech3@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
