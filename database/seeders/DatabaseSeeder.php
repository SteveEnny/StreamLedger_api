<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user  = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user->wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => fake()->randomFloat(2, 100, 1000),
        ]);

        Transaction::create([
                'user_id' => $user->id,
                'entry' => 'credit',
                'amount' => 100,
                'balance' => 100,
            ]);
        Transaction::create([
                'user_id' => $user->id,
                'entry' => 'credit',
                'amount' => 100,
                'balance' => 200,
            ]);
        Transaction::create([
                'user_id' => $user->id,
                'entry' => 'credit',
                'amount' => 100,
                'balance' => 300,
            ]);

        Transaction::create([
                'user_id' => $user->id,
                'entry' => 'debit',
                'amount' => 100,
                'balance' => 200,
            ]);
        Transaction::create([
                'user_id' => $user->id,
                'entry' => 'debit',
                'amount' => 100,
                'balance' => 100,
            ]);
    }
}