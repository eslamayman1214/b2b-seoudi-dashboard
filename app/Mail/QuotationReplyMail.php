<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class QuotationReplyMail extends Mailable
{
    public $quotation, $status, $notes;

    public function __construct($quotation, $status, $notes)
    {
        $this->quotation = $quotation;
        $this->status = $status;
        $this->notes = $notes;
    }

    public function build()
    {
        return $this->subject("Quotation Reply: {$this->status}")
            ->view('emails.quotations.reply')
            ->with([
                'quotation' => $this->quotation,
                'status' => $this->status,
                'notes' => $this->notes,
            ]);
    }
}