<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SupportTicketController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                if (!\App\Helpers\Feature::enabled('support_enabled')) {
                    abort(404);
                }
                return $next($request);
            },
        ];
    }

    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB limit
        ]);

        // Generate unique 6-digit Ticket ID (e.g., TIC-123456)
        // We use the ID from the database after creation or generate a random one.
        // Requirement says "ticket_id (string, unique, e.g., 'TIC-123456')"
        // We can generate a random string or use the auto-increment ID if we want consistency.
        // Let's generate a random unique string for now as requested.
        $ticketId = 'TIC-' . strtoupper(Str::random(6));
        while (SupportTicket::where('ticket_id', $ticketId)->exists()) {
            $ticketId = 'TIC-' . strtoupper(Str::random(6));
        }

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
            'ticket_id' => $ticketId,
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-attachments', 'public');
        }

        // Create initial message
        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
        ]);

        // Auto-reply logic
        if (\App\Helpers\Feature::enabled('support_auto_reply_enabled')) {
            $autoReplyText = Setting::get('features.support_auto_reply_text', "Thank you for contacting us. We have received your ticket and will get back to you shortly.");
            
            SupportTicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => null, // null for system/admin
                'message' => $autoReplyText,
            ]);
        }

        return redirect()->route('support.index')->with('status', 'Ticket created successfully.');
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load(['messages.user', 'messages' => function($query) {
            $query->orderBy('created_at', 'asc');
        }]);

        return view('support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-attachments', 'public');
        }

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
        ]);

        $ticket->update([
            'status' => 'customer_reply',
            'updated_at' => now(), // bump timestamp
        ]);

        return back()->with('status', 'Reply sent successfully.');
    }

    public function close(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->update([
            'status' => 'closed',
        ]);

        return back()->with('status', 'Ticket closed successfully.');
    }
}
