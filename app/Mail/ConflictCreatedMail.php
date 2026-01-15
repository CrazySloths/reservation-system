<?php

namespace App\Mail;

use App\Models\BookingConflict;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConflictCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $conflict;
    public $booking;
    public $cityEvent;
    public $facility;
    public $citizen;

    /**
     * Create a new message instance.
     */
    public function __construct($conflict, $booking, $cityEvent, $facility, $citizen)
    {
        $this->conflict = $conflict;
        $this->booking = $booking;
        $this->cityEvent = $cityEvent;
        $this->facility = $facility;
        $this->citizen = $citizen;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Important: City Event Affects Your Booking',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.conflict-created',
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
