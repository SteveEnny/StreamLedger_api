<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\postJson;

test('a user can sign up successfully with valid data', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
        // 'password_confirmation' => 'password',
    ];

    $this->postJson('/api_v1/register', $userData)
        ->assertStatus(201);

    $this->assertDatabaseHas('users', [
        'email' => 'john.doe@example.com',
    ]);
});

test('an existing user can sign in successfully', function () {
    $user = User::factory()->create([
        'email' => 'testuser@example.com',
        'password' => Hash::make('password'),
    ]);

    $credentials = [
        'email' => 'testuser@example.com',
        'password' => 'password',
    ];

    $this->postJson('/api_v1/login', $credentials)
        ->assertStatus(200)
        ->assertJsonStructure([
                 'data' => [
                     'token'
                 ]
        ]);
    });


test('user cannot sign up with an existing email address', function () {
    User::factory()->create([
        'name' => 'John Doe',
        'email' => 'joe.doe@example.com',
        'password' => 'password',
    ]);

    $userData = [
        'name' => 'Jane Smith',
        'email' => 'joe.doe@example.com',
        'password' => 'password',
        // 'password_confirmation' => 'password',
    ];

    $this->postJson('/api_v1/register', $userData)
        ->assertStatus(422)
        ->assertJsonValidationErrors('email');
});