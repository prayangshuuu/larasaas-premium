<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncStripeRevenue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revenue:sync-stripe {--fresh : Wipes all existing transactions before syncing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync historical revenue data from Stripe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            if ($this->confirm('This will wipe all existing transaction data. Continue?', true)) {
                $this->info('Truncating transactions table...');
                \App\Models\Transaction::truncate();
            }
        }

        $users = \App\Models\User::whereNotNull('stripe_id')->get();
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        // Initialize Stripe Client
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        foreach ($users as $user) {
            try {
                // Fetch all invoices from Stripe (using Stripe PHP SDK directly)
                $invoices = $stripe->invoices->all([
                    'customer' => $user->stripe_id,
                    'limit' => 100, // Pagination consideration for future
                    'status' => 'paid', // Only fetch paid invoices
                ]);

                foreach ($invoices->autoPagingIterator() as $invoice) {
                    \App\Models\Transaction::updateOrCreate(
                        ['invoice_id' => $invoice->id],
                        [
                            'user_id' => $user->id,
                            'amount' => $invoice->total / 100, // Stripe amount is in cents
                            'currency' => $invoice->currency,
                            'status' => $invoice->status,
                            'payment_method' => 'card', // Simplified
                            'paid_at' => \Carbon\Carbon::createFromTimestamp($invoice->created),
                        ]
                    );
                }
            } catch (\Exception $e) {
                // User might have a stripe_id but deleted in Stripe, or API error
                // Log error but continue
                \Illuminate\Support\Facades\Log::error("Failed to sync user {$user->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Stripe revenue sync completed successfully.');
    }
}
