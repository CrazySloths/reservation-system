@extends('emails.layout')

@section('title', 'Booking Conflict Notice')

@section('content')
    <h2>Important: City Event Affects Your Booking</h2>
    
    <p>Dear {{ $citizen->first_name }} {{ $citizen->last_name }},</p>
    
    <p>We need to inform you that a city-wide event has been scheduled that affects your existing booking reservation.</p>
    
    <div class="info-box info-box-warning">
        <h3>Your Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $facility->name }}</p>
        <p><strong>Your Reserved Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('l, F d, Y') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
    </div>
    
    <div class="info-box info-box-error">
        <h3>Conflicting City Event</h3>
        <p><strong>Event:</strong> {{ $cityEvent->event_title }}</p>
        <p><strong>Type:</strong> {{ ucfirst($cityEvent->event_type) }}</p>
        <p><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($cityEvent->start_time)->format('l, F d, Y') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($cityEvent->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($cityEvent->end_time)->format('h:i A') }}</p>
        @if($cityEvent->event_description)
        <p><strong>Description:</strong> {{ $cityEvent->event_description }}</p>
        @endif
    </div>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Action Required</h3>
    
    <p>You have <strong>7 days</strong> from today to choose one of the following options:</p>
    
    <ul style="margin-left: 20px; margin-bottom: 15px;">
        <li><strong>Reschedule:</strong> Choose a new date and time for your booking</li>
        <li><strong>Request Refund:</strong> Cancel your booking and receive a full refund</li>
    </ul>
    
    <div class="countdown-timer">
        <h3>Response Deadline</h3>
        <p style="font-size: 18px; margin: 10px 0;">{{ \Carbon\Carbon::parse($conflict->created_at)->addDays(7)->format('l, F d, Y') }}</p>
        <p style="font-size: 14px; margin: 5px 0;">{{ \Carbon\Carbon::parse($conflict->created_at)->addDays(7)->format('h:i A') }}</p>
    </div>
    
    <p><strong>What happens if I don't respond?</strong><br>
    If we don't receive your choice within 7 days, your booking will be automatically cancelled and a full refund will be processed.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/booking-conflicts/' . $conflict->id) }}" class="button">
            View Conflict & Choose Option
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px; text-align: center;">
        We apologize for any inconvenience this may cause. For questions, please contact our Facilities Management Office.
    </p>
@endsection

