<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SupportTicket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Support Ticket Status Update: #' . $this->ticket->ticket_id)
                    ->line('Your support ticket status has been updated to: ' . ucfirst(str_replace('_', ' ', $this->ticket->status)))
                    ->action('View Ticket', route('support.show', $this->ticket))
                    ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'status' => $this->ticket->status,
        ];
    }
}
