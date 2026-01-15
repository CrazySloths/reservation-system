<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernmentProgramBooking;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GovernmentProgramController extends Controller
{
    /**
     * Show import dashboard - fetch seminars from Energy Efficiency database
     */
    public function import()
    {
        try {
            // Test connection
            DB::connection('energy_efficiency')->getPdo();
            
            // Get all upcoming seminars from Energy Efficiency system
            $energySeminars = DB::connection('energy_efficiency')
                ->table('seminars')
                ->where('is_archived', 0)
                ->whereDate('seminar_date', '>=', now())
                ->orderBy('seminar_date', 'asc')
                ->get();
            
            // Get already imported seminar IDs
            $importedIds = GovernmentProgramBooking::pluck('source_seminar_id')->toArray();
            
            // Separate into imported and not imported
            $notImported = $energySeminars->filter(function($seminar) use ($importedIds) {
                return !in_array($seminar->seminar_id, $importedIds);
            });
            
            $alreadyImported = $energySeminars->filter(function($seminar) use ($importedIds) {
                return in_array($seminar->seminar_id, $importedIds);
            });
            
            return view('admin.government-programs.import', [
                'notImported' => $notImported,
                'alreadyImported' => $alreadyImported,
                'connectionStatus' => 'connected'
            ]);
            
        } catch (\Exception $e) {
            return view('admin.government-programs.import', [
                'notImported' => collect(),
                'alreadyImported' => collect(),
                'connectionStatus' => 'failed',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Import a single seminar from Energy Efficiency database
     */
    public function importSingle($seminarId)
    {
        try {
            // Get seminar details
            $seminar = DB::connection('energy_efficiency')
                ->table('seminars')
                ->where('seminar_id', $seminarId)
                ->first();
            
            if (!$seminar) {
                return redirect()->back()->with('error', 'Seminar not found in Energy Efficiency database');
            }
            
            // Check if already imported
            $exists = GovernmentProgramBooking::where('source_seminar_id', $seminarId)->first();
            if ($exists) {
                return redirect()->back()->with('error', 'This seminar has already been imported');
            }
            
            // Get creator/organizer (assuming admin created it)
            $creator = DB::connection('energy_efficiency')
                ->table('users')
                ->where('user_role', 'admin')
                ->first();
            
            if (!$creator) {
                $creator = DB::connection('energy_efficiency')
                    ->table('users')
                    ->where('user_role', 'staff')
                    ->first();
            }
            
            // Get expected attendees count
            $expectedAttendees = DB::connection('energy_efficiency')
                ->table('seminar_joins')
                ->where('seminar_id', $seminarId)
                ->count();
            
            // Create government program booking
            $program = GovernmentProgramBooking::create([
                'source_system' => 'Energy Efficiency',
                'source_seminar_id' => $seminarId,
                'source_database' => 'ener_nova_capri',
                'organizer_user_id' => $creator->user_id ?? null,
                'organizer_name' => $creator ? trim("{$creator->first_name} {$creator->last_name}") : 'Energy Efficiency Office',
                'organizer_contact' => $creator->cellphone_number ?? '',
                'organizer_email' => $creator->email ?? '',
                'organizer_area' => $seminar->target_area ?? 'N/A',
                'program_title' => $seminar->seminar_title,
                'program_type' => 'seminar',
                'program_description' => $seminar->description ?? '',
                'event_date' => $seminar->seminar_date,
                'start_time' => $seminar->start_time,
                'end_time' => $seminar->end_time,
                'expected_attendees' => $expectedAttendees > 0 ? $expectedAttendees : 150,
                'requested_location' => $seminar->location ?? 'To be assigned',
                'coordination_status' => 'pending_review',
                'assigned_admin_id' => session('user_id'),
                'assigned_at' => now(),
            ]);
            
            return redirect()->route('admin.government-programs.show', $program->id)
                ->with('success', 'Seminar imported successfully! You can now coordinate and assign a facility.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Bulk import all available seminars
     */
    public function importBulk()
    {
        try {
            $energySeminars = DB::connection('energy_efficiency')
                ->table('seminars')
                ->where('is_archived', 0)
                ->whereDate('seminar_date', '>=', now())
                ->get();
            
            $importedIds = GovernmentProgramBooking::pluck('source_seminar_id')->toArray();
            $imported = 0;
            
            foreach ($energySeminars as $seminar) {
                if (in_array($seminar->seminar_id, $importedIds)) {
                    continue;
                }
                
                $creator = DB::connection('energy_efficiency')
                    ->table('users')
                    ->where('user_role', 'admin')
                    ->first();
                
                $expectedAttendees = DB::connection('energy_efficiency')
                    ->table('seminar_joins')
                    ->where('seminar_id', $seminar->seminar_id)
                    ->count();
                
                GovernmentProgramBooking::create([
                    'source_system' => 'Energy Efficiency',
                    'source_seminar_id' => $seminar->seminar_id,
                    'source_database' => 'ener_nova_capri',
                    'organizer_user_id' => $creator->user_id ?? null,
                    'organizer_name' => $creator ? trim("{$creator->first_name} {$creator->last_name}") : 'Energy Efficiency Office',
                    'organizer_contact' => $creator->cellphone_number ?? '',
                    'organizer_email' => $creator->email ?? '',
                    'organizer_area' => $seminar->target_area ?? 'N/A',
                    'program_title' => $seminar->seminar_title,
                    'program_type' => 'seminar',
                    'program_description' => $seminar->description ?? '',
                    'event_date' => $seminar->seminar_date,
                    'start_time' => $seminar->start_time,
                    'end_time' => $seminar->end_time,
                    'expected_attendees' => $expectedAttendees > 0 ? $expectedAttendees : 150,
                    'requested_location' => $seminar->location ?? 'To be assigned',
                    'coordination_status' => 'pending_review',
                    'assigned_admin_id' => session('user_id'),
                    'assigned_at' => now(),
                ]);
                
                $imported++;
            }
            
            return redirect()->route('admin.government-programs.index')
                ->with('success', "Successfully imported {$imported} seminars!");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk import failed: ' . $e->getMessage());
        }
    }

    /**
     * Show all government program requests
     */
    public function index()
    {
        try {
            // Test connection to Energy Efficiency database
            DB::connection('energy_efficiency')->getPdo();
            $connectionStatus = 'connected';
            
            // Get seminars from Energy Efficiency database
            $energySeminars = DB::connection('energy_efficiency')
                ->table('seminars')
                ->where('is_archived', 0)
                ->orderBy('seminar_date', 'desc')
                ->get();
            
            // Get already imported seminar IDs
            $importedIds = GovernmentProgramBooking::pluck('source_seminar_id')->toArray();
            
            // Get our existing programs
            $programs = GovernmentProgramBooking::with(['assignedFacility', 'assignedAdmin'])
                ->orderBy('event_date', 'desc')
                ->get();
            
            // Separate Energy Efficiency seminars into pending and processed
            $pendingRequests = $energySeminars->filter(function($seminar) use ($importedIds) {
                return !in_array($seminar->seminar_id, $importedIds);
            });
            
            return view('admin.government-programs.index', [
                'programs' => $programs,
                'pendingRequests' => $pendingRequests,
                'connectionStatus' => $connectionStatus,
                'totalPending' => $pendingRequests->count(),
                'totalProcessed' => $programs->count()
            ]);
            
        } catch (\Exception $e) {
            // Connection failed - show only local programs
            $programs = GovernmentProgramBooking::with(['assignedFacility', 'assignedAdmin'])
                ->orderBy('event_date', 'desc')
                ->get();
            
            return view('admin.government-programs.index', [
                'programs' => $programs,
                'pendingRequests' => collect(),
                'connectionStatus' => 'failed',
                'connectionError' => $e->getMessage(),
                'totalPending' => 0,
                'totalProcessed' => $programs->count()
            ]);
        }
    }

    /**
     * Preview a pending seminar request from Energy Efficiency database (not yet imported)
     */
    public function preview($seminarId)
    {
        try {
            // Get seminar details from Energy Efficiency database
            $seminar = DB::connection('energy_efficiency')
                ->table('seminars')
                ->where('seminar_id', $seminarId)
                ->first();
            
            if (!$seminar) {
                abort(404, 'Seminar not found in Energy Efficiency database');
            }
            
            // Get organizer details (if created_by exists in seminar)
            $organizer = null;
            if (isset($seminar->created_by)) {
                $organizer = DB::connection('energy_efficiency')
                    ->table('users')
                    ->where('user_id', $seminar->created_by)
                    ->first();
            }
            
            // If no organizer from created_by, try to get the first admin/staff user as default contact
            if (!$organizer) {
                $organizer = DB::connection('energy_efficiency')
                    ->table('users')
                    ->whereIn('user_role', ['admin', 'staff'])
                    ->first();
            }
            
            // Get expected attendees
            $attendees = DB::connection('energy_efficiency')
                ->table('seminar_joins as sj')
                ->join('users as u', 'sj.user_id', '=', 'u.user_id')
                ->where('sj.seminar_id', $seminarId)
                ->select([
                    'u.user_id',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as name"),
                    'u.email',
                    'u.cellphone_number',
                    'u.area',
                    'sj.joined_at'
                ])
                ->get();
            
            // Get ALL facilities that could accommodate this seminar
            // For government programs, include ALL facilities (even if marked unavailable)
            // Admin can coordinate to make them available for official government use
            $availableFacilities = Facility::where('capacity', '>=', $attendees->count())
                ->orderBy('is_available', 'desc') // Show available ones first
                ->orderBy('capacity', 'asc')
                ->get();
            
            return view('admin.government-programs.preview', compact(
                'seminar',
                'organizer',
                'attendees',
                'availableFacilities'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.government-programs.index')
                ->with('error', 'Failed to load seminar details: ' . $e->getMessage());
        }
    }

    /**
     * Show the acceptance form with facility, equipment, and budget inputs
     */
    public function showAcceptForm($seminarId)
    {
        try {
            // Get seminar details from Energy Efficiency database
            $seminar = DB::connection('energy_efficiency')
                ->table('seminars')
                ->where('seminar_id', $seminarId)
                ->first();
            
            if (!$seminar) {
                abort(404, 'Seminar not found in Energy Efficiency database');
            }

            // Check if already accepted
            $existing = GovernmentProgramBooking::where('source_seminar_id', $seminarId)->first();
            if ($existing) {
                return redirect()->route('admin.government-programs.index')
                    ->with('error', 'This seminar has already been processed');
            }
            
            // Get organizer details
            $organizer = DB::connection('energy_efficiency')
                ->table('users')
                ->whereIn('user_role', ['admin', 'staff'])
                ->first();
            
            // Get expected attendees
            $attendees = DB::connection('energy_efficiency')
                ->table('seminar_joins as sj')
                ->join('users as u', 'sj.user_id', '=', 'u.user_id')
                ->where('sj.seminar_id', $seminarId)
                ->select([
                    'u.user_id',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as name"),
                    'u.email',
                    'u.cellphone_number',
                    'u.area',
                    'sj.joined_at'
                ])
                ->get();
            
            // Get ALL facilities that could accommodate this seminar
            // For government programs, include ALL facilities (even if marked unavailable)
            // Admin can coordinate to make them available for official government use
            $availableFacilities = Facility::where('capacity', '>=', $attendees->count())
                ->orderBy('is_available', 'desc') // Show available ones first
                ->orderBy('capacity', 'asc')
                ->get();
            
            // Get equipment inventory
            $equipmentInventory = DB::connection('facilities_db')
                ->table('equipment_inventory')
                ->select('equipment_id', 'equipment_name', 'category', 'available_quantity', 'total_quantity')
                ->where('available_quantity', '>', 0)
                ->orderBy('category', 'asc')
                ->orderBy('equipment_name', 'asc')
                ->get();
            
            return view('admin.government-programs.accept', compact(
                'seminar',
                'organizer',
                'attendees',
                'availableFacilities',
                'equipmentInventory'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.government-programs.index')
                ->with('error', 'Failed to load acceptance form: ' . $e->getMessage());
        }
    }

    /**
     * Accept a seminar request and assign a facility
     */
    public function accept(Request $request, $seminarId)
    {
        try {
            $validated = $request->validate([
                'facility_id' => 'required|integer',
                'total_budget' => 'required|numeric|min:0',
                'budget_items' => 'required|array|min:1',
                'budget_items.*.item' => 'required|string|max:255',
                'budget_items.*.amount' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validation failed: ' . json_encode($e->errors()));
        }
        
        // Manually check if facility exists
        $facility = Facility::find($request->facility_id);
        if (!$facility) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['facility_id' => 'The selected facility is invalid.']);
        }

        try {
            // Get seminar details from Energy Efficiency database
            $seminar = DB::connection('energy_efficiency')
                ->table('seminars')
                ->where('seminar_id', $seminarId)
                ->first();

            if (!$seminar) {
                return redirect()->route('admin.government-programs.index')
                    ->with('error', 'Seminar not found');
            }

            // Check if already accepted
            $existing = GovernmentProgramBooking::where('source_seminar_id', $seminarId)->first();
            if ($existing) {
                return redirect()->route('admin.government-programs.index')
                    ->with('error', 'This seminar has already been processed');
            }

            // Get attendees count
            $attendeesCount = DB::connection('energy_efficiency')
                ->table('seminar_joins')
                ->where('seminar_id', $seminarId)
                ->count();

            // Get organizer (admin/staff from Energy Efficiency)
            $organizer = DB::connection('energy_efficiency')
                ->table('users')
                ->whereIn('user_role', ['admin', 'staff'])
                ->first();

            // Create Government Program Booking
            // Note: Speakers are provided by Energy Efficiency team, not by LGU
            $programBooking = GovernmentProgramBooking::create([
                'source_system' => 'energy_efficiency',
                'source_seminar_id' => $seminarId,
                'source_database' => 'ener_nova_capri',
                'organizer_user_id' => $organizer ? $organizer->user_id : null,
                'organizer_name' => $organizer ? $organizer->first_name . ' ' . $organizer->last_name : 'Unknown',
                'organizer_contact' => $organizer->cellphone_number ?? 'N/A',
                'organizer_email' => $organizer->email ?? null,
                'organizer_area' => $organizer->area ?? null,
                'program_title' => $seminar->seminar_title,
                'program_type' => 'seminar',
                'program_description' => $seminar->description,
                'event_date' => $seminar->seminar_date,
                'start_time' => $seminar->start_time,
                'end_time' => $seminar->end_time,
                'expected_attendees' => $attendeesCount,
                'requested_location' => $seminar->location,
                'assigned_facility_id' => $facility->facility_id,
                'coordination_status' => 'confirmed',
                'equipment_provided' => $request->equipment ?? [],
                'requested_amount' => $request->total_budget,
                'approved_amount' => $request->total_budget,
                'fund_breakdown' => $request->budget_items,
                'assigned_admin_id' => auth()->id(),
                'assigned_at' => now(),
            ]);

            // Send confirmation back to Energy Efficiency database
            // (In production, this would be via API)
            try {
                \Log::info('Attempting to insert confirmation to Energy Efficiency DB', [
                    'seminar_id' => $seminarId,
                    'facility' => $facility->name,
                    'program_booking_id' => $programBooking->id
                ]);
                
                $insertResult = DB::connection('energy_efficiency')
                    ->table('facility_booking_confirmations')
                    ->insert([
                        'seminar_id' => $seminarId,
                        'public_facilities_tracking_id' => 'GPR-' . now()->format('Y') . '-' . str_pad($programBooking->id, 6, '0', STR_PAD_LEFT),
                        'request_status' => 'confirmed',
                        'assigned_facility_id' => $facility->facility_id,
                        'assigned_facility_name' => $facility->name,
                        'assigned_facility_address' => $facility->address,
                        'assigned_facility_capacity' => $facility->capacity,
                        'facility_fee_charged' => 0.00,
                        'facility_fee_waived' => true,
                        'confirmed_date' => $seminar->seminar_date,
                        'confirmed_start_time' => $seminar->start_time,
                        'confirmed_end_time' => $seminar->end_time,
                        'equipment_provided' => json_encode($request->equipment ?? []),
                        'requested_amount' => $request->total_budget,
                        'approved_amount' => $request->total_budget,
                        'fund_approval_status' => 'approved',
                        'pre_event_budget_breakdown' => json_encode($request->budget_items),
                        'admin_contact_name' => auth()->user()->full_name ?? 'LGU Admin',
                        'admin_contact_email' => auth()->user()->email ?? null,
                        'coordination_notes' => 'Facility assigned by LGU Public Facilities System',
                        'is_published_publicly' => true,
                        'received_at' => now(),
                        'confirmed_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                
                \Log::info('Confirmation inserted successfully', ['result' => $insertResult]);
                
            } catch (\Exception $insertError) {
                \Log::error('Failed to insert confirmation to Energy Efficiency DB', [
                    'error' => $insertError->getMessage(),
                    'trace' => $insertError->getTraceAsString()
                ]);
                // Continue anyway - don't fail the whole operation
            }

            return redirect()->route('admin.government-programs.index')
                ->with('success', 'Seminar request accepted! Facility "' . $facility->name . '" has been assigned.');

        } catch (\Exception $e) {
            \Log::error('Failed to accept request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to accept request: ' . $e->getMessage());
        }
    }

    /**
     * Show single program details
     */
    public function show($id)
    {
        $program = GovernmentProgramBooking::with(['assignedFacility', 'assignedAdmin', 'liquidationItems'])
            ->findOrFail($id);
        
        // Get original seminar data from Energy Efficiency database
        try {
            $seminar = DB::connection('energy_efficiency')
                ->table('seminars')
                ->where('seminar_id', $program->source_seminar_id)
                ->first();
            
            $organizer = DB::connection('energy_efficiency')
                ->table('users')
                ->where('user_id', $program->organizer_user_id)
                ->first();
            
            $registrations = DB::connection('energy_efficiency')
                ->table('seminar_joins as sj')
                ->join('users as u', 'sj.user_id', '=', 'u.user_id')
                ->where('sj.seminar_id', $program->source_seminar_id)
                ->select([
                    'u.user_id',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as name"),
                    'u.email',
                    'u.cellphone_number',
                    'u.area',
                    'sj.joined_at'
                ])
                ->get();
                
        } catch (\Exception $e) {
            $seminar = null;
            $organizer = null;
            $registrations = collect();
        }
        
        // Get available facilities
        $facilities = Facility::where('is_available', true)
            ->orderBy('name', 'asc')
            ->get();
        
        return view('admin.government-programs.show', compact('program', 'seminar', 'organizer', 'registrations', 'facilities'));
    }

    /**
     * Assign facility to program
     */
    public function assignFacility(Request $request, $id)
    {
        $program = GovernmentProgramBooking::findOrFail($id);
        
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
        ]);
        
        $program->update([
            'assigned_facility_id' => $request->facility_id,
            'coordination_status' => 'facility_assigned',
            'is_fee_waived' => true,
        ]);
        
        return redirect()->back()->with('success', 'Facility assigned successfully! Fee waived for government program.');
    }

    /**
     * Update coordination status
     */
    public function updateStatus(Request $request, $id)
    {
        $program = GovernmentProgramBooking::findOrFail($id);
        
        $request->validate([
            'coordination_status' => 'required|in:pending_review,organizer_contacted,speaker_coordinating,fund_requested,fund_approved,facility_assigned,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $program->update([
            'coordination_status' => $request->coordination_status,
            'coordination_notes' => $request->notes,
        ]);
        
        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    /**
     * Sync attendance from Energy Efficiency database
     */
    public function syncAttendance($id)
    {
        $program = GovernmentProgramBooking::findOrFail($id);
        
        try {
            // Get actual attendance count
            $actualAttendance = DB::connection('energy_efficiency')
                ->table('attendance')
                ->where('event_name', 'LIKE', '%' . $program->program_title . '%')
                ->count();
            
            // Get detailed attendance data
            $attendanceDetails = DB::connection('energy_efficiency')
                ->table('attendance as a')
                ->join('users as u', 'a.user_id', '=', 'u.user_id')
                ->where('a.event_name', 'LIKE', '%' . $program->program_title . '%')
                ->select([
                    'a.user_id',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as name"),
                    'u.area',
                    'a.timestamp'
                ])
                ->get();
            
            $program->update([
                'actual_attendees' => $actualAttendance,
                'attendance_data' => $attendanceDetails->toArray(),
            ]);
            
            return redirect()->back()->with('success', "Attendance synced! {$actualAttendance} attendees recorded.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to sync attendance: ' . $e->getMessage());
        }
    }
}

