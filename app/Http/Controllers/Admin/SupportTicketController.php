<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\SupportTicketMessage;
use App\Notifications\SupportTicketStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('user')->orderBy('priority', 'desc')->orderBy('updated_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate(20);

        return view('admin.support.index', compact('tickets'));
    }

    public function create()
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        return view('admin.support.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user_id,
            'ticket_id' => strtoupper(Str::random(10)),
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => $request->priority,
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(), // Admin as sender
            'message' => $request->message,
        ]);

        return redirect()->route('admin.support.index')->with('status', 'Ticket created successfully.');
    }

    public function show(SupportTicket $supportTicket)
    {
        $supportTicket->load(['messages.user', 'user']);
        
        return view('admin.support.show', compact('supportTicket'));
    }

    public function reply(Request $request, SupportTicket $supportTicket)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-attachments', 'public');
        }

        SupportTicketMessage::create([
            'support_ticket_id' => $supportTicket->id,
            // Admin user ID is logged in user.
            // System messages have user_id = null, but here an admin is replying.
            'user_id' => Auth::id(), 
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
        ]);

        $supportTicket->update([
            'status' => 'answered',
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Reply sent successfully.');
    }

    public function updateStatus(Request $request, SupportTicket $supportTicket)
    {
        $request->validate([
            'status' => 'required|in:open,answered,customer_reply,closed',
        ]);

        $oldStatus = $supportTicket->status;
        $newStatus = $request->status;

        $supportTicket->update([
            'status' => $newStatus,
        ]);

        if ($oldStatus !== $newStatus) {
            $supportTicket->user->notify(new \App\Notifications\SupportTicketStatusUpdated($supportTicket));
            \App\Services\WebhookService::trigger($supportTicket->user, 'ticket.updated', $supportTicket->toArray());
        }

        return back()->with('status', 'Ticket status updated.');
    }
}
