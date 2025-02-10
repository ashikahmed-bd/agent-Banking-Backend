<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invitation $invitation;
    public string $invitationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->invitationUrl = config('app.client_url') . "/register?token=" . $this->invitation->token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You are invited to join!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Generate the frontend registration URL
    $invitationUrl = config('app.client_url') . "/register?token=" . $this->invitation->token;

        return new Content(
            view: 'emails.invitation',
            with: [
                'invitationUrl' => $this->invitationUrl,
                'email' => $this->invitation->email,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
