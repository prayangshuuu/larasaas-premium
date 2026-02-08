<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        return [10, 30, 60];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Webhook $webhook,
        public string $event,
        public array $payload
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = $this->webhook->url;
        $secret = $this->webhook->secret;
        
        $body = json_encode($this->payload);
        $signature = $secret ? hash_hmac('sha256', $body, $secret) : null;

        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'MySaaS-Webhook-Bot/1.0',
            'X-Webhook-Event' => $this->event,
        ];

        if ($signature) {
            $headers['X-Hub-Signature-256'] = 'sha256=' . $signature;
        }

        $responseStatus = null;
        $responseBody = null;

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, $this->payload);

            $responseStatus = $response->status();
            $responseBody = $response->body();

            // Throw exception if client/server error to trigger retry
            // explicitly check for success first
            if (!$response->successful()) {
               $response->throw();
            }

        } catch (\Exception $e) {
            // Capture error details for the log
            // If it was an HTTP exception, we might have status/body
            if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response) {
                $responseStatus = $e->response->status();
                $responseBody = $e->response->body();
            } else {
                 $responseBody = $e->getMessage();
            }
             // Re-throw to trigger retry mechanism if attempts remain
             // The job will fail eventually and we want that to be recorded by queue worker, 
             // but we also want to log the specific delivery attempt.
             // However, if we re-throw, this specific execution stops. 
             // We should log this attempt first.
             
             // NOTE: If we want to log EVERY attempt, we do it here. 
             // If we only want to log the final result, we might do it in `failed` method.
             // Requirement says: "Log history". Typically every attempt or at least the final one.
             // Let's log every attempt for visibility.
             
             $this->logDelivery($responseStatus, $responseBody);
             
             throw $e;
        }

        $this->logDelivery($responseStatus, $responseBody);
    }

    protected function logDelivery($status, $body)
    {
        WebhookDelivery::create([
            'webhook_id' => $this->webhook->id,
            'event' => $this->event,
            'payload' => $this->payload,
            'response_status' => $status,
            'response_body' => substr($body, 0, 60000), // Truncate to avoid text limit issues if huge
        ]);
    }
}
