<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FacilityUtilizationExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->leftJoin('bookings', function ($join) {
                $join->on('facilities.id', '=', 'bookings.facility_id')
                    ->whereBetween(DB::raw('DATE(bookings.created_at)'), [$this->startDate, $this->endDate])
                    ->whereIn('bookings.status', ['approved', 'completed', 'paid']);
            })
            ->select(
                'facilities.id',
                'facilities.name',
                'facilities.capacity',
                DB::raw('COUNT(DISTINCT bookings.id) as total_bookings'),
                DB::raw('COALESCE(SUM(bookings.total_price), 0) as total_revenue'),
                DB::raw('COALESCE(AVG(bookings.total_price), 0) as avg_revenue_per_booking')
            )
            ->where('facilities.is_available', 1)
            ->groupBy('facilities.id', 'facilities.name', 'facilities.capacity')
            ->get();

        return $facilities->map(function ($facility) {
            return [
                'facility_name' => $facility->name,
                'capacity' => $facility->capacity,
                'total_bookings' => $facility->total_bookings,
                'total_revenue' => '₱' . number_format($facility->total_revenue, 2),
                'avg_revenue' => '₱' . number_format($facility->avg_revenue_per_booking, 2),
                'utilization_rate' => number_format(($facility->total_bookings / 30) * 100, 1) . '%', // Simplified calculation
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Facility Name',
            'Capacity',
            'Total Bookings',
            'Total Revenue',
            'Avg Revenue/Booking',
            'Utilization Rate',
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
        return 'Facility Utilization';
    }
}

