<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Thank you for your message')
            ->view('emails.contact-user');
    }
}
