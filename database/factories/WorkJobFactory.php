<?php

namespace Database\Factories;

use App\Models\WorkJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkJobFactory extends Factory
{
    protected $model = WorkJob::class;

    public function definition(): array
    {
        $statuses = [
            WorkJob::STATUS_OPEN,
            WorkJob::STATUS_ASSIGNED,
            WorkJob::STATUS_IN_PROGRESS,
            WorkJob::STATUS_COMPLETED,
            WorkJob::STATUS_CANCELLED,
        ];

        // create 2 users for recommended technicians and get their IDs
        $recommendedTechnicians = User::factory()->count(2)->create()->pluck('id')->toArray();

        return [
            'client_id' => User::factory(), // automatically create a client user
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'skills' => $this->faker->words(3), // creates array of 3 skills
            'recommended_technicians' => $recommendedTechnicians, // array of IDs
        ];
    }
}
