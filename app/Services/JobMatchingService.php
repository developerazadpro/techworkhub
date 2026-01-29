<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkJob;
use App\Services\GoMatchingService;

class JobMatchingService
{
    public function run(WorkJob $job): void
    {
        $technicians = User::whereHas('roles', fn ($q) =>
            $q->where('name', 'technician')
        )->with('skills')->get();

        $response = app(GoMatchingService::class)->matchTechnicians([
            'job_id' => $job->id,
            'required_skills' => $job->skills,
            'technicians' => $technicians->map(fn ($tech) => [
                'id' => $tech->id,
                'skills' => $tech->skills->pluck('name')->toArray(),
            ])->toArray(),
        ]);

        $job->update([
            'recommended_technicians' => $response['recommended_technicians'] ?? [],
        ]);
    }
}
