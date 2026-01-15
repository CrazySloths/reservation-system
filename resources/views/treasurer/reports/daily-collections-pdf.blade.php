<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daily Collections Report - {{ $selectedDate->format('F d, Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0f5b3a;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #0f5b3a;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 18px;
            color: #374151;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 12px;
            color: #6b7280;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .stat-card .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #0f5b3a;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f5b3a;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }
        .payment-methods {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .method-card {
            display: table-cell;
            width: 20%;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .method-card .method-name {
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        .method-card .amount {
            font-size: 14px;
            font-weight: bold;
            color: #0f5b3a;
        }
        .method-card .count {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead th {
            background: #f3f4f6;
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #d1d5db;
        }
        tbody td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-green {
            color: #0f5b3a;
        }
        tfoot td {
            padding: 12px 8px;
            background: #f3f4f6;
            border-top: 2px solid #d1d5db;
            font-size: 11px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
        }
        .method-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-cash { background: #d1fae5; color: #065f46; }
        .badge-gcash { background: #dbeafe; color: #1e40af; }
        .badge-paymaya { background: #e9d5ff; color: #6b21a8; }
        .badge-bank { background: #fef3c7; color: #92400e; }
        .badge-card { background: #fce7f3; color: #9f1239; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>DAILY COLLECTIONS REPORT</h1>
        <h2>{{ $selectedDate->format('F d, Y') }}</h2>
        <p>{{ $selectedDate->format('l') }} • City Treasurer's Office</p>
    </div>

    <!-- Statistics Summary -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Total Collections</div>
            <div class="value">&#8369;{{ number_format($stats['total_collections'], 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Transactions</div>
            <div class="value">{{ $stats['total_transactions'] }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Average Amount</div>
            <div class="value">
                &#8369;{{ $stats['total_transactions'] > 0 ? number_format($stats['total_collections'] / $stats['total_transactions'], 2) : '0.00' }}
            </div>
        </div>
        <div class="stat-card">
            <div class="label">Cash Collections</div>
            <div class="value">&#8369;{{ number_format($stats['cash'], 2) }}</div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="section-title">Payment Method Breakdown</div>
    <div class="payment-methods">
        <div class="method-card">
            <div class="method-name">CASH</div>
            <div class="amount">&#8369;{{ number_format($stats['cash'], 2) }}</div>
            <div class="count">{{ $stats['cash_count'] }} trans.</div>
        </div>
        <div class="method-card">
            <div class="method-name">GCASH</div>
            <div class="amount">&#8369;{{ number_format($stats['gcash'], 2) }}</div>
            <div class="count">{{ $stats['gcash_count'] }} trans.</div>
        </div>
        <div class="method-card">
            <div class="method-name">PAYMAYA</div>
            <div class="amount">&#8369;{{ number_format($stats['paymaya'], 2) }}</div>
            <div class="count">{{ $stats['paymaya_count'] }} trans.</div>
        </div>
        <div class="method-card">
            <div class="method-name">BANK</div>
            <div class="amount">&#8369;{{ number_format($stats['bank_transfer'], 2) }}</div>
            <div class="count">{{ $stats['bank_transfer_count'] }} trans.</div>
        </div>
        <div class="method-card">
            <div class="method-name">CARD</div>
            <div class="amount">&#8369;{{ number_format($stats['credit_card'], 2) }}</div>
            <div class="count">{{ $stats['credit_card_count'] }} trans.</div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="section-title">Transaction Details</div>
    @if($payments->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">TIME</th>
                <th style="width: 15%;">OR NUMBER</th>
                <th style="width: 25%;">PAYOR</th>
                <th style="width: 22%;">FACILITY</th>
                <th style="width: 15%;">METHOD</th>
                <th style="width: 15%;" class="text-right">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('h:i A') }}</td>
                <td class="font-bold text-green">{{ $payment->transaction_reference }}</td>
                <td>{{ $payment->applicant_name }}</td>
                <td>{{ $payment->facility_name }}</td>
                <td>
                    @php
                        $badgeClass = [
                            'cash' => 'badge-cash',
                            'gcash' => 'badge-gcash',
                            'paymaya' => 'badge-paymaya',
                            'bank_transfer' => 'badge-bank',
                            'credit_card' => 'badge-card',
                        ][$payment->payment_method] ?? '';
                    @endphp
                    <span class="method-badge {{ $badgeClass }}">
                        {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}
                    </span>
                </td>
                <td class="text-right font-bold">&#8369;{{ number_format($payment->amount_due, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">TOTAL COLLECTIONS:</td>
                <td class="text-right text-green">&#8369;{{ number_format($stats['total_collections'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
    <p style="text-align: center; padding: 40px; color: #6b7280;">
        No collections recorded for this date.
    </p>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>LGU Facility Reservation System • City Treasurer's Office</p>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>

