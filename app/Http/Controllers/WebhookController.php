<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function index()
    {
        $webhooks = auth()->user()->webhooks()->latest()->get();
        return view('webhooks.index', compact('webhooks'));
    }

    public function create()
    {
        return view('webhooks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'events' => 'required|array',
            'events.*' => 'string',
        ]);

        $webhook = auth()->user()->webhooks()->create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'events' => $validated['events'],
            'secret' => Str::random(32),
            'is_active' => true,
        ]);

        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook created successfully. Secret: ' . $webhook->secret);
    }

    public function show(Webhook $webhook)
    {
        // Ensure user owns the webhook
        if ($webhook->user_id !== auth()->id()) {
            abort(403);
        }

        $deliveries = $webhook->deliveries()->latest()->paginate(20);

        return view('webhooks.show', compact('webhook', 'deliveries'));
    }

    public function destroy(Webhook $webhook)
    {
        if ($webhook->user_id !== auth()->id()) {
            abort(403);
        }

        $webhook->delete();

        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook deleted successfully.');
    }
}
