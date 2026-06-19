<?php

namespace App\Mail;

use App\Models\UpdateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $heading,
        public string $body,
        public UpdateRequest $requestData,
        public ?string $adminNote = null,
        public string $statusColor = '#1e40af', // Default blue
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[' . config('app.name') . '] ' . $this->heading);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.request');
    }
}
