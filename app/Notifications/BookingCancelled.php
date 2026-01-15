<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification
{
    public $booking;
    public $reason;

    public function __construct($booking, $reason = null)
    {
        $this->booking = $booking;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Booking Cancelled by Citizen')
                    ->markdown('emails.booking-cancelled', [
                        'booking' => $this->booking,
                        'reason' => $this->reason
                    ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'facility_name' => $this->booking->facility_name,
            'booking_reference' => $this->booking->booking_reference,
            'citizen_name' => $this->booking->applicant_name ?? $this->booking->user_name,
            'reason' => $this->reason,
            'message' => 'A booking has been cancelled by the citizen.',
        ];
    }
}

