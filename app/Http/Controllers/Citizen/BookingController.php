<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    /**
     * Show Step 1: Select facility and date/time
     */
    public function create($facilityId = null)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get user data with city information
        $user = DB::connection('auth_db')
            ->table('users')
            ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
            ->select('users.*', 'cities.name as city_name', 'cities.code as city_code')
            ->where('users.id', $userId)
            ->first();

        // Check if returning from Step 2 (session data exists)
        $sessionData = session('booking_step1');
        
        // Get facility if provided or from session
        $facility = null;
        $effectiveFacilityId = $facilityId ?? ($sessionData['facility_id'] ?? null);
        
        if ($effectiveFacilityId) {
            $facility = DB::connection('facilities_db')
                ->table('facilities')
                ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
                ->select('facilities.*', 'lgu_cities.city_name', 'lgu_cities.city_code')
                ->where('facilities.facility_id', $effectiveFacilityId)
                ->whereNull('facilities.deleted_at')
                ->first();

            if (!$facility) {
                return redirect()->route('citizen.browse-facilities')
                    ->with('error', 'Facility not found.');
            }
        }

        // Get all facilities for dropdown
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('citizen.booking.step1-select-datetime', compact('facility', 'facilities', 'user'));
    }

    /**
     * Show Step 2: Select equipment and add-ons
     */
    public function step2(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get facility details first to validate capacity
        $facility = DB::connection('facilities_db')
            ->table('facilities')
            ->where('facility_id', $request->facility_id)
            ->first();

        if (!$facility) {
            return redirect()->back()->with('error', 'Facility not found.');
        }

        // Validate step 1 data with dynamic capacity constraints
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities_db.facilities,facility_id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:500',
            'expected_attendees' => [
                'required',
                'integer',
                'min:' . ($facility->min_capacity ?? 1),
                'max:' . ($facility->capacity ?? 1000),
            ],
        ], [
            'expected_attendees.min' => 'This facility requires a minimum of ' . number_format($facility->min_capacity ?? 1) . ' attendees.',
            'expected_attendees.max' => 'This facility can accommodate a maximum of ' . number_format($facility->capacity ?? 1000) . ' attendees.',
        ]);
        
        // Additional validation: Ensure booking doesn't cross midnight (same-day only)
        $startDateTime = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->start_time);
        $endDateTime = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->end_time);
        
        if ($endDateTime->lte($startDateTime)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'End time must be on the same day as start time. Bookings cannot cross midnight.');
        }
        
        // Additional validation: Check operating hours (8:00 AM to 10:00 PM)
        $operatingStart = \Carbon\Carbon::parse($request->booking_date . ' 08:00:00');
        $operatingEnd = \Carbon\Carbon::parse($request->booking_date . ' 22:00:00');
        
        if ($startDateTime->lt($operatingStart) || $endDateTime->gt($operatingEnd)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bookings must be within operating hours: 8:00 AM to 10:00 PM.');
        }

        // Store step 1 data in session
        session(['booking_step1' => $validated]);

        // Get requested booking date and facility
        $bookingDate = $validated['booking_date'];
        $facilityId = $validated['facility_id'];

        // Get all bookings on the SAME DATE across ALL FACILITIES
        // Equipment is shared across all facilities - if LED TVs are booked at one facility,
        // they can't be used at another facility on the same day
        $bookingsOnSameDate = DB::connection('facilities_db')
            ->table('bookings')
            ->whereDate('start_time', $bookingDate)
            ->whereIn('status', ['pending', 'staff_verified', 'reserved', 'payment_pending', 'confirmed', 'paid'])
            ->pluck('id')
            ->toArray();

        // Get equipment used in bookings on the same date
        $unavailableEquipmentQty = [];
        if (!empty($bookingsOnSameDate)) {
            $usedEquipment = DB::connection('facilities_db')
                ->table('booking_equipment')
                ->whereIn('booking_id', $bookingsOnSameDate)
                ->select('equipment_item_id', DB::raw('SUM(quantity) as total_used'))
                ->groupBy('equipment_item_id')
                ->get();

            foreach ($usedEquipment as $used) {
                $unavailableEquipmentQty[$used->equipment_item_id] = $used->total_used;
            }
        }

        // Get equipment items with adjusted availability
        $equipmentRaw = DB::connection('facilities_db')
            ->table('equipment_items')
            ->where('is_available', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // Adjust available quantity based on same-day bookings
        foreach ($equipmentRaw as $item) {
            $usedQty = $unavailableEquipmentQty[$item->id] ?? 0;
            $item->quantity_available_now = max(0, $item->quantity_available - $usedQty);
        }

        // Filter out items with zero availability and group by category
        $equipment = $equipmentRaw
            ->filter(function($item) {
                return $item->quantity_available_now > 0;
            })
            ->groupBy('category');

        // Calculate pricing
        $pricing = $this->calculatePricing($validated, []);

        return view('citizen.booking.step2-select-equipment', compact('facility', 'equipment', 'pricing'));
    }

    /**
     * Show Step 3: Apply discounts and review
     */
    public function step3(Request $request)
    {
        $userId = session('user_id');
        $userName = session('user_name');
        $userEmail = session('user_email');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get step 1 data from session
        $step1Data = session('booking_step1');
        if (!$step1Data) {
            return redirect()->route('citizen.booking.create')
                ->with('error', 'Please start from step 1.');
        }

        // Get selected equipment
        $selectedEquipment = $request->input('equipment', []);
        session(['booking_equipment' => $selectedEquipment]);

        // Get facility
        $facility = DB::connection('facilities_db')
            ->table('facilities')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select('facilities.*', 'lgu_cities.city_name', 'lgu_cities.city_code')
            ->where('facilities.facility_id', $step1Data['facility_id'])
            ->first();

        // Get equipment details
        $equipmentDetails = [];
        if (!empty($selectedEquipment)) {
            foreach ($selectedEquipment as $equipId => $quantity) {
                if ($quantity > 0) {
                    $item = DB::connection('facilities_db')
                        ->table('equipment_items')
                        ->where('id', $equipId)
                        ->first();
                    if ($item) {
                        $equipmentDetails[] = [
                            'item' => $item,
                            'quantity' => $quantity,
                            'subtotal' => $item->price_per_unit * $quantity,
                        ];
                    }
                }
            }
        }

        // Calculate pricing
        $pricing = $this->calculatePricing($step1Data, $selectedEquipment);

        // Get user's registered city
        $user = DB::connection('auth_db')
            ->table('users')
            ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
            ->select('users.*', 'cities.name as city_name', 'cities.code as city_code')
            ->where('users.id', $userId)
            ->first();

        // Get cities for residency check
        $cities = DB::connection('facilities_db')
            ->table('lgu_cities')
            ->where('status', 'active')
            ->orderBy('city_name')
            ->get();

        return view('citizen.booking.step3-review-submit', compact(
            'facility',
            'step1Data',
            'equipmentDetails',
            'pricing',
            'cities',
            'userName',
            'userEmail',
            'user'
        ));
    }

    /**
     * Submit the booking
     */
    public function store(Request $request)
    {
        $userId = session('user_id');
        $userName = session('user_name');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get step 1 data
        $step1Data = session('booking_step1');
        if (!$step1Data) {
            return redirect()->route('citizen.booking.create')
                ->with('error', 'Session expired. Please start again.');
        }

        // Validate final submission
        $validated = $request->validate([
            'city_of_residence' => 'required|string',
            'valid_id_type' => 'required|string',
            'special_discount_type' => 'nullable|in:senior,pwd,student',
            'special_discount_id' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'valid_id_front' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'valid_id_back' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'valid_id_selfie' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        try {
            DB::connection('facilities_db')->beginTransaction();

            // Upload documents
            $validIdFrontPath = null;
            $validIdBackPath = null;
            $validIdSelfiePath = null;
            $specialIdPath = null;

            if ($request->hasFile('valid_id_front')) {
                $validIdFrontPath = $request->file('valid_id_front')->store('bookings/valid_ids/front', 'public');
            }

            if ($request->hasFile('valid_id_back')) {
                $validIdBackPath = $request->file('valid_id_back')->store('bookings/valid_ids/back', 'public');
            }

            if ($request->hasFile('valid_id_selfie')) {
                $validIdSelfiePath = $request->file('valid_id_selfie')->store('bookings/valid_ids/selfie', 'public');
            }

            if ($request->hasFile('special_discount_id')) {
                $specialIdPath = $request->file('special_discount_id')->store('bookings/discount_ids', 'public');
            }

            // Get facility
            $facility = DB::connection('facilities_db')
                ->table('facilities')
                ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
                ->select('facilities.*', 'lgu_cities.city_name')
                ->where('facilities.facility_id', $step1Data['facility_id'])
                ->first();

            // Calculate pricing with discounts
            $selectedEquipment = session('booking_equipment', []);
            $pricing = $this->calculatePricing($step1Data, $selectedEquipment);

            // Check if resident
            $isResident = false;
            $residentDiscountRate = 0;
            $residentDiscountAmount = 0;

            if ($facility->city_name && strtolower($validated['city_of_residence']) === strtolower($facility->city_name)) {
                $isResident = true;
                $residentDiscountRate = 30.00; // 30%
                $residentDiscountAmount = $pricing['subtotal'] * 0.30;
            }

            // Calculate special discount
            $specialDiscountRate = 0;
            $specialDiscountAmount = 0;

            if (!empty($validated['special_discount_type'])) {
                $specialDiscountRate = 20.00; // 20%
                $afterResidentDiscount = $pricing['subtotal'] - $residentDiscountAmount;
                $specialDiscountAmount = $afterResidentDiscount * 0.20;
            }

            // Calculate total
            $totalDiscount = $residentDiscountAmount + $specialDiscountAmount;
            $totalAmount = $pricing['subtotal'] - $totalDiscount;

            // Create booking
            $startDateTime = Carbon::parse($step1Data['booking_date'] . ' ' . $step1Data['start_time']);
            $endDateTime = Carbon::parse($step1Data['booking_date'] . ' ' . $step1Data['end_time']);

            $bookingId = DB::connection('facilities_db')->table('bookings')->insertGetId([
                'user_id' => $userId,
                'facility_id' => $step1Data['facility_id'],
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'user_name' => $userName,
                'status' => 'pending',
                'base_rate' => $pricing['base_rate'],
                'extension_rate' => $pricing['extension_hours'] > 0 ? $pricing['extension_rate'] : 0,
                'equipment_total' => $pricing['equipment_total'],
                'subtotal' => $pricing['subtotal'],
                'city_of_residence' => $validated['city_of_residence'],
                'is_resident' => $isResident,
                'resident_discount_rate' => $residentDiscountRate,
                'resident_discount_amount' => $residentDiscountAmount,
                'special_discount_type' => $validated['special_discount_type'],
                'special_discount_id_path' => $specialIdPath,
                'special_discount_rate' => $specialDiscountRate,
                'special_discount_amount' => $specialDiscountAmount,
                'total_discount' => $totalDiscount,
                'total_amount' => $totalAmount,
                'purpose' => $step1Data['purpose'],
                'expected_attendees' => $step1Data['expected_attendees'],
                'special_requests' => $validated['special_requests'],
                'valid_id_type' => $validated['valid_id_type'],
                'valid_id_front_path' => $validIdFrontPath,
                'valid_id_back_path' => $validIdBackPath,
                'valid_id_selfie_path' => $validIdSelfiePath,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Add equipment to booking_equipment table
            if (!empty($selectedEquipment)) {
                foreach ($selectedEquipment as $equipId => $quantity) {
                    if ($quantity > 0) {
                        $item = DB::connection('facilities_db')
                            ->table('equipment_items')
                            ->where('id', $equipId)
                            ->first();

                        if ($item) {
                            DB::connection('facilities_db')->table('booking_equipment')->insert([
                                'booking_id' => $bookingId,
                                'equipment_item_id' => $equipId,
                                'quantity' => $quantity,
                                'price_per_unit' => $item->price_per_unit,
                                'subtotal' => $item->price_per_unit * $quantity,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }
                    }
                }
            }

            DB::connection('facilities_db')->commit();

            // Send notification to citizen
            try {
                $user = User::find($userId);
                $bookingWithFacility = DB::connection('facilities_db')
                    ->table('bookings')
                    ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                    ->where('bookings.id', $bookingId)
                    ->selectRaw('bookings.*, facilities.name as facility_name, CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference')
                    ->first();
                
                if ($user && $bookingWithFacility) {
                    // Notify the citizen
                    $user->notify(new \App\Notifications\BookingSubmitted($bookingWithFacility));
                    
                    // Notify ALL staff members about the new booking
                    // Get all users who have "reservations staff" role in session
                    // Since we can't query by session, we'll query by subsystem_role_id
                    // Staff role ID is 3 based on the auth system
                    $staffMembers = User::where('subsystem_role_id', 3)
                        ->where('subsystem_id', 4) // Facilities subsystem
                        ->get();
                    
                    foreach ($staffMembers as $staff) {
                        $staff->notify(new \App\Notifications\BookingSubmitted($bookingWithFacility));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send booking notification: ' . $e->getMessage());
            }

            // Clear session data
            session()->forget(['booking_step1', 'booking_equipment']);

            return redirect()->route('citizen.booking.confirmation', $bookingId)
                ->with('success', 'Booking submitted successfully! Please wait for staff verification.');

        } catch (\Exception $e) {
            DB::connection('facilities_db')->rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while creating your booking. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show booking confirmation
     */
    public function confirmation($bookingId)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get booking details
        $booking = DB::connection('facilities_db')
            ->table('bookings')
            ->where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            return redirect()->route('citizen.reservations')
                ->with('error', 'Booking not found.');
        }

        // Get facility
        $facility = DB::connection('facilities_db')
            ->table('facilities')
            ->where('facility_id', $booking->facility_id)
            ->first();

        // Get equipment
        $equipment = DB::connection('facilities_db')
            ->table('booking_equipment')
            ->join('equipment_items', 'booking_equipment.equipment_item_id', '=', 'equipment_items.id')
            ->where('booking_equipment.booking_id', $bookingId)
            ->select('equipment_items.name', 'booking_equipment.quantity', 'booking_equipment.subtotal')
            ->get();

        return view('citizen.booking.confirmation', compact('booking', 'facility', 'equipment'));
    }

    /**
     * Calculate pricing based on facility pricing model (per-person or hourly)
     */
    private function calculatePricing($step1Data, $selectedEquipment)
    {
        // Get facility pricing information
        $facility = DB::connection('facilities_db')
            ->table('facilities')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select('facilities.*', 'lgu_cities.city_name')
            ->where('facilities.facility_id', $step1Data['facility_id'])
            ->first();

        // Get expected attendees
        $expectedAttendees = $step1Data['expected_attendees'] ?? 1;

        // Calculate duration
        $startTime = Carbon::parse($step1Data['booking_date'] . ' ' . $step1Data['start_time']);
        $endTime = Carbon::parse($step1Data['booking_date'] . ' ' . $step1Data['end_time']);
        $totalHours = $startTime->diffInHours($endTime);

        $baseRate = 0;
        $extensionRate = 0;
        $perPersonRate = 0;
        $extensionRatePer2Hours = 0;
        $baseHours = 3;
        $extensionHours = 0;
        $extensionBlocks = 0;
        $pricingModel = 'default';
        
        // Determine pricing model based on facility
        if (isset($facility->per_person_rate) && $facility->per_person_rate > 0) {
            // Per-person pricing model with FIXED 2-HOUR EXTENSION BLOCKS
            $perPersonRate = $facility->per_person_rate;
            $baseHours = $facility->base_hours ?? 3;
            $extensionRatePer2Hours = $facility->per_person_extension_rate ?? 0;
            
            // Base rate for base hours (e.g., first 3 hours)
            $baseRate = $perPersonRate * $expectedAttendees;
            
            // Extension: FIXED 2-hour blocks (e.g., 4 hours = 2 blocks, 5 hours = 3 blocks)
            $extensionHours = max(0, $totalHours - $baseHours);
            if ($extensionHours > 0 && $extensionRatePer2Hours > 0) {
                $extensionBlocks = ceil($extensionHours / 2); // Round up to full 2-hour blocks
                $extensionRate = $extensionBlocks * $extensionRatePer2Hours * $expectedAttendees;
            } else {
                $extensionRate = 0;
                $extensionBlocks = 0;
            }
            
            $pricingModel = 'per_person';
        } else if (isset($facility->hourly_rate) && $facility->hourly_rate > 0) {
            // Hourly pricing model (if facility uses this)
            $baseRate = $facility->hourly_rate * $totalHours;
            $extensionRate = 0;
            $extensionBlocks = 0;
            $pricingModel = 'hourly';
        } else {
            // Fallback to old calculation if no pricing model is set
            $baseRate = 7000.00; // Default ₱7,000 for 3 hours
            $extensionRatePerTwoHours = 3000.00;
        $baseHours = 3;
        $extensionHours = max(0, $totalHours - $baseHours);
        $extensionBlocks = ceil($extensionHours / 2);
        $extensionRate = $extensionBlocks * $extensionRatePerTwoHours;
        }

        // Calculate equipment total
        $equipmentTotal = 0;
        if (!empty($selectedEquipment)) {
            foreach ($selectedEquipment as $equipId => $quantity) {
                if ($quantity > 0) {
                    $item = DB::connection('facilities_db')
                        ->table('equipment_items')
                        ->where('id', $equipId)
                        ->first();
                    if ($item) {
                        $equipmentTotal += $item->price_per_unit * $quantity;
                    }
                }
            }
        }

        $subtotal = $baseRate + $extensionRate + $equipmentTotal;

        return [
            'base_rate' => $baseRate,
            'total_hours' => $totalHours,
            'base_hours' => $baseHours,
            'extension_hours' => $extensionHours,
            'extension_blocks' => $extensionBlocks,
            'extension_rate' => $extensionRate,
            'extension_rate_per_block' => $extensionRatePer2Hours,
            'equipment_total' => $equipmentTotal,
            'subtotal' => $subtotal,
            'pricing_model' => $pricingModel,
            'per_person_rate' => $perPersonRate,
            'expected_attendees' => $expectedAttendees,
        ];
    }

    /**
     * Check facility availability for a given date/time
     */
    public function checkAvailability(Request $request)
    {
        $facilityId = $request->input('facility_id');
        $bookingDate = $request->input('booking_date');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        $startDateTime = Carbon::parse($bookingDate . ' ' . $startTime);
        $endDateTime = Carbon::parse($bookingDate . ' ' . $endTime);

        // Add 2-hour buffer after each booking for cleanup, inspection, and preparation
        $bufferHours = 2;

        // Check for conflicts and get the conflicting bookings
        // Check if requested time overlaps with any booking + its 2-hour buffer
        $conflictingBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->where('facility_id', $facilityId)
            ->whereIn('status', ['pending', 'staff_verified', 'reserved', 'payment_pending', 'confirmed', 'paid'])
            ->where(function($query) use ($startDateTime, $endDateTime, $bufferHours) {
                $query->where(function($q) use ($startDateTime, $endDateTime, $bufferHours) {
                    // Check if new booking overlaps with existing booking + buffer
                    $q->whereRaw('? < DATE_ADD(end_time, INTERVAL ? HOUR)', [$startDateTime, $bufferHours])
                      ->whereRaw('? > start_time', [$endDateTime]);
                      });
            })
            ->select('start_time', 'end_time', 'status')
            ->orderBy('start_time')
            ->get();

        $hasConflict = $conflictingBookings->count() > 0;

        // If there's a conflict, find available time slots for the same day
        $availableSlots = [];
        if ($hasConflict) {
            // Get all bookings for that day
            $dayStart = Carbon::parse($bookingDate . ' 08:00:00');
            $dayEnd = Carbon::parse($bookingDate . ' 22:00:00');
            
            $allBookings = DB::connection('facilities_db')
                ->table('bookings')
                ->where('facility_id', $facilityId)
                ->whereIn('status', ['pending', 'staff_verified', 'reserved', 'payment_pending', 'confirmed', 'paid'])
                ->whereDate('start_time', $bookingDate)
                ->orderBy('start_time')
                ->get();

            // Find gaps between bookings (minimum 3 hours)
            $minDuration = 3; // minimum booking duration in hours
            
            if ($allBookings->isEmpty()) {
                // Entire day is free - suggest 3-hour slot from opening time
                $slotEnd = $dayStart->copy()->addHours($minDuration);
                $availableSlots[] = [
                    'start' => $dayStart->format('h:i A'),
                    'end' => $slotEnd->format('h:i A'),
                    'start_24h' => $dayStart->format('H:i'),
                    'end_24h' => $slotEnd->format('H:i'),
                ];
            } else {
                // Check gap before first booking
                $firstBooking = Carbon::parse($allBookings->first()->start_time);
                $gapHours = $dayStart->diffInHours($firstBooking);
                if ($gapHours >= $minDuration) {
                    // Suggest 3-hour slot, not the entire gap
                    $slotEnd = $dayStart->copy()->addHours($minDuration);
                    // Make sure it doesn't exceed the next booking
                    if ($slotEnd->greaterThan($firstBooking)) {
                        $slotEnd = $firstBooking;
                    }
                    $availableSlots[] = [
                        'start' => $dayStart->format('h:i A'),
                        'end' => $slotEnd->format('h:i A'),
                        'start_24h' => $dayStart->format('H:i'),
                        'end_24h' => $slotEnd->format('H:i'),
                    ];
                }
                
                // Check gaps between bookings (including 2-hour buffer after each)
                for ($i = 0; $i < $allBookings->count() - 1; $i++) {
                    $currentEnd = Carbon::parse($allBookings[$i]->end_time)->addHours($bufferHours); // Add buffer
                    $nextStart = Carbon::parse($allBookings[$i + 1]->start_time);
                    
                    $gapHours = $currentEnd->diffInHours($nextStart);
                    if ($gapHours >= $minDuration) {
                        // Suggest 3-hour slot, not the entire gap
                        $slotEnd = $currentEnd->copy()->addHours($minDuration);
                        // Make sure it doesn't exceed the next booking
                        if ($slotEnd->greaterThan($nextStart)) {
                            $slotEnd = $nextStart;
                        }
                        $availableSlots[] = [
                            'start' => $currentEnd->format('h:i A'),
                            'end' => $slotEnd->format('h:i A'),
                            'start_24h' => $currentEnd->format('H:i'),
                            'end_24h' => $slotEnd->format('H:i'),
                        ];
                    }
                }
                
                // Check gap after last booking (including 2-hour buffer)
                $lastBookingEnd = Carbon::parse($allBookings->last()->end_time)->addHours($bufferHours);
                $gapHours = $lastBookingEnd->diffInHours($dayEnd);
                if ($gapHours >= $minDuration) {
                    // Suggest 3-hour slot, not the entire gap
                    $slotEnd = $lastBookingEnd->copy()->addHours($minDuration);
                    // Make sure it doesn't exceed closing time
                    if ($slotEnd->greaterThan($dayEnd)) {
                        $slotEnd = $dayEnd;
                    }
                    $availableSlots[] = [
                        'start' => $lastBookingEnd->format('h:i A'),
                        'end' => $slotEnd->format('h:i A'),
                        'start_24h' => $lastBookingEnd->format('H:i'),
                        'end_24h' => $slotEnd->format('H:i'),
                    ];
                }
            }
        }

        return response()->json([
            'available' => !$hasConflict,
            'conflicts' => $conflictingBookings->map(function($booking) use ($bufferHours) {
                $endTime = Carbon::parse($booking->end_time);
                $endWithBuffer = $endTime->copy()->addHours($bufferHours);
                return [
                    'start' => Carbon::parse($booking->start_time)->format('h:i A'),
                    'end' => $endTime->format('h:i A'),
                    'buffer_end' => $endWithBuffer->format('h:i A'),
                    'status' => ucfirst(str_replace('_', ' ', $booking->status)),
                ];
            }),
            'available_slots' => $availableSlots,
            'buffer_hours' => $bufferHours,
        ]);
    }
}

