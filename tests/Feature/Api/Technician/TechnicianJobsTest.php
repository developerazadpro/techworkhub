<?php

use App\Models\User;
use App\Models\Role;
use App\Models\WorkJob;
use App\Models\JobAssignment;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\patchJson;

beforeEach(function () {
    $techRole = Role::firstOrCreate(['name' => 'technician']);
    $this->technician = User::factory()->create();
    $this->technician->roles()->attach($techRole);

    Sanctum::actingAs($this->technician);

    $this->job = WorkJob::factory()->create([
        'status' => 'open',
        'client_id' => User::factory(),
    ]);
});

test('technician can see all open jobs', function () {
    $response = getJson('/api/work-jobs');

    $response
        ->assertStatus(200)
        ->assertJsonStructure(['jobs']);
});

test('technician can see job details', function () {
    $response = getJson("/api/work-jobs/{$this->job->id}");

    $response
        ->assertStatus(200)
        ->assertJson([
            'job' => [
                'id' => $this->job->id,
            ],
        ]);
});

test('technician can accept a job', function () {
    $response = postJson("/api/work-jobs/{$this->job->id}/accept");

    $response->assertStatus(200);
    $this->assertDatabaseHas('work_jobs', [
        'id' => $this->job->id,
        'status' => 'assigned',
    ]);
});

test('technician can update job status', function () {
    // first accept the job
    $this->job->status = 'assigned';
    $this->job->save();

    $response = patchJson("/api/work-jobs/{$this->job->id}/status", [
        'status' => 'in_progress'
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('work_jobs', [
        'id' => $this->job->id,
        'status' => 'in_progress',
    ]);
});

test('technician can view my jobs', function () {
    // create a job
    $job = WorkJob::factory()->create([
        'status' => 'assigned',
        'client_id' => User::factory(),
    ]);

    // assign this job to the technician via pivot table
    JobAssignment::create([
        'work_job_id' => $job->id,
        'technician_id' => $this->technician->id,
    ]);

    $response = getJson('/api/my-jobs');

    $response
        ->assertStatus(200)
        ->assertJsonFragment([
            'id' => $job->id,
            'status' => 'assigned',
        ]);
});
