<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\GovernmentProgramBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacilityCalendarController extends Controller
{
    /**
     * Display the facility calendar with bookings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get user ID from session
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }
        
        // Get selected month/year or default to current
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $selectedFacilityId = $request->get('facility_id');
        
        // Create a Carbon instance for the selected month
        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        // Get all facilities for the filter dropdown
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
        
        // Build the query for bookings in the selected month
        $bookingsQuery = DB::connection('facilities_db')
            ->table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select(
                'bookings.*',
                'facilities.name as facility_name',
                'facilities.address as facility_location'
            )
            ->whereYear('bookings.start_time', $year)
            ->whereMonth('bookings.start_time', $month)
            ->whereIn('bookings.status', ['reserved', 'tentative', 'confirmed', 'staff_verified', 'payment_pending']);
        
        // Filter by facility if selected
        if ($selectedFacilityId) {
            $bookingsQuery->where('bookings.facility_id', $selectedFacilityId);
        }
        
        // Get all bookings for the month
        $bookings = $bookingsQuery->orderBy('bookings.start_time')->get();
        
        // Get city events for the selected month
        $cityEventsQuery = DB::connection('facilities_db')
            ->table('city_events')
            ->join('facilities', 'city_events.facility_id', '=', 'facilities.facility_id')
            ->select(
                'city_events.*',
                'facilities.name as facility_name',
                'facilities.address as facility_location'
            )
            ->whereYear('city_events.start_time', $year)
            ->whereMonth('city_events.start_time', $month);
        
        // Filter by facility if selected
        if ($selectedFacilityId) {
            $cityEventsQuery->where('city_events.facility_id', $selectedFacilityId);
        }
        
        $cityEvents = $cityEventsQuery->orderBy('city_events.start_time')->get();
        
        // Get government program bookings for the selected month
        $govProgramsQuery = DB::connection('facilities_db')
            ->table('government_program_bookings')
            ->join('facilities', 'government_program_bookings.assigned_facility_id', '=', 'facilities.facility_id')
            ->select(
                'government_program_bookings.*',
                'facilities.name as facility_name',
                'facilities.address as facility_location'
            )
            ->where('government_program_bookings.coordination_status', 'confirmed')
            ->whereYear('government_program_bookings.event_date', $year)
            ->whereMonth('government_program_bookings.event_date', $month);
        
        // Filter by facility if selected
        if ($selectedFacilityId) {
            $govProgramsQuery->where('government_program_bookings.assigned_facility_id', $selectedFacilityId);
        }
        
        $govPrograms = $govProgramsQuery->orderBy('government_program_bookings.event_date')->get();
        
        // Group bookings by date
        $bookingsByDate = $bookings->groupBy(function($booking) {
            return Carbon::parse($booking->start_time)->format('Y-m-d');
        });
        
        // Group city events by date
        $cityEventsByDate = $cityEvents->groupBy(function($event) {
            return Carbon::parse($event->start_time)->format('Y-m-d');
        });
        
        // Group government programs by date
        $govProgramsByDate = $govPrograms->groupBy(function($program) {
            return Carbon::parse($program->event_date)->format('Y-m-d');
        });
        
        // Get previous and next month navigation
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();
        
        // Generate calendar data
        $calendarData = $this->generateCalendarData($currentDate, $bookingsByDate, $cityEventsByDate, $govProgramsByDate);
        
        return view('citizen.facility-calendar', [
            'currentDate' => $currentDate,
            'calendarData' => $calendarData,
            'bookings' => $bookings,
            'bookingsByDate' => $bookingsByDate,
            'cityEvents' => $cityEvents,
            'cityEventsByDate' => $cityEventsByDate,
            'govPrograms' => $govPrograms,
            'govProgramsByDate' => $govProgramsByDate,
            'facilities' => $facilities,
            'selectedFacilityId' => $selectedFacilityId,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
        ]);
    }
    
    /**
     * Generate calendar data with booking, city event, and government program information.
     *
     * @param  \Carbon\Carbon  $currentDate
     * @param  \Illuminate\Support\Collection  $bookingsByDate
     * @param  \Illuminate\Support\Collection  $cityEventsByDate
     * @param  \Illuminate\Support\Collection  $govProgramsByDate
     * @return array
     */
    private function generateCalendarData($currentDate, $bookingsByDate, $cityEventsByDate = null, $govProgramsByDate = null)
    {
        $calendarData = [];
        $cityEventsByDate = $cityEventsByDate ?? collect();
        $govProgramsByDate = $govProgramsByDate ?? collect();
        
        // Get the first day of the month
        $firstDay = $currentDate->copy()->startOfMonth();
        $daysInMonth = $currentDate->daysInMonth;
        
        // Get the day of week for the first day (0 = Sunday, 6 = Saturday)
        $firstDayOfWeek = $firstDay->dayOfWeek;
        
        // Add empty cells for days before the first day of the month
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendarData[] = [
                'date' => null,
                'isCurrentMonth' => false,
                'isToday' => false,
                'hasBookings' => false,
                'bookingCount' => 0,
                'hasCityEvents' => false,
                'cityEventCount' => 0,
                'hasGovPrograms' => false,
                'govProgramCount' => 0,
            ];
        }
        
        // Add cells for each day of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $currentDate->copy()->setDay($day);
            $dateString = $date->format('Y-m-d');
            $bookingsForDay = $bookingsByDate->get($dateString, collect());
            $cityEventsForDay = $cityEventsByDate->get($dateString, collect());
            $govProgramsForDay = $govProgramsByDate->get($dateString, collect());
            
            $calendarData[] = [
                'date' => $date,
                'dateString' => $dateString,
                'isCurrentMonth' => true,
                'isToday' => $date->isToday(),
                'isPast' => $date->isPast() && !$date->isToday(),
                'hasBookings' => $bookingsForDay->isNotEmpty(),
                'bookingCount' => $bookingsForDay->count(),
                'bookings' => $bookingsForDay,
                'hasCityEvents' => $cityEventsForDay->isNotEmpty(),
                'cityEventCount' => $cityEventsForDay->count(),
                'cityEvents' => $cityEventsForDay,
                'hasGovPrograms' => $govProgramsForDay->isNotEmpty(),
                'govProgramCount' => $govProgramsForDay->count(),
                'govPrograms' => $govProgramsForDay,
            ];
        }
        
        return $calendarData;
    }
    
    /**
     * Get bookings, city events, and government programs for a specific date (AJAX endpoint).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookingsForDate(Request $request)
    {
        $date = $request->get('date');
        $facilityId = $request->get('facility_id');
        
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }
        
        // Get bookings
        $query = DB::connection('facilities_db')
            ->table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select(
                'bookings.*',
                'facilities.name as facility_name',
                'facilities.address as facility_location'
            )
            ->whereDate('bookings.start_time', $date)
            ->whereIn('bookings.status', ['reserved', 'tentative', 'confirmed', 'staff_verified', 'payment_pending'])
            ->orderBy('bookings.start_time');
        
        if ($facilityId) {
            $query->where('bookings.facility_id', $facilityId);
        }
        
        $bookings = $query->get();
        
        // Get city events
        $cityEventsQuery = DB::connection('facilities_db')
            ->table('city_events')
            ->join('facilities', 'city_events.facility_id', '=', 'facilities.facility_id')
            ->select(
                'city_events.*',
                'facilities.name as facility_name',
                'facilities.address as facility_location'
            )
            ->whereDate('city_events.start_time', $date)
            ->orderBy('city_events.start_time');
        
        if ($facilityId) {
            $cityEventsQuery->where('city_events.facility_id', $facilityId);
        }
        
        $cityEvents = $cityEventsQuery->get();
        
        // Get government programs
        $govProgramsQuery = DB::connection('facilities_db')
            ->table('government_program_bookings')
            ->join('facilities', 'government_program_bookings.assigned_facility_id', '=', 'facilities.facility_id')
            ->select(
                'government_program_bookings.*',
                'facilities.name as facility_name',
                'facilities.address as facility_location'
            )
            ->where('government_program_bookings.coordination_status', 'confirmed')
            ->whereDate('government_program_bookings.event_date', $date)
            ->orderBy('government_program_bookings.event_date');
        
        if ($facilityId) {
            $govProgramsQuery->where('government_program_bookings.assigned_facility_id', $facilityId);
        }
        
        $govPrograms = $govProgramsQuery->get();
        
        return response()->json([
            'date' => $date,
            'bookings' => $bookings,
            'cityEvents' => $cityEvents,
            'govPrograms' => $govPrograms,
        ]);
    }
}

