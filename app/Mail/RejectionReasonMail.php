<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectionReasonMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $rejectionReason;
    public $note;

    /**
     * Create a new message instance.
     */
    public function __construct($customerName, $rejectionReason, $note)
    {
        $this->customerName = $customerName;
        $this->rejectionReason = $rejectionReason;
        $this->note = $note;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Document Rejection Notice')
            ->view('emails.rejection-reason'); // Update to match the file name
    }
}
