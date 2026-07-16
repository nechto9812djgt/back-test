<?php

namespace App\Services;

use App\Mail\ContactOwnerMail;
use App\Mail\ContactUserMail;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function send(array $data): void
    {
        Mail::to(config('mail.from.address'))
            ->send(new ContactOwnerMail($data));

        Mail::to($data['email'])
            ->send(new ContactUserMail($data));
    }
}
