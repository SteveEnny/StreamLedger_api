<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a wallet is automatically created when a user signs up', function () {
    // Create a user
    $user = User::factory()->create();

    expect($user->wallet)
        ->not->toBeNull()
        ->and($user->wallet->balance)->toBe(0);

    $this->assertDatabaseHas('wallets', [
        'user_id' => $user->id,
        'balance' => 0,
    ]);
});

test('a user has only one wallet', function () {
    $user = User::factory()->create();

    $user->update(['name' => 'Updated Name']);
    expect($user->wallet()->count())->toBe(1);
});