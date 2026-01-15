<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\BookingConflict;

class ConflictResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $conflict;
    public $booking;
    public $cityEvent;
    public $facility;
    public $choice;

    /**
     * Create a new message instance.
     */
    public function __construct(BookingConflict $conflict)
    {
        $this->conflict = $conflict;
        $this->booking = $conflict->booking(); // Call method, not property
        $this->cityEvent = $conflict->cityEvent;
        
        // Get facility details from database
        if ($this->booking) {
            $this->facility = \DB::connection('facilities_db')
                ->table('facilities')
                ->where('facility_id', $this->booking->facility_id)
                ->first();
        }
        
        $this->choice = $conflict->citizen_choice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Conflict Resolved - ' . ucfirst($this->choice),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.conflict-resolved',
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
