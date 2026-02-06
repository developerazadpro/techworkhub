<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Skill;
use App\Models\WorkJob;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    // Ensure client role exists
    $this->clientRole = Role::firstOrCreate(['name' => 'client']);

    // Create a client user
    $this->client = User::factory()->create();
    $this->client->roles()->attach($this->clientRole);

    // Authenticate as this client
    Sanctum::actingAs($this->client);
});

test('client can create a job', function () {
    $skills = Skill::factory()->count(3)->create();

    $response = postJson('/api/work-jobs', [
        'title' => 'Build React Dashboard',
        'description' => 'Need a React developer',
        'skill_ids' => $skills->pluck('id')->toArray(),
    ]);

    $response
        ->assertStatus(201)
        ->assertJson([
            'job' => [
                'title' => 'Build React Dashboard',
                'status' => 'open',
            ],
        ]);

    $this->assertDatabaseHas('work_jobs', [
        'title' => 'Build React Dashboard',
        'client_id' => $this->client->id,
    ]);
});

test('client can view their jobs list', function () {
    WorkJob::factory()->count(2)->for($this->client, 'client')->create();

    $response = getJson('/api/client/my-jobs');

    $response->assertStatus(200)
             ->assertJsonCount(2, 'jobs');
});

test('client can update own job', function () {
    $job = WorkJob::factory()->for($this->client, 'client')->create([
        'title' => 'Old Title',
    ]);

    $skillIds = Skill::factory()->count(3)->create()->pluck('id')->toArray();

    $response = putJson("/api/work-jobs/{$job->id}", [
        'title' => 'Updated Title',
        'description' => $job->description,
        'skill_ids' => $skillIds,
    ]);

    $response->assertStatus(200)
             ->assertJson([
                 'job' => [
                     'id' => $job->id,
                     'title' => 'Updated Title',
                 ],
             ]);

    $this->assertDatabaseHas('work_jobs', [
        'id' => $job->id,
        'title' => 'Updated Title',
    ]);
});

test('client can view own job details', function () {
    $job = WorkJob::factory()->for($this->client, 'client')->create();

    $response = getJson("/api/work-jobs/{$job->id}");

    $response->assertStatus(200)
             ->assertJson([
                 'job' => [
                     'id' => $job->id,
                     'title' => $job->title,
                 ],
             ]);
});

test('client cannot update another client job', function () {
    $client1 = User::factory()->create();
    $client1->roles()->attach($this->clientRole);

    $client2 = User::factory()->create();
    $client2->roles()->attach($this->clientRole);

    Sanctum::actingAs($client1);

    $job = WorkJob::factory()->for($client2, 'client')->create();

    $skillIds = Skill::factory()->count(3)->create()->pluck('id')->toArray();

    $response = putJson("/api/work-jobs/{$job->id}", [
        'title' => 'Hacked Title',
        'skill_ids' => $skillIds,
    ]);

    $response->assertStatus(403);
});
