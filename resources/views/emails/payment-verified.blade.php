@extends('emails.layout')

@section('title', 'Payment Confirmed - Booking Reserved')

@section('content')
    <h2>Payment Confirmed! Booking Reserved</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>Excellent news! Your payment has been verified by our treasurer and your booking is now reserved.</p>
    
    <div class="info-box info-box-success">
        <h3>Official Receipt Issued</h3>
        <p><strong>OR Number:</strong> {{ $paymentSlip->or_number }}</p>
        <p><strong>Payment Slip:</strong> {{ $paymentSlip->slip_number }}</p>
        <p><strong>Amount Paid:</strong> ₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
        <p><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($paymentSlip->paid_at)->format('F d, Y - h:i A') }}</p>
    </div>
    
    <div class="info-box">
        <h3>Booking Information</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
        <p><strong>Expected Attendees:</strong> {{ $booking->expected_attendees }} people</p>
    </div>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Next Steps</h3>
    
    <p><strong>Admin Final Confirmation</strong><br>
    Our admin will perform a final review and issue the official booking confirmation.</p>
    
    <p><strong>Prepare for Your Event</strong><br>
    Start planning your event! You will receive final confirmation and event guidelines soon.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/payments/' . $paymentSlip->id . '/receipt') }}" class="button">
            Download Official Receipt
        </a>
    </p>
    
    <p style="text-align: center; margin-top: 15px;">
        <a href="{{ url('/citizen/reservations/' . $booking->id) }}" class="button button-secondary">
            View Booking Details
        </a>
    </p>
@endsection

