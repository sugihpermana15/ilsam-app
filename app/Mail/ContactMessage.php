<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<string, mixed> */
    public array $data;

    public ?string $ip;

    public ?string $userAgent;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(array $data, ?string $ip = null, ?string $userAgent = null)
    {
        $this->data = $data;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    public function envelope(): Envelope
    {
        $name = (string) ($this->data['name'] ?? '');
        $inquiries = (string) ($this->data['inquiries'] ?? '');

        $subject = trim('ILSAM Contact Form' . ($inquiries !== '' ? ' - ' . $inquiries : ''));

        $envelope = new Envelope(subject: $subject);

        $fromEmail = (string) ($this->data['email'] ?? '');
        if ($fromEmail !== '') {
            $envelope->replyTo = [
                new \Illuminate\Mail\Mailables\Address($fromEmail, $name !== '' ? $name : null),
            ];
        }

        return $envelope;
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
            with: [
                'data' => $this->data,
                'ip' => $this->ip,
                'userAgent' => $this->userAgent,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
