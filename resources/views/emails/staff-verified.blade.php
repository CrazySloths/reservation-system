@extends('emails.layout')

@section('title', 'Booking Approved - Payment Required')

@section('content')
    <h2>Booking Approved! Payment Required</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>Great news! Your facility booking request has been reviewed and approved by our staff.</p>
    
    <div class="info-box info-box-success">
        <h3>Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Payment Slip Number:</strong> {{ $paymentSlip->slip_number }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y - h:i A') }} to {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
        <p><strong>Amount Due:</strong> ₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
    </div>
    
    <div class="countdown-timer">
        <h3>PAYMENT DEADLINE</h3>
        <div class="time">48 HOURS</div>
        <p>Deadline: {{ \Carbon\Carbon::parse($paymentSlip->payment_deadline)->format('F d, Y - h:i A') }}</p>
    </div>
    
    <div class="info-box info-box-warning">
        <h3>IMPORTANT: Submit Payment Now</h3>
        <p>To secure your booking, you must submit payment within <strong>48 hours</strong>.</p>
        <p>If payment is not received by the deadline, your booking will automatically expire and the facility will become available to others.</p>
    </div>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">How to Pay</h3>
    
    <p><strong>Option 1: Cash Payment</strong><br>
    Visit the City Treasurer's Office during business hours and present your Payment Slip Number.</p>
    
    <p><strong>Option 2: Cashless Payment</strong><br>
    Pay via GCash, Maya, or Bank Transfer and submit your payment reference number online.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/payments/' . $paymentSlip->id) }}" class="button">
            Submit Payment Now
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px;">
        <strong>Note:</strong> You will receive reminders at 24 hours and 6 hours before the deadline.
    </p>
@endsection

