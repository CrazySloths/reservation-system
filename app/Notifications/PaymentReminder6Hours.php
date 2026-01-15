<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminder6Hours extends Notification
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
                    ->subject('URGENT: Payment Deadline in 6 Hours')
                    ->markdown('emails.payment-reminder-6', [
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
            'payment_deadline' => $this->paymentSlip->payment_deadline,
            'amount_due' => $this->paymentSlip->amount_due,
            'message' => 'URGENT: Only 6 hours left to submit payment!',
        ];
    }
}
