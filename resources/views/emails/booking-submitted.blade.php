@extends('emails.layout')

@section('title', 'Booking Request Received')

@section('content')
    <h2>Booking Request Received!</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>Thank you for submitting your facility booking request. We have received your application and it is now being reviewed by our staff.</p>
    
    <div class="info-box info-box-success">
        <h3>Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y - h:i A') }} to {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
        <p><strong>Expected Attendees:</strong> {{ $booking->expected_attendees }} people</p>
        <p><strong>Total Amount:</strong> ₱{{ number_format($booking->total_amount, 2) }}</p>
    </div>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">What Happens Next?</h3>
    
    <p><strong>1. Staff Verification (24-48 hours)</strong><br>
    Our staff will review your booking request and verify all submitted documents.</p>
    
    <p><strong>2. Payment Instructions</strong><br>
    Once approved, you'll receive payment instructions via email.</p>
    
    <p><strong>3. Final Confirmation</strong><br>
    After payment verification, your booking will be confirmed.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/reservations/' . $booking->id) }}" class="button">
            View Booking Status
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px;">
        <strong>Note:</strong> You can track your booking status anytime by logging into your account and visiting "My Reservations".
    </p>
@endsection

