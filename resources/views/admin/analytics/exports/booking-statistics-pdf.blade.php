<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Statistics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #0f3d3e;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #0f3d3e;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Local Government Unit</h1>
        <h2>Booking Statistics Report</h2>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
        <p>Generated: {{ now()->format('F d, Y g:i A') }}</p>
    </div>

    <div class="summary">
        <p><strong>Total Bookings:</strong> {{ $bookings->count() }}</p>
        <p><strong>Total Revenue:</strong> ₱{{ number_format($bookings->sum('total_amount'), 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>User ID</th>
                <th>Facility</th>
                <th>Status</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->id }}</td>
                    <td>{{ $booking->user_id }}</td>
                    <td>{{ $booking->facility_name }}</td>
                    <td>{{ ucfirst($booking->status) }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y g:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->end_time)->format('M d, Y g:i A') }}</td>
                    <td>₱{{ number_format($booking->total_amount ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No bookings found for the selected period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated report from the LGU Public Facilities Reservation System.</p>
        <p>© {{ now()->year }} Local Government Unit. All rights reserved.</p>
    </div>
</body>
</html>

