<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Official Receipt - {{ $paymentSlip->transaction_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0f5b3a;
        }
        
        .header h1 {
            font-size: 24px;
            color: #0f5b3a;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 11px;
            color: #666;
        }
        
        .or-number {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f0f9ff;
            border: 2px solid #0f5b3a;
            border-radius: 8px;
        }
        
        .or-number h3 {
            font-size: 20px;
            color: #0f5b3a;
            margin-bottom: 5px;
        }
        
        .or-number p {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f5b3a;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 8px 10px;
            font-weight: bold;
            color: #666;
            background-color: #f9fafb;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 10px;
            color: #333;
        }
        
        .amount-section {
            margin: 30px 0;
            padding: 20px;
            background-color: #f0f9ff;
            border: 2px solid #0f5b3a;
            border-radius: 8px;
        }
        
        .amount-section .label {
            font-size: 14px;
            font-weight: bold;
            color: #0f5b3a;
            margin-bottom: 5px;
        }
        
        .amount-section .amount {
            font-size: 28px;
            font-weight: bold;
            color: #0f5b3a;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }
        
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin: 40px 30px 5px 30px;
        }
        
        .signature-label {
            font-size: 11px;
            color: #666;
        }
        
        .footer-note {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    
    <!-- Header -->
    <div class="header">
        <h1>LOCAL GOVERNMENT UNIT</h1>
        <h2>City Treasurer's Office</h2>
        <p>LGU1 City, Philippines</p>
        <p>Tel: (02) 1234-5678 | Email: treasurer@lgu1.gov.ph</p>
    </div>
    
    <!-- Official Receipt Number -->
    <div class="or-number">
        <h3>OFFICIAL RECEIPT</h3>
        <p>{{ $paymentSlip->transaction_reference }}</p>
    </div>
    
    <!-- Receipt Information -->
    <div class="section">
        <div class="section-title">Receipt Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Payment Slip #:</div>
                <div class="info-value">{{ $paymentSlip->slip_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date Issued:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($paymentSlip->paid_at)->format('F d, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Time:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($paymentSlip->paid_at)->format('h:i A') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Method:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $paymentSlip->payment_method)) }}</div>
            </div>
        </div>
    </div>
    
    <!-- Payor Information -->
    <div class="section">
        <div class="section-title">Received From</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value">{{ $paymentSlip->applicant_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $paymentSlip->applicant_email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $paymentSlip->applicant_phone }}</div>
            </div>
        </div>
    </div>
    
    <!-- Facility Booking Details -->
    <div class="section">
        <div class="section-title">Payment For</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Service:</div>
                <div class="info-value">Public Facility Reservation</div>
            </div>
            <div class="info-row">
                <div class="info-label">Facility:</div>
                <div class="info-value">{{ $paymentSlip->facility_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $paymentSlip->facility_address }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Event Date:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('F d, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Time:</div>
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('h:i A') }} - 
                    {{ \Carbon\Carbon::parse($paymentSlip->end_time)->format('h:i A') }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Purpose:</div>
                <div class="info-value">{{ $paymentSlip->purpose }}</div>
            </div>
        </div>
    </div>
    
    <!-- Amount Paid -->
    <div class="amount-section">
        <div class="label">AMOUNT PAID:</div>
        <div class="amount">&#8369;{{ number_format($paymentSlip->amount_due, 2) }}</div>
    </div>
    
    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Payor's Signature</div>
            @if($paymentSlip->applicant_name)
                <p style="margin-top: 5px; font-size: 10px; font-weight: bold;">{{ $paymentSlip->applicant_name }}</p>
            @endif
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Authorized Signature</div>
            @if($paymentSlip->verified_by_name)
                <p style="margin-top: 5px; font-size: 10px; font-weight: bold;">{{ $paymentSlip->verified_by_name }}</p>
            @endif
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer-note">
        <p>This is an official receipt issued by the City Treasurer's Office.</p>
        <p>For inquiries, please contact the City Treasurer's Office at (02) 1234-5678.</p>
        <p style="margin-top: 10px;">Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>
    
</body>
</html>

