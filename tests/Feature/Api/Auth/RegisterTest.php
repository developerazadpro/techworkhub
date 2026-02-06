<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\postJson;

beforeEach(function () {
    // Reset DB
    $this->artisan('migrate:fresh');

    // Seed required roles
    Role::create(['name' => 'client']);
    Role::create(['name' => 'technician']);
});

test('user can register as client', function () {
    $response = postJson('/api/register', [
        'name' => 'Test Client',
        'email' => 'client@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'client',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ]);
    
    $user = User::where('email', 'client@test.com')->first();
    
    expect($user)->not->toBeNull();
    expect($user->roles->pluck('name')->contains('client'))->toBeTrue();
});

test('user can register as technician', function () {
    $response = postJson('/api/register', [
        'name' => 'Test Technician',
        'email' => 'tech@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'technician',
    ]);

    $response->assertCreated();

    $user = User::where('email', 'tech@test.com')->first();

    expect($user)->not->toBeNull();
    expect($user->roles->pluck('name')->contains('technician'))->toBeTrue();
});

test('registration fails with duplicate email', function () {
    User::factory()->create([
        'email' => 'duplicate@test.com',
    ]);

    $response = postJson('/api/register', [
        'name' => 'Another User',
        'email' => 'duplicate@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'client',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registration fails if role is invalid', function () {
    $response = postJson('/api/register', [
        'name' => 'Bad Role',
        'email' => 'badrole@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'badRole',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['role']);
});
