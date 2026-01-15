<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Display all bookings/reservations for the logged-in citizen.
     */
    public function index(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $status = $request->get('status', 'all');
        $search = $request->get('search');

        // Base query - My Reservations should only show ACTIVE bookings (not closed/historical ones)
        $query = DB::connection('facilities_db')
            ->table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select(
                'bookings.*',
                'facilities.name as facility_name',
                'facilities.address as facility_address',
                'facilities.image_path as facility_image',
                'lgu_cities.city_name',
                'lgu_cities.city_code'
            )
            ->where('bookings.user_id', $userId)
            // EXCLUDE completed, expired, cancelled, rejected from "My Reservations"
            ->whereNotIn('bookings.status', ['completed', 'expired', 'cancelled', 'rejected'])
            ->orderBy('bookings.start_time', 'desc');

        // Filter by status
        if ($status !== 'all') {
            if ($status === 'active') {
                $query->whereIn('bookings.status', ['pending', 'staff_verified', 'payment_pending', 'confirmed']);
            } elseif ($status === 'completed') {
                $query->where('bookings.status', 'completed');
            } else {
                $query->where('bookings.status', $status);
            }
        }

        // Search by facility name or booking reference
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('facilities.name', 'like', "%{$search}%")
                  ->orWhere('bookings.id', 'like', "%{$search}%")
                  ->orWhere('bookings.purpose', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(10);

        // Get counts for filter badges - only count ACTIVE bookings
        $statusCounts = [
            'all' => DB::connection('facilities_db')->table('bookings')
                ->where('user_id', $userId)
                ->whereNotIn('status', ['completed', 'expired', 'cancelled', 'rejected'])
                ->count(),
            'active' => DB::connection('facilities_db')->table('bookings')
                ->where('user_id', $userId)
                ->whereIn('status', ['pending', 'staff_verified', 'payment_pending', 'confirmed'])
                ->count(),
            'completed' => DB::connection('facilities_db')->table('bookings')
                ->where('user_id', $userId)
                ->where('status', 'completed')
                ->count(),
        ];

        return view('citizen.reservations.index', compact('bookings', 'status', 'search', 'statusCounts'));
    }

    /**
     * Display details of a specific booking.
     */
    public function show($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Use Booking model instead of raw query to access model methods
        $booking = \App\Models\Booking::with(['facility.lguCity'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            return redirect()->route('citizen.reservations')->with('error', 'Booking not found.');
        }

        // Add facility details as properties for backward compatibility with view
        $booking->facility_name = $booking->facility->name ?? 'N/A';
        $booking->facility_description = $booking->facility->description ?? '';
        $booking->facility_address = $booking->facility->address ?? '';
        $booking->facility_capacity = $booking->facility->capacity ?? 0;
        $booking->facility_image = $booking->facility->image_path ?? '';
        $booking->city_name = $booking->facility->lguCity->name ?? '';
        $booking->city_code = $booking->facility->lguCity->code ?? '';

        // Get selected equipment
        $equipment = DB::connection('facilities_db')
            ->table('booking_equipment')
            ->join('equipment_items', 'booking_equipment.equipment_item_id', '=', 'equipment_items.id')
            ->select('booking_equipment.*', 'equipment_items.name as equipment_name', 'equipment_items.category')
            ->where('booking_equipment.booking_id', $id)
            ->get();

        // Get payment slip if exists
        $paymentSlip = DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('booking_id', $id)
            ->first();

        // Check if booking has passed and if review exists
        $eventHasPassed = Carbon::parse($booking->end_time)->isPast();
        $existingReview = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('booking_id', $id)
            ->first();
        $canReview = $eventHasPassed && !$existingReview && in_array($booking->status, ['confirmed', 'completed']);

        return view('citizen.reservations.show', compact('booking', 'equipment', 'paymentSlip', 'canReview', 'existingReview'));
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, $id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $booking = DB::connection('facilities_db')
            ->table('bookings')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            return redirect()->route('citizen.reservations')->with('error', 'Booking not found.');
        }

        // Check if booking can be cancelled (only pending, staff_verified, payment_pending)
        if (!in_array($booking->status, ['pending', 'staff_verified', 'payment_pending'])) {
            return redirect()->back()->with('error', 'This booking cannot be cancelled at this stage.');
        }

        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update booking status
        DB::connection('facilities_db')
            ->table('bookings')
            ->where('id', $id)
            ->update([
                'status' => 'cancelled',
                'rejected_reason' => $request->cancellation_reason,
                'updated_at' => Carbon::now(),
            ]);

        // Send notification to all staff members
        try {
            // Get booking details with facility info
            $bookingDetails = DB::connection('facilities_db')
                ->table('bookings')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->where('bookings.id', $id)
                ->selectRaw('bookings.*, facilities.name as facility_name, CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference')
                ->first();

            if ($bookingDetails) {
                // Get all staff members (subsystem_role_id = 3 for Reservations Staff, subsystem_id = 4 for Public Facilities)
                $staffUsers = \App\Models\User::where('subsystem_role_id', 3)
                                              ->where('subsystem_id', 4)
                                              ->get();

                foreach ($staffUsers as $staff) {
                    $staff->notify(new \App\Notifications\BookingCancelled($bookingDetails, $request->cancellation_reason));
                }

                \Log::info('Cancellation notifications sent to ' . $staffUsers->count() . ' staff members', [
                    'booking_id' => $id
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send booking cancellation notification: ' . $e->getMessage());
        }

        return redirect()->route('citizen.reservations')->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Upload additional documents for a booking.
     */
    public function uploadDocument(Request $request, $id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $booking = DB::connection('facilities_db')
            ->table('bookings')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'document_type' => 'required|in:valid_id,special_discount_id,supporting_doc',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Handle file upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('booking_documents/' . $request->document_type, 'public');
        }

        // Update the corresponding field
        $fieldMap = [
            'valid_id' => 'valid_id_path',
            'special_discount_id' => 'special_discount_id_path',
            'supporting_doc' => 'supporting_doc_path',
        ];

        $field = $fieldMap[$request->document_type];

        DB::connection('facilities_db')
            ->table('bookings')
            ->where('id', $id)
            ->update([
                $field => $documentPath,
                'updated_at' => Carbon::now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Document uploaded successfully.', 'path' => $documentPath]);
    }

    /**
     * Display reservation history (completed and cancelled).
     */
    public function history(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $search = $request->get('search');

        $query = DB::connection('facilities_db')
            ->table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select(
                'bookings.*',
                'facilities.name as facility_name',
                'facilities.address as facility_address',
                'facilities.image_path as facility_image',
                'lgu_cities.city_name',
                'lgu_cities.city_code'
            )
            ->where('bookings.user_id', $userId)
            ->whereIn('bookings.status', ['completed', 'cancelled', 'rejected', 'expired'])
            ->orderBy('bookings.start_time', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('facilities.name', 'like', "%{$search}%")
                  ->orWhere('bookings.id', 'like', "%{$search}%")
                  ->orWhere('bookings.purpose', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(15);

        return view('citizen.reservations.history', compact('bookings', 'search'));
    }
}

