<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerified extends Notification
{

    public $booking;
    public $paymentSlip;

    public function __construct($booking, $paymentSlip)
    {
        $this->booking = $booking;
        $this->paymentSlip = $paymentSlip;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Payment Confirmed! Booking Reserved')
                    ->markdown('emails.payment-verified', [
                        'booking' => $this->booking,
                        'paymentSlip' => $this->paymentSlip
                    ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'facility_name' => $this->booking->facility_name,
            'or_number' => $this->paymentSlip->or_number,
            'message' => 'Your payment has been verified and Official Receipt has been issued.',
        ];
    }
}
