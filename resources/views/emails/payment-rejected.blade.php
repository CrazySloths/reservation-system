@extends('emails.layout')

@section('title', 'Payment Verification Failed')

@section('content')
    <h2>Payment Verification Failed - Resubmission Required</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>Our treasurer was unable to verify your payment submission. Please review the issue and resubmit the correct payment information.</p>
    
    <div class="info-box info-box-error">
        <h3>Payment Details</h3>
        <p><strong>Payment Slip:</strong> {{ $paymentSlip->slip_number }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Amount Due:</strong> ₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
        <p><strong>Status:</strong> Verification Failed</p>
    </div>
    
    @if($reason)
    <div class="info-box info-box-warning">
        <h3>Reason for Rejection</h3>
        <p>{{ $reason }}</p>
    </div>
    @endif
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Common Issues</h3>
    
    <ul style="margin-left: 20px; margin-bottom: 15px;">
        <li>Incorrect or invalid reference number</li>
        <li>Payment amount does not match the required amount</li>
        <li>Unclear or unreadable payment proof image</li>
        <li>Payment not found in our records</li>
        <li>Duplicate or already-used reference number</li>
    </ul>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">What to Do Next</h3>
    
    <p><strong>1. Check Your Payment Details</strong><br>
    Verify that you entered the correct reference number and amount.</p>
    
    <p><strong>2. Upload Clear Images</strong><br>
    If submitting receipt photos, ensure they are clear and all information is visible.</p>
    
    <p><strong>3. Resubmit Payment Proof</strong><br>
    You have until {{ \Carbon\Carbon::parse($paymentSlip->payment_deadline)->format('F d, Y - h:i A') }} to resubmit.</p>
    
    <div class="info-box info-box-warning">
        <h3>Important Deadline</h3>
        <p>If correct payment proof is not submitted by the deadline, your booking will expire.</p>
    </div>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/payments/' . $paymentSlip->id) }}" class="button">
            Resubmit Payment Proof
        </a>
    </p>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 20px; text-align: center;">
        For assistance, please contact our Treasurer's Office.
    </p>
@endsection

