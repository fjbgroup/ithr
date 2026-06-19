<?php

namespace App\Mail;

use App\Models\BusinessTravel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TravelMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $heading,
        public string $body,
        public BusinessTravel $travel,
        public string $statusColor = '#1e40af',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[' . config('app.name') . '] ' . $this->heading);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.travel');
    }
}
