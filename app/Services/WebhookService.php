<?php

namespace App\Services;

use App\Jobs\SendWebhookJob;
use App\Models\User;
use App\Models\Webhook;

class WebhookService
{
    /**
     * Trigger a webhook event for a user.
     *
     * @param User $user
     * @param string $event
     * @param array $payload
     * @return void
     */
    public static function trigger(User $user, string $event, array $payload): void
    {
        // Find all active webhooks for this user
        // We need to filter in PHP or use a JSON query if supported by DB (usually yes for MySQL 5.7+/check json_contains)
        // For simplicity and compatibility, we can fetch active webhooks and filter in code or use whereJsonContains if we are sure of the structure.
        // `events` is a JSON array of strings.
        
        $webhooks = Webhook::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            // Check if the webhook is subscribed to this event
            if (in_array($event, $webhook->events)) {
                dispatch(new SendWebhookJob($webhook, $event, $payload));
            }
        }
    }
}
