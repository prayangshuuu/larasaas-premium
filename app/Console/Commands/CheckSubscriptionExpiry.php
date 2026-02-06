<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and mark them as expired.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        // Find subscriptions that are active or canceled (grace period)
        // AND have passed their current_period_end
        $expiredSubscriptions = Subscription::whereIn('status', ['active', 'canceled'])
            ->where('current_period_end', '<', now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return;
        }

        $count = 0;

        foreach ($expiredSubscriptions as $sub) {
            try {
                // Update status to 'expired'
                $sub->update(['status' => 'expired']);
                
                $this->info("Expired subscription ID: {$sub->id} (User: {$sub->user_id})");
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to expire subscription {$sub->id}: " . $e->getMessage());
                Log::error("Failed to expire subscription {$sub->id}", ['exception' => $e]);
            }
        }

        $this->info("Successfully processed {$count} expired subscriptions.");
    }
}
