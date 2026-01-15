@extends('emails.layout')

@section('title', 'Booking Expired - Payment Not Received')

@section('content')
    <h2>Booking Expired - Payment Not Received</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>We regret to inform you that your booking has expired because payment was not received within the 48-hour deadline.</p>
    
    <div class="info-box info-box-error">
        <h3>Expired Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Intended Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y - h:i A') }}</p>
        <p><strong>Status:</strong> Expired</p>
    </div>
    
    <p>The facility has been released and is now available for other citizens to book.</p>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Would You Like to Rebook?</h3>
    
    <p>If you still need the facility, you're welcome to make a new booking. Please note:</p>
    <ul style="margin-left: 20px; margin-bottom: 15px;">
        <li>Availability may have changed</li>
        <li>Pricing may be different</li>
        <li>You will need to submit a new booking request</li>
    </ul>
    
    <p>We recommend checking availability first before submitting a new booking.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/facilities/' . $booking->facility_id) }}" class="button">
            Book Again
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px; text-align: center;">
        We're sorry for any inconvenience. To avoid expiration in the future, please submit payment promptly within 48 hours of approval.
    </p>
@endsection

