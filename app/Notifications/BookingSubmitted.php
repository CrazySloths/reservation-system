<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingSubmitted extends Notification
{

    public $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Booking Request Received - ' . $this->booking->facility_name)
                    ->markdown('emails.booking-submitted', ['booking' => $this->booking]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        // Customize message based on recipient's role
        $message = 'Your booking request has been submitted and is awaiting staff verification.';
        
        // If recipient is staff, show different message
        if (session('subsystem_role_name') === 'Reservations Staff' || 
            (isset($notifiable->subsystem_role_id) && $notifiable->subsystem_role_id == 3)) {
            $message = 'New booking request received. Please review and verify the booking details.';
        }
        
        return [
            'booking_id' => $this->booking->id,
            'facility_name' => $this->booking->facility_name,
            'booking_reference' => $this->booking->booking_reference,
            'message' => $message,
        ];
    }
}
