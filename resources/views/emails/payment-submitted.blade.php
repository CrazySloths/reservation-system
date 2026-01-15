@extends('emails.layout')

@section('title', 'Payment Received - Under Review')

@section('content')
    <h2>Payment Received - Under Review</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>Thank you! We have received your payment submission and it is now being reviewed by our treasurer.</p>
    
    <div class="info-box info-box-success">
        <h3>Payment Details</h3>
        <p><strong>Payment Slip:</strong> {{ $paymentSlip->slip_number }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Amount Paid:</strong> ₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
        <p><strong>Payment Method:</strong> {{ strtoupper($paymentSlip->payment_channel ?? $paymentSlip->payment_method ?? 'N/A') }}</p>
        @if($paymentSlip->transaction_reference)
        <p><strong>Reference Number:</strong> {{ $paymentSlip->transaction_reference }}</p>
        @endif
    </div>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">What Happens Next?</h3>
    
    <p><strong>1. Treasurer Verification (24-48 hours)</strong><br>
    Our treasurer will verify your payment with our records.</p>
    
    <p><strong>2. Official Receipt Issuance</strong><br>
    Once verified, an Official Receipt will be generated and sent to you.</p>
    
    <p><strong>3. Final Booking Confirmation</strong><br>
    Your booking will be confirmed and locked in our system.</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/reservations/' . $booking->id) }}" class="button">
            View Booking Status
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px;">
        <strong>Note:</strong> You will receive an email notification once your payment has been verified.
    </p>
@endsection

