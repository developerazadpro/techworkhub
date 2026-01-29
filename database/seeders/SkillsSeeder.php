<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillsSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'plumber',
            'electrician',
            'carpenter',
            'wiring',
            'switch repair',
            'generator repair',
            'ac servicing',
            'pipe fitting',
            'circuit breaker',
            'lighting installation',
        ];

        foreach ($skills as $skill) {
            DB::table('skills')->insert([
                'name' => $skill,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
