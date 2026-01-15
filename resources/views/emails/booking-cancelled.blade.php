@extends('emails.layout')

@section('title', 'Booking Cancelled by Citizen')

@section('content')
    <h2>Booking Cancellation Notice</h2>
    
    <p>Dear Staff,</p>
    
    <p>A citizen has cancelled their facility booking request.</p>
    
    <div class="info-box info-box-warning">
        <h3>Cancelled Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Citizen:</strong> {{ $booking->applicant_name ?? $booking->user_name }}</p>
        <p><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y - h:i A') }}</p>
        <p><strong>Status:</strong> Cancelled</p>
    </div>
    
    @if($reason)
    <div class="info-box info-box-info">
        <h3>Cancellation Reason</h3>
        <p>{{ $reason }}</p>
    </div>
    @endif
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/staff/bookings') }}" class="button">
            View All Bookings
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px; text-align: center;">
        This is an automated notification from the LGU Facility Reservation System.
    </p>
@endsection

