<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $user;

    public function __construct(Ticket $ticket, User $user)
    {
        $this->ticket = $ticket;
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.ticket-assigned')
            ->subject('New Ticket Assignment')
            ->with([
                'ticket' => $this->ticket,
                'user' => $this->user,
            ]);
    }
}
