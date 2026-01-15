@extends('emails.layout')

@section('title', 'URGENT: Payment Deadline in 6 Hours')

@section('content')
    <h2 style="color: #dc2626;">URGENT: Payment Deadline in 6 Hours!</h2>
    
    <p>Dear {{ $booking->applicant_name ?? $booking->user_name }},</p>
    
    <p>This is your <strong>final reminder</strong>. Your payment deadline is in <strong>6 hours</strong>. Please submit payment immediately to avoid losing your booking.</p>
    
    <div class="countdown-timer" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
        <h3>FINAL WARNING</h3>
        <div class="time">6 HOURS LEFT</div>
        <p>Deadline: {{ \Carbon\Carbon::parse($paymentSlip->payment_deadline)->format('F d, Y - h:i A') }}</p>
    </div>
    
    <div class="info-box info-box-error">
        <h3>Booking Will Expire Soon!</h3>
        <p><strong>Payment Slip:</strong> {{ $paymentSlip->slip_number }}</p>
        <p><strong>Facility:</strong> {{ $booking->facility_name }}</p>
        <p><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y - h:i A') }}</p>
        <p><strong>Amount Due:</strong> ₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
    </div>
    
    <p style="color: #dc2626; font-weight: 600;">Without payment in the next 6 hours, your booking will be automatically cancelled and the facility will be available for others to reserve.</p>
    
    <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 18px; color: #0f3d3e;">Pay Immediately</h3>
    
    <p><strong>Cash Payment:</strong> Visit City Treasurer's Office NOW<br>
    <strong>Cashless Payment:</strong> Pay online and submit reference number</p>
    
    <div class="divider"></div>
    
    <p style="text-align: center;">
        <a href="{{ url('/citizen/payments/' . $paymentSlip->id) }}" class="button" style="background-color: #dc2626;">
            PAY NOW - URGENT
        </a>
    </p>
@endsection

