<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Monthly Summary Report - {{ $selectedMonth->format('F Y') }}</title>
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
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-green {
            color: #0f5b3a;
        }
        .facility-card {
            padding: 10px;
            margin-bottom: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            display: table;
            width: 100%;
        }
        .facility-rank {
            display: table-cell;
            width: 40px;
            font-size: 16px;
            font-weight: bold;
            color: #0f5b3a;
            vertical-align: middle;
            text-align: center;
        }
        .facility-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
        }
        .facility-revenue {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #0f5b3a;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>MONTHLY SUMMARY REPORT</h1>
        <h2>{{ $selectedMonth->format('F Y') }}</h2>
        <p>Monthly Revenue & Statistics • City Treasurer's Office</p>
    </div>

    <!-- Statistics Summary -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Total Revenue</div>
            <div class="value">&#8369;{{ number_format($stats['total_revenue'], 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Transactions</div>
            <div class="value">{{ $stats['total_transactions'] }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Average Amount</div>
            <div class="value">&#8369;{{ number_format($stats['average_transaction'], 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Active Days</div>
            <div class="value">{{ $stats['days_with_collections'] }}</div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="section-title">Payment Method Distribution</div>
    <div class="payment-methods">
        <div class="method-card">
            <div class="method-name">CASH</div>
            <div class="amount">&#8369;{{ number_format($methodBreakdown['cash']['amount'], 2) }}</div>
            <div class="count">{{ $methodBreakdown['cash']['count'] }} trans.</div>
        </div>
        <div class="method-card">
            <div class="method-name">GCASH</div>
            <div class="amount">&#8369;{{ number_format($methodBreakdown['gcash']['amount'], 2) }}</div>
            <div class="count">{{ $methodBreakdown['gcash']['count'] }} trans.</div>
        </div>
        <div class="method-card">
            <div class="method-name">PAYMAYA</div>
            <div class="amount">&#8369;{{ number_format($methodBreakdown['paymaya']['amount'], 2) }}</div>
            <div class="count">{{ $methodBreakdown['paymaya']['count'] }} trans.</div>
        </div>
        <div class="method-card">
            <div class="method-name">BANK</div>
            <div class="amount">&#8369;{{ number_format($methodBreakdown['bank_transfer']['amount'], 2) }}</div>
            <div class="count">{{ $methodBreakdown['bank_transfer']['count'] }} trans.</div>
        </div>
        <div class="method-card">
            <div class="method-name">CARD</div>
            <div class="amount">&#8369;{{ number_format($methodBreakdown['credit_card']['amount'], 2) }}</div>
            <div class="count">{{ $methodBreakdown['credit_card']['count'] }} trans.</div>
        </div>
    </div>

    <!-- Daily Collections Breakdown -->
    <div class="section-title">Daily Collections Summary</div>
    @if($dailyCollections->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">DATE</th>
                <th style="width: 20%;">DAY</th>
                <th style="width: 30%;" class="text-center">TRANSACTIONS</th>
                <th style="width: 30%;" class="text-right">AMOUNT COLLECTED</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyCollections as $date => $data)
            <tr>
                <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($date)->format('l') }}</td>
                <td class="text-center">{{ $data['count'] }}</td>
                <td class="text-right font-bold">&#8369;{{ number_format($data['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right" style="background: #f3f4f6; border-top: 2px solid #d1d5db; padding: 12px 8px; font-weight: bold;">
                    TOTAL:
                </td>
                <td class="text-right text-green" style="background: #f3f4f6; border-top: 2px solid #d1d5db; padding: 12px 8px; font-weight: bold;">
                    &#8369;{{ number_format($stats['total_revenue'], 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
    @else
    <p style="text-align: center; padding: 20px; color: #6b7280;">No daily collections data available.</p>
    @endif

    <!-- Top Facilities -->
    <div class="section-title">Top 5 Revenue-Generating Facilities</div>
    @if($topFacilities->count() > 0)
        @foreach($topFacilities as $facilityName => $data)
        <div class="facility-card">
            <div class="facility-rank">{{ $loop->iteration }}</div>
            <div class="facility-info">
                <div style="font-weight: bold; font-size: 11px;">{{ $facilityName }}</div>
                <div style="font-size: 9px; color: #6b7280;">{{ $data['bookings'] }} booking(s)</div>
            </div>
            <div class="facility-revenue">&#8369;{{ number_format($data['revenue'], 2) }}</div>
        </div>
        @endforeach
    @else
    <p style="text-align: center; padding: 20px; color: #6b7280;">No facility data available for this month.</p>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>LGU Facility Reservation System • City Treasurer's Office</p>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>

