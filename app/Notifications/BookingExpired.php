<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingExpired extends Notification
{

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Booking Expired - Payment Not Received')
                    ->markdown('emails.booking-expired', ['booking' => $this->booking]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'facility_name' => $this->booking->facility_name,
            'booking_reference' => $this->booking->booking_reference,
            'message' => 'Your booking has expired due to non-payment within 48 hours.',
        ];
    }
}
