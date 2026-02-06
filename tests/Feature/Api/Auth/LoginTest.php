<?php

use App\Models\User;
use function Pest\Laravel\postJson;

test('user can login with valid credentials', function () {
    // Arrange
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    // Act
    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    // Assert
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'token',
        'user' => [
            'id',
            'name',
            'email',
            'role',
        ],
    ]);
});

test('user cannot login with wrong password', function () {
    // Arrange
    $user = User::factory()->create([
        'password' => bcrypt('correct-password'),
    ]);

    // Act
    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    // Assert
    $response->assertStatus(401);
    $response->assertJson([
        'message' => 'Invalid credentials',
    ]);
});

test('user cannot login with non existing email', function () {
    // Act
    $response = postJson('/api/login', [
        'email' => 'ghost@techworkhub.com',
        'password' => 'secret123',
    ]);

    // Assert
    $response->assertStatus(401);
    $response->assertJson([
        'message' => 'Invalid credentials',
    ]);
});

test('login fails if email is missing', function () {
    // Act
    $response = postJson('/api/login', [
        'password' => 'secret123',
    ]);

    // Assert
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

test('login fails with empty request', function () {
    $response = postJson('/api/login', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email', 'password']);
});


