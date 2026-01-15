<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CityEvent;
use App\Models\FacilityDb;
use App\Models\GovernmentProgramBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the calendar view
     */
    public function index()
    {
        try {
            $userId = session('user_id');
            
            if (!$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get all facilities for the filter dropdown
            $facilities = FacilityDb::select('facility_id', 'name')
                ->where('is_available', true)
                ->orderBy('name')
                ->get();

            return view('staff.calendar.index', compact('facilities'));
            
        } catch (\Exception $e) {
            \Log::error('Calendar index error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error loading calendar: ' . $e->getMessage());
        }
    }

    /**
     * Get booking events for the calendar (AJAX endpoint)
     * Returns JSON data in FullCalendar format
     */
    public function getEvents(Request $request)
    {
        try {
            $userId = session('user_id');
            
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Get filter parameters
            $facilityId = $request->input('facility_id');
            $status = $request->input('status');
            $start = $request->input('start'); // FullCalendar sends start/end dates
            $end = $request->input('end');

        // Build query for bookings
        $query = Booking::with(['facility.lguCity'])
            // Exclude refunded and rescheduled bookings (replaced by city events)
            ->whereNotIn('status', ['refunded', 'rescheduled']);

        // Filter by date range (if provided by FullCalendar)
        // Use start_time since event_date may be NULL
        if ($start && $end) {
            $query->where(function($q) use ($start, $end) {
                // Clean the date strings - remove any extra whitespace/timezone that might cause parsing issues
                // FullCalendar sends dates like "2025-11-30T00:00:00" or "2025-11-30"
                $startClean = trim(preg_replace('/\s+.*$/', '', $start)); // Remove everything after first space
                $endClean = trim(preg_replace('/\s+.*$/', '', $end));
                
                $startDate = Carbon::parse($startClean)->startOfDay();
                $endDate = Carbon::parse($endClean)->endOfDay();
                
                $q->whereBetween('start_time', [$startDate, $endDate])
                  ->orWhereBetween('end_time', [$startDate, $endDate]);
            });
        }

        // Filter by facility
        if ($facilityId && $facilityId !== 'all') {
            $query->where('facility_id', $facilityId);
        }

        // Filter by status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        } else {
            // Default: Show only approved/confirmed bookings (locked slots)
            $query->whereIn('status', ['staff_verified', 'paid', 'confirmed']);
        }

        $bookings = $query->get();

        // Get city events for the same date range
        $cityEventsQuery = CityEvent::with('facility');
        
        if ($start && $end) {
            $cityEventsQuery->where(function($q) use ($start, $end) {
                $startClean = trim(preg_replace('/\s+.*$/', '', $start));
                $endClean = trim(preg_replace('/\s+.*$/', '', $end));
                
                $startDate = Carbon::parse($startClean)->startOfDay();
                $endDate = Carbon::parse($endClean)->endOfDay();
                
                $q->whereBetween('start_time', [$startDate, $endDate])
                  ->orWhereBetween('end_time', [$startDate, $endDate]);
            });
        }
        
        if ($facilityId && $facilityId !== 'all') {
            $cityEventsQuery->where('facility_id', $facilityId);
        }
        
        $cityEvents = $cityEventsQuery->where('status', 'scheduled')->get();

        // Get government program bookings for the same date range
        $govProgramQuery = GovernmentProgramBooking::with(['assignedFacility.lguCity'])
            ->whereIn('coordination_status', ['confirmed', 'completed']);
        
        if ($start && $end) {
            $govProgramQuery->where(function($q) use ($start, $end) {
                $startClean = trim(preg_replace('/\s+.*$/', '', $start));
                $endClean = trim(preg_replace('/\s+.*$/', '', $end));
                
                $startDate = Carbon::parse($startClean)->startOfDay();
                $endDate = Carbon::parse($endClean)->endOfDay();
                
                $q->whereBetween('event_date', [$startDate, $endDate]);
            });
        }
        
        if ($facilityId && $facilityId !== 'all') {
            $govProgramQuery->where('assigned_facility_id', $facilityId);
        }
        
        $govPrograms = $govProgramQuery->get();

        // Transform bookings to FullCalendar event format
        $events = $bookings->map(function ($booking) {
            // Determine event color based on status
            $color = $this->getEventColor($booking->status);
            
            // Create event object
            return [
                'id' => $booking->id,
                'title' => $booking->facility->name ?? 'Unknown Facility',
                'start' => $booking->start_time->toIso8601String(),
                'end' => $booking->end_time->toIso8601String(),
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'textColor' => $color['text'],
                'extendedProps' => [
                    'bookingId' => 'BK-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                    'facilityName' => $booking->facility->name ?? 'N/A',
                    'cityName' => $booking->facility->lguCity->name ?? $booking->facility->address ?? '',
                    'purpose' => $booking->purpose ?? 'N/A',
                    'attendees' => $booking->expected_attendees,
                    'status' => $booking->status,
                    'statusLabel' => $this->getStatusLabel($booking->status),
                    'userName' => $booking->user_name ?? 'N/A',
                    'startTime' => $booking->start_time->toIso8601String(),
                    'endTime' => $booking->end_time->toIso8601String(),
                ],
            ];
        });

        // Transform city events to FullCalendar event format
        $cityEventsList = $cityEvents->map(function ($cityEvent) {
            return [
                'id' => 'city_event_' . $cityEvent->id,
                'title' => '[CITY EVENT] ' . $cityEvent->event_title,
                'start' => $cityEvent->start_time->toIso8601String(),
                'end' => $cityEvent->end_time->toIso8601String(),
                'backgroundColor' => '#faae2b', // Golden yellow for city events
                'borderColor' => '#00473e',      // Dark teal border
                'textColor' => '#00473e',        // Dark teal text
                'extendedProps' => [
                    'event_id' => $cityEvent->id,
                    'event_type' => 'city_event',
                    'facility_name' => $cityEvent->facility->name ?? 'N/A',
                    'event_description' => $cityEvent->event_description ?? '',
                    'category' => ucfirst($cityEvent->event_type),
                    'affected_bookings' => $cityEvent->affected_bookings_count ?? 0,
                    'status' => $cityEvent->status,
                    'startTime' => $cityEvent->start_time->toIso8601String(),
                    'endTime' => $cityEvent->end_time->toIso8601String(),
                ],
            ];
        });

        // Transform government program bookings to FullCalendar event format
        $govProgramsList = $govPrograms->map(function ($program) {
            // Combine date and time properly
            $eventDate = $program->event_date->format('Y-m-d');
            $startTime = is_string($program->start_time) ? $program->start_time : $program->start_time->format('H:i:s');
            $endTime = is_string($program->end_time) ? $program->end_time : $program->end_time->format('H:i:s');
            
            $startDateTime = Carbon::parse($eventDate . ' ' . $startTime);
            $endDateTime = Carbon::parse($eventDate . ' ' . $endTime);
            
            return [
                'id' => 'gov_program_' . $program->id,
                'title' => '[GOV PROGRAM] ' . $program->program_title,
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'backgroundColor' => '#10b981', // Green for government programs
                'borderColor' => '#059669',      // Darker green border
                'textColor' => '#ffffff',        // White text
                'extendedProps' => [
                    'program_id' => $program->id,
                    'event_type' => 'government_program',
                    'facilityName' => $program->assignedFacility->name ?? 'N/A',
                    'cityName' => $program->assignedFacility->lguCity->name ?? 'N/A',
                    'program_description' => $program->program_description ?? '',
                    'source_system' => $program->source_system,
                    'organizer_name' => $program->organizer_name,
                    'attendees' => $program->expected_attendees,
                    'speakers_count' => $program->number_of_speakers,
                    'approved_budget' => $program->approved_amount,
                    'status' => $program->coordination_status,
                    'startTime' => $startDateTime->toIso8601String(),
                    'endTime' => $endDateTime->toIso8601String(),
                ],
            ];
        });

        // Combine bookings, city events, and government programs
        $allEvents = $events->concat($cityEventsList)->concat($govProgramsList);

            return response()->json($allEvents);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Calendar getEvents error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error as JSON instead of HTML error page
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Get color scheme based on booking status
     * 
     * @param string $status
     * @return array
     */
    private function getEventColor($status)
    {
        return match($status) {
            'pending' => [
                'bg' => '#fbbf24',      // Yellow
                'border' => '#f59e0b',
                'text' => '#78350f'
            ],
            'staff_verified' => [
                'bg' => '#34d399',      // Green (approved, awaiting payment)
                'border' => '#10b981',
                'text' => '#064e3b'
            ],
            'paid', 'confirmed' => [
                'bg' => '#60a5fa',      // Blue (confirmed & paid)
                'border' => '#3b82f6',
                'text' => '#1e3a8a'
            ],
            'rejected', 'cancelled' => [
                'bg' => '#f87171',      // Red
                'border' => '#ef4444',
                'text' => '#7f1d1d'
            ],
            default => [
                'bg' => '#9ca3af',      // Gray
                'border' => '#6b7280',
                'text' => '#1f2937'
            ]
        };
    }

    /**
     * Get human-readable status label
     * 
     * @param string $status
     * @return string
     */
    private function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Pending Verification',
            'staff_verified' => 'Approved (Awaiting Payment)',
            'paid' => 'Paid & Reserved',
            'confirmed' => 'Confirmed',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            default => ucfirst($status)
        };
    }
}

