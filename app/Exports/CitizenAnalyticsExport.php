<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CitizenAnalyticsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        $citizens = DB::connection('auth_db')
            ->table('users')
            ->where('role', 'citizen')
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get(['id', 'full_name', 'email', 'created_at']);

        $bookingCounts = DB::connection('facilities_db')
            ->table('bookings')
            ->select('citizen_id', DB::raw('COUNT(*) as booking_count'))
            ->groupBy('citizen_id')
            ->pluck('booking_count', 'citizen_id');

        return $citizens->map(function ($citizen) use ($bookingCounts) {
            return [
                'citizen_id' => $citizen->id,
                'full_name' => $citizen->full_name,
                'email' => $citizen->email,
                'booking_count' => $bookingCounts[$citizen->id] ?? 0,
                'registered_at' => \Carbon\Carbon::parse($citizen->created_at)->format('M d, Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Citizen ID',
            'Full Name',
            'Email',
            'Total Bookings',
            'Registered At',
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
        return 'Citizen Analytics';
    }
}

