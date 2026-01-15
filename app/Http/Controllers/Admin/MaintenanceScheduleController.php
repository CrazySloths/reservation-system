<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MaintenanceScheduleController extends Controller
{
    /**
     * Display a listing of maintenance schedules.
     */
    public function index(Request $request)
    {
        $facilityId = $request->input('facility_id');
        $type = $request->input('type');
        $timeFilter = $request->input('time_filter', 'upcoming'); // upcoming, ongoing, past, all

        $query = MaintenanceSchedule::with(['facility', 'creator']);

        // Filter by facility
        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        // Filter by type
        if ($type) {
            $query->where('maintenance_type', $type);
        }

        // Filter by time
        $today = now()->toDateString();
        if ($timeFilter === 'upcoming') {
            $query->where('start_date', '>', $today);
        } elseif ($timeFilter === 'ongoing') {
            $query->where('start_date', '<=', $today)
                  ->where('end_date', '>=', $today);
        } elseif ($timeFilter === 'past') {
            $query->where('end_date', '<', $today);
        }

        $schedules = $query->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get facilities for filter
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Maintenance types
        $maintenanceTypes = [
            'routine' => 'Routine Maintenance',
            'repair' => 'Repair',
            'renovation' => 'Renovation',
            'inspection' => 'Inspection',
            'cleaning' => 'Deep Cleaning',
            'emergency' => 'Emergency Maintenance',
        ];

        // Get counts
        $upcomingCount = MaintenanceSchedule::where('start_date', '>', $today)->count();
        $ongoingCount = MaintenanceSchedule::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();

        return view('admin.maintenance.index', compact(
            'schedules',
            'facilities',
            'maintenanceTypes',
            'facilityId',
            'type',
            'timeFilter',
            'upcomingCount',
            'ongoingCount'
        ));
    }

    /**
     * Show the form for creating a new maintenance schedule.
     */
    public function create()
    {
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $maintenanceTypes = [
            'routine' => 'Routine Maintenance',
            'repair' => 'Repair',
            'renovation' => 'Renovation',
            'inspection' => 'Inspection',
            'cleaning' => 'Deep Cleaning',
            'emergency' => 'Emergency Maintenance',
        ];

        return view('admin.maintenance.create', compact('facilities', 'maintenanceTypes'));
    }

    /**
     * Store a newly created maintenance schedule.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required|exists:facilities_db.facilities,facility_id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'maintenance_type' => 'required|in:routine,repair,renovation,inspection,cleaning,emergency',
            'description' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'is_recurring' => 'boolean',
            'recurring_pattern' => 'nullable|required_if:is_recurring,1|in:daily,weekly,monthly,yearly',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create maintenance schedule
            $schedule = MaintenanceSchedule::create([
                'facility_id' => $request->facility_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'maintenance_type' => $request->maintenance_type,
                'description' => $request->description,
                'notes' => $request->notes,
                'is_recurring' => $request->boolean('is_recurring'),
                'recurring_pattern' => $request->recurring_pattern,
                'created_by' => session('user_id'),
            ]);

            // Check for affected bookings
            $affectedBookings = $schedule->getAffectedBookings();

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'create',
                'model' => 'MaintenanceSchedule',
                'model_id' => $schedule->id,
                'changes' => json_encode([
                    'facility_id' => $schedule->facility_id,
                    'start_date' => $schedule->start_date,
                    'end_date' => $schedule->end_date,
                    'type' => $schedule->maintenance_type,
                    'affected_bookings' => $affectedBookings->count(),
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            $message = 'Maintenance schedule created successfully!';
            if ($affectedBookings->count() > 0) {
                $message .= ' Warning: ' . $affectedBookings->count() . ' existing booking(s) affected.';
            }

            return redirect()->route('admin.maintenance.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Failed to create maintenance schedule: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create maintenance schedule. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified maintenance schedule.
     */
    public function destroy($id)
    {
        try {
            $schedule = MaintenanceSchedule::findOrFail($id);
            
            // Soft delete
            $schedule->delete();

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'delete',
                'model' => 'MaintenanceSchedule',
                'model_id' => $id,
                'changes' => json_encode([
                    'facility' => $schedule->facility->name,
                    'dates' => $schedule->start_date . ' to ' . $schedule->end_date,
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.maintenance.index')
                ->with('success', 'Maintenance schedule cancelled successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to cancel maintenance schedule: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to cancel maintenance schedule. Please try again.');
        }
    }
}

