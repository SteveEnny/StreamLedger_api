<?php

use App\Models\User;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Junges\Kafka\Facades\Kafka;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->wallet->update(['balance' => 1000]);
});


test('an authenticated user can successfully create a credit transaction', function () {
    $payload = [
        'entry' => 'credit',
        'amount' => 500,
    ];

    $response = $this->actingAs($this->user, 'sanctum')
                     ->postJson('/api_v1/transaction', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('transactions', [
        'user_id' => $this->user->id,
        'entry'   => 'credit',
        'amount'  => 500.0000,
        'balance' => 1500.0000,
    ]);

    // User balance update check
    $this->user->refresh();
    $this->assertEquals(1500, (int)$this->user->wallet->balance);

});


test('an authenticated user can successfully create a debit transaction', function () {
    $payload = [
        'entry' => 'debit',
        'amount' => 500.00,
    ];

    $response = $this->actingAs($this->user, 'sanctum')
                     ->postJson('/api_v1/transaction', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('transactions', [
        'user_id' => $this->user->id,
        'entry' => 'debit',
        'amount' => 500.00,
        'balance' => 500.00,
    ]);
});


test('transaction creation fails due to insufficient funds for a debit entry', function () {
    $payload = [
        'entry' => 'debit',
        'amount' => 1100,
    ];

    $response = $this->actingAs($this->user, 'sanctum')
                    ->postJson('/api_v1/transaction', $payload);;

    $response->assertStatus(422)
            ->assertJson([
             'message' => 'Insufficient balance',
             'status' => 422,
         ]);
});


test('retrieves all transactions with pagination', function () {
    Transaction::factory()->count(5)->create([
        'user_id' => $this->user->id,
        'entry'   => fake()->randomElement(['debit', 'credit']),
        'balance' => 1000,
        'amount'  => fake()->numberBetween(100, 500),
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
                 ->getJson('/api_v1/transaction');

    $response->assertStatus(200)
        ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'entry', 'amount', 'balance', 'created_at', 'updated_at']
                    ]
                ]
            ]);
});


//// UNAUTHENTICATED USERS ////

test('an unauthenticated users cannot create a transaction', function () {
    $payload = [
        'entry' => 'credit',
        'amount' => 100,
    ];

    postJson('/api_v1/transaction', $payload)
        ->assertUnauthorized();
});

test('an unauthenticated users cannot retrieve transactions', function () {
    getJson('/api_v1/transaction')
        ->assertUnauthorized();
});


test('an unauthenticated users cannot export transactions', function () {
    getJson('/api_v1/transaction/export')
        ->assertUnauthorized();
});