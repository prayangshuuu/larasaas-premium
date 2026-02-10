<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = \App\Models\User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found, skipping transaction seeding.');
            return;
        }

        foreach ($users as $user) {
            // Generate 3-8 transactions per user
            $count = rand(3, 8);

            for ($i = 0; $i < $count; $i++) {
                $status = fake()->randomElement(['paid', 'paid', 'paid', 'open', 'failed']);
                $paidAt = $status === 'paid' ? fake()->dateTimeBetween('-1 year', 'now') : null;

                \App\Models\Transaction::create([
                    'user_id' => $user->id,
                    'invoice_id' => 'inv_' . fake()->bothify('??##??##'),
                    'amount' => fake()->randomFloat(2, 10, 500),
                    'currency' => 'USD',
                    'status' => $status,
                    'payment_method' => fake()->randomElement(['card', 'paypal']),
                    'paid_at' => $paidAt,
                    'created_at' => $paidAt ?? fake()->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }
    }
}
