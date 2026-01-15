@extends('emails.layout')

@section('title', 'Payment Reminder - 24 Hours Left')

@section('content')
    <h2>Payment Deadline: 24 Hours Remaining</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>This is a reminder that your payment deadline is approaching. You have <strong>24 hours remaining</strong> to submit your payment and secure your booking.</p>
    
    <div class="countdown-timer">
        <h3>TIME REMAINING</h3>
        <div class="time">24 HOURS</div>
        <p>Deadline: {{ \Carbon\Carbon::parse($paymentSlip->payment_deadline)->format('F d, Y - h:i A') }}</p>
    </div>
    
    <div class="info-box info-box-warning">
        <h3>Booking at Risk of Expiration</h3>
        <p><strong>Payment Slip:</strong> {{ $paymentSlip->slip_number }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y - h:i A') }}</p>
        <p><strong>Amount Due:</strong> ₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
    </div>
    
    <p><strong>What happens if I don't pay on time?</strong><br>
    If payment is not received by the deadline, your booking will automatically expire and the facility will be released for others to book.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/payments/' . $paymentSlip->id) }}" class="button">
            Pay Now
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px; text-align: center;">
        Don't lose your booking! Submit payment now to avoid expiration.
    </p>
@endsection

