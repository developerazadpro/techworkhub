<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $clientRole = DB::table('roles')->where('name', 'client')->value('id');
        $techRole = DB::table('roles')->where('name', 'technician')->value('id');

        $users = DB::table('users')->get();

        foreach ($users as $user) {
            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => str_contains($user->email, 'client')
                    ? $clientRole
                    : $techRole,
            ]);
        }
    }
}
