@extends('emails.layout')

@section('title', 'Booking Conflict Resolved')

@section('content')
    <h2>Your Booking Conflict Has Been Resolved</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>Thank you for responding to the booking conflict notice. Your choice has been processed successfully.</p>
    
    <div class="info-box info-box-success">
        <h3>Your Choice: {{ ucfirst($choice) }}</h3>
        <p><strong>Status:</strong> Resolved</p>
        <p><strong>Processed On:</strong> {{ \Carbon\Carbon::parse($conflict->resolved_at)->format('F d, Y h:i A') }}</p>
    </div>
    
    @if($choice === 'reschedule')
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Next Steps - Rescheduling</h3>
    
    <p>Your original booking has been marked for rescheduling. Please follow these steps:</p>
    
    <ol style="margin-left: 20px; margin-bottom: 15px;">
        <li>Log in to your citizen portal</li>
        <li>Go to "My Bookings"</li>
        <li>Select the booking that needs rescheduling</li>
        <li>Choose "Reschedule" and select your new preferred date/time</li>
    </ol>
    
    <div class="info-box">
        <h3>Original Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $facility->name }}</p>
        <p><strong>Original Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('l, F d, Y') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
    </div>
    
    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ url('/citizen/reservations') }}" class="button">
            Go to My Bookings
        </a>
    </p>
    
    @else
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Refund Information</h3>
    
    <p>Your refund request has been approved. A full refund will be processed within 3-7 business days.</p>
    
    <div class="info-box">
        <h3>Refund Details</h3>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
        <p><strong>Facility:</strong> {{ $facility->name }}</p>
        <p><strong>Amount Paid:</strong> ₱{{ number_format($booking->total_amount, 2) }}</p>
        <p><strong>Refund Amount:</strong> ₱{{ number_format($booking->total_amount, 2) }}</p>
        <p><strong>Processing Time:</strong> 3-7 business days</p>
        
        @if($conflict->refund_method)
        <p style="margin-top: 15px;"><strong>Refund Method:</strong> 
            @if($conflict->refund_method === 'cash')
                Cash - Pick up at City Treasurer's Office
            @elseif($conflict->refund_method === 'gcash')
                GCash
            @elseif($conflict->refund_method === 'paymaya')
                PayMaya
            @elseif($conflict->refund_method === 'bank_transfer')
                Bank Transfer
            @endif
        </p>
        
        @if($conflict->refund_method !== 'cash')
        <p><strong>Account Name:</strong> {{ $conflict->refund_account_name }}</p>
        <p><strong>
            @if($conflict->refund_method === 'gcash' || $conflict->refund_method === 'paymaya')
                Mobile Number:
            @else
                Account Number:
            @endif
        </strong> {{ $conflict->refund_account_number }}</p>
        
        @if($conflict->refund_method === 'bank_transfer' && $conflict->refund_bank_name)
        <p><strong>Bank Name:</strong> {{ $conflict->refund_bank_name }}</p>
        @endif
        @endif
        @endif
    </div>
    
    <p><strong>How will I receive my refund?</strong><br>
    @if($conflict->refund_method === 'cash')
        Your refund will be available for pick-up at the City Treasurer's Office. Please bring a valid ID and your booking reference number.
    @else
        The refund will be transferred directly to your registered {{ ucfirst($conflict->refund_method) }} account. You will receive a confirmation email once the refund has been completed.
    @endif
    </p>
    
    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ url('/citizen/transactions') }}" class="button">
            View Transaction History
        </a>
    </p>
    @endif
    
    <div class="divider"></div>
    
    <p style="font-size: 14px; color: #6b7280; text-align: center;">
        Thank you for your understanding. For questions or concerns, please contact our Facilities Management Office.
    </p>
@endsection

