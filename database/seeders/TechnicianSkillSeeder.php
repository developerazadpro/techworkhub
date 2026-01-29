<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechnicianSkillSeeder extends Seeder
{
    public function run(): void
    {
        $technicians = DB::table('users')
            ->whereIn('email', ['tech1@example.com', 'tech2@example.com', 'tech3@example.com'])
            ->pluck('id');

        $skillIds = DB::table('skills')->pluck('id')->toArray();

        foreach ($technicians as $techId) {
            $randomSkills = collect($skillIds)->random(rand(2, 4));

            foreach ($randomSkills as $skillId) {
                DB::table('technician_skill')->insert([
                    'technician_id' => $techId,
                    'skill_id' => $skillId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
