<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Skill;
use App\Models\WorkJob;

class WorkJobsSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::whereHas('roles', fn ($q) =>
            $q->where('name', 'client')
        )->pluck('id');

        $technicians = User::whereHas('roles', fn ($q) =>
            $q->where('name', 'technician')
        )->pluck('id');

        $skillNames = Skill::pluck('name');

        for ($i = 1; $i <= 20; $i++) {

            $skills = $skillNames->random(rand(2, 4))->values()->toArray();

            // 50% jobs get recommended technicians
            $recommendedTechnicians =
                rand(0, 1)
                    ? $technicians->random(rand(1, 3))->values()->toArray()
                    : [];

            WorkJob::create([
                'client_id' => $clients->random(),
                'title' => "Service Job #{$i}",
                'description' => "Work job description for job #{$i}",
                'skills' => $skills,
                'status' => 'open',

                // ✅ exactly what you asked
                'recommended_technicians' => $recommendedTechnicians,
            ]);
        }
    }
}
