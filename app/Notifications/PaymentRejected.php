<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification
{

    public $booking;
    public $paymentSlip;
    public $reason;

    public function __construct($booking, $paymentSlip, $reason = null)
    {
        $this->booking = $booking;
        $this->paymentSlip = $paymentSlip;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Payment Verification Failed - Resubmission Required')
                    ->markdown('emails.payment-rejected', [
                        'booking' => $this->booking,
                        'paymentSlip' => $this->paymentSlip,
                        'reason' => $this->reason
                    ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'facility_name' => $this->booking->facility_name,
            'payment_slip_number' => $this->paymentSlip->slip_number,
            'reason' => $this->reason,
            'message' => 'Payment verification failed. Please resubmit correct payment proof.',
        ];
    }
}
