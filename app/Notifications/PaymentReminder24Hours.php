<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminder24Hours extends Notification
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
                    ->subject('Payment Deadline: 24 Hours Remaining')
                    ->markdown('emails.payment-reminder-24', [
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
            'message' => 'Payment deadline in 24 hours! Submit payment to secure your booking.',
        ];
    }
}
