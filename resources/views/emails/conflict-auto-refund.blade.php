@extends('emails.layout')

@section('title', 'Automatic Refund Processed')

@section('content')
    <h2>Automatic Refund - Booking Conflict</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>This is to inform you that the 7-day response period for your booking conflict has expired. As per our policy, an automatic refund has been processed for your booking.</p>
    
    <div class="info-box info-box-warning">
        <h3>What Happened?</h3>
        <p>A city event was scheduled that conflicted with your booking. You were given 7 days to choose between rescheduling or requesting a refund, but we did not receive your response within the deadline.</p>
        <p><strong>Conflict Created:</strong> {{ \Carbon\Carbon::parse($conflict->created_at)->format('F d, Y h:i A') }}</p>
        <p><strong>Response Deadline:</strong> {{ \Carbon\Carbon::parse($conflict->created_at)->addDays(7)->format('F d, Y h:i A') }}</p>
    </div>
    
    <div class="info-box">
        <h3>Cancelled Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $facility->name }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('l, F d, Y') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
        <p><strong>Status:</strong> Cancelled (Auto-Refund)</p>
    </div>
    
    <div class="info-box info-box-success">
        <h3>Refund Information</h3>
        <p><strong>Amount Paid:</strong> ₱{{ number_format($booking->total_amount, 2) }}</p>
        <p><strong>Refund Amount:</strong> ₱{{ number_format($booking->total_amount, 2) }} (100% Full Refund)</p>
        <p><strong>Processing Time:</strong> 3-7 business days</p>
        <p><strong>Refund Method:</strong> Same as original payment method</p>
    </div>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Next Steps</h3>
    
    <p><strong>Track Your Refund:</strong><br>
    You can track the status of your refund in your transaction history. The refund will typically appear in your account within 3-7 business days.</p>
    
    <p><strong>Book Again:</strong><br>
    If you still need to reserve a facility, you can make a new booking through the citizen portal at any time.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/transactions') }}" class="button">
            View Transaction History
        </a>
        <a href="{{ url('/citizen/facilities') }}" class="button button-secondary" style="margin-left: 10px;">
            Browse Facilities
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px; text-align: center;">
        We apologize for any inconvenience. If you believe this was processed in error, please contact our Facilities Management Office immediately.
    </p>
@endsection

