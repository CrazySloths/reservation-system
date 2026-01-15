<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingStatisticsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $bookings = DB::connection('facilities_db')
            ->table('bookings')
            ->leftJoin('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->selectRaw('
                bookings.id,
                bookings.user_id,
                facilities.name as facility_name,
                bookings.status,
                bookings.start_time,
                bookings.end_time,
                bookings.total_amount,
                bookings.created_at
            ')
            ->whereBetween(DB::raw('DATE(bookings.created_at)'), [$this->startDate, $this->endDate])
            ->orderBy('bookings.created_at', 'desc')
            ->get();

        return $bookings->map(function ($booking) {
            return [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'facility_name' => $booking->facility_name,
                'status' => ucfirst($booking->status),
                'start_time' => \Carbon\Carbon::parse($booking->start_time)->format('M d, Y g:i A'),
                'end_time' => \Carbon\Carbon::parse($booking->end_time)->format('M d, Y g:i A'),
                'total_amount' => '₱' . number_format($booking->total_amount ?? 0, 2),
                'created_at' => \Carbon\Carbon::parse($booking->created_at)->format('M d, Y g:i A'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Booking ID',
            'User ID',
            'Facility',
            'Status',
            'Start Time',
            'End Time',
            'Total Amount',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Booking Statistics';
    }
}

