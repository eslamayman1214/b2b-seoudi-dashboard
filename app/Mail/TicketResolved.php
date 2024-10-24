<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketResolved extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->view('emails.ticket-resolved')
            ->subject('Your Ticket Has Been Resolved')
            ->with([
                'ticketId' => $this->ticket->id,
                'description' => $this->ticket->description,
                'section' => $this->ticket->section,
            ]);
    }
}
