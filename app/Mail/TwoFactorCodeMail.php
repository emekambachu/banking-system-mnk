<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;
    public string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function build(): TwoFactorCodeMail
    {
        return $this
            ->subject('Your Two‑Factor Authentication Code')
            ->markdown('emails.2fa_code', [
                'code' => $this->secret,
            ]);
    }
}
