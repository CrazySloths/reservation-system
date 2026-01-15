<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffVerified extends Notification
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
                    ->subject('Booking Approved! Pay Within 48 Hours')
                    ->markdown('emails.staff-verified', [
                        'booking' => $this->booking,
                        'paymentSlip' => $this->paymentSlip
                    ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'facility_name' => $this->booking->facility_name,
            'booking_reference' => $this->booking->booking_reference,
            'payment_deadline' => $this->paymentSlip->payment_deadline,
            'amount_due' => $this->paymentSlip->amount_due,
            'payment_slip_id' => $this->paymentSlip->id,
            'message' => 'Your booking has been verified by staff. Please submit payment within 48 hours. Go to Payment Slips (sidebar) to view your payment slip and pay via Cash at CTO or Cashless (GCash, Maya, Bank Transfer).',
        ];
    }
}
