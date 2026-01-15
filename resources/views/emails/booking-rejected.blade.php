@extends('emails.layout')

@section('title', 'Booking Request Declined')

@section('content')
    <h2>Booking Request Declined</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>We regret to inform you that your facility booking request has been declined by our staff.</p>
    
    <div class="info-box info-box-error">
        <h3>Declined Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Requested Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y - h:i A') }}</p>
        <p><strong>Status:</strong> Rejected</p>
    </div>
    
    @if($reason)
    <div class="info-box info-box-warning">
        <h3>Reason for Decline</h3>
        <p>{{ $reason }}</p>
    </div>
    @endif
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Common Reasons for Rejection</h3>
    
    <ul style="margin-left: 20px; margin-bottom: 15px;">
        <li>Incomplete or invalid documentation</li>
        <li>Facility already booked for the requested date/time</li>
        <li>Requested event does not meet facility usage guidelines</li>
        <li>Applicant does not meet eligibility requirements</li>
    </ul>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">What You Can Do</h3>
    
    <p><strong>Contact Us:</strong> For more information about the rejection, please contact our Facilities Management Office.</p>
    
    <p><strong>Try Another Facility:</strong> Consider booking a different facility that may better suit your needs.</p>
    
    <p><strong>Different Date:</strong> If the issue was scheduling, try selecting a different date or time.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/facilities') }}" class="button">
            Browse Other Facilities
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px; text-align: center;">
        We apologize for any inconvenience. Please feel free to submit a new booking request.
    </p>
@endsection

