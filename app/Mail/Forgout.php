<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Forgout extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $data)
    {

    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->data['fromEmail'], $this->data['fromName']),
            subject: $this->data['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mails.forgout',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
