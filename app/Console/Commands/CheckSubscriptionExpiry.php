<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire subscriptions that have passed their period end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        $expiredSubscriptions = Subscription::whereIn('status', ['active', 'past_due'])
            ->where('current_period_end', '<', now())
            ->get();

        $count = $expiredSubscriptions->count();

        if ($count > 0) {
            foreach ($expiredSubscriptions as $subscription) {
                $subscription->update(['status' => 'expired']);
                $this->info("Expired subscription ID: {$subscription->id} for User ID: {$subscription->user_id}");
            }
            $this->info("Successfully expired {$count} subscriptions.");
        } else {
            $this->info('No expired subscriptions found.');
        }
    }
}
