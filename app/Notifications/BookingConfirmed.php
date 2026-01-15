<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
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
                    ->subject('Your Booking is Confirmed - ' . $this->booking->facility_name)
                    ->markdown('emails.booking-confirmed', ['booking' => $this->booking]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'facility_name' => $this->booking->facility_name,
            'booking_reference' => $this->booking->booking_reference,
            'event_date' => $this->booking->start_time,
            'message' => 'Your booking has been confirmed! Get ready for your event.',
        ];
    }
}
