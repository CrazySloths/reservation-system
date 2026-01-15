<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSubmitted extends Notification
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
                    ->subject('Payment Received - Under Review')
                    ->markdown('emails.payment-submitted', [
                        'booking' => $this->booking,
                        'paymentSlip' => $this->paymentSlip
                    ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'facility_name' => $this->booking->facility_name,
            'payment_slip_number' => $this->paymentSlip->slip_number,
            'message' => 'Your payment submission has been received and is under review by the treasurer.',
        ];
    }
}
