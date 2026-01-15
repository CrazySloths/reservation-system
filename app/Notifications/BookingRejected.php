<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRejected extends Notification
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
                    ->subject('Booking Request Declined')
                    ->markdown('emails.booking-rejected', [
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
            'reason' => $this->reason,
            'message' => 'Your booking request has been declined.',
        ];
    }
}
