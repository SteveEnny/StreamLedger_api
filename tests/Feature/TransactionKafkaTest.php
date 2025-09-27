<?php

use App\Models\User;
use App\Models\Transaction;
use App\Services\KafkaProducerService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->wallet->update(['balance' => 1000]);

});

test('kafka test', function () {
    $this->mock(KafkaProducerService::class);

    Transaction::create([
        'user_id' => $this->user->id,
        'entry' => 'credit',
        'amount' => 100.50,
        'balance' => 1100.50,
    ]);

    $this->assertDatabaseHas('transactions', [
        'user_id' => $this->user->id,
        'amount' => 100.50
    ]);
});