<?php

use App\Models\User;
use App\Models\Skill;
use App\Models\Role;
use Illuminate\Queue\Middleware\Skip;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\postJson;
use function Pest\Laravel\getJson;

beforeEach(function () {
    // Create a test user and act as this user
    $this->user = User::factory()->create();

    // Create a role
    $role = Role::factory()->create(['name' => 'client']);

    // Attach the role to the user
    $this->user->roles()->attach($role);

    Sanctum::actingAs($this->user);
});

test('user can fetch own info via /user', function () {
    $roleName = $this->user->roles->first()->name;

    $response = getJson('/api/user');

    $response->assertOk()
             ->assertJson([
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $roleName,
             ]);
});

test('user can fetch list of skills', function () {
    Skill::factory()->count(3)->create();

    $response = getJson('/api/skills');

    $response->assertOk()
             ->assertJsonCount(3, 'skills');
});

test('user can create a new skill', function () {
    $skillName = 'Testing Skill';

    $response = postJson('/api/skills', [
        'name' => $skillName,
    ]);

    $response->assertCreated()
             ->assertJson([
                'skill' => [
                    'name' => strtolower($skillName)
                ],
             ]);
});

test('user can resolve skill IDs to names', function () {
    $skill = Skill::factory()->create();

    $response = postJson('/api/skills/resolve-id-to-name', [
        'skill_ids' => [$skill->id],
    ]);

    $response->assertOk()
             ->assertJson([
                 'skills' => [$skill->name],
             ]);
});

test('user can resolve skill names to IDs', function () {
    $skill = Skill::factory()->create();

    $response = postJson('/api/skills/resolve-name-to-id', [
        'skills' => [$skill->name],
    ]);

    $response->assertOk()
             ->assertJson([
                 'skill_ids' => [$skill->id],
             ]);
});