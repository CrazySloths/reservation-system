<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\BookingConflict;
use App\Models\CityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConflictResolvedMail;

class BookingConflictController extends Controller
{
    /**
     * Display all booking conflicts for the authenticated citizen
     */
    public function index()
    {
        $userId = session('user_id');

        // Get all bookings for this user (including refunded/rescheduled for conflict history)
        $userBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->where('user_id', $userId)
            ->whereIn('status', ['confirmed', 'paid', 'refunded', 'rescheduled'])
            ->pluck('id');

        // Get filter from request
        $filter = request('filter', 'pending'); // 'pending', 'resolved', or 'all'
        
        // Get conflicts for these bookings
        $query = BookingConflict::whereIn('booking_id', $userBookings)
            ->with('cityEvent');
        
        // Apply status filter
        if ($filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filter === 'resolved') {
            $query->where('status', 'resolved');
        }
        // 'all' shows both pending and resolved
        
        $conflicts = $query->orderBy('response_deadline', 'desc')->paginate(15);

        // Attach booking and facility details
        foreach ($conflicts as $conflict) {
            $booking = $conflict->booking();
            if ($booking) {
                $facility = DB::connection('facilities_db')
                    ->table('facilities')
                    ->where('facility_id', $booking->facility_id)
                    ->first();
                
                $conflict->bookingDetails = $booking;
                $conflict->facilityDetails = $facility;
            }
        }

        return view('citizen.conflicts.index', compact('conflicts', 'filter'));
    }

    /**
     * Display the specified booking conflict
     */
    public function show($id)
    {
        $conflict = BookingConflict::with('cityEvent')->findOrFail($id);

        // Verify this conflict belongs to the authenticated user
        $booking = $conflict->booking();
        if ($booking->user_id !== session('user_id')) {
            abort(403, 'Unauthorized access to this conflict.');
        }

        // Get facility details
        $facility = DB::connection('facilities_db')
            ->table('facilities')
            ->where('facility_id', $booking->facility_id)
            ->first();

        // Get available alternative dates (next 30 days)
        $availableDates = $this->getAvailableDates($booking->facility_id, 30);

        $conflict->bookingDetails = $booking;
        $conflict->facilityDetails = $facility;

        return view('citizen.conflicts.show', compact('conflict', 'availableDates'));
    }

    /**
     * Process citizen's choice for a conflict
     */
    public function resolveConflict(Request $request, $id)
    {
        $request->validate([
            'choice' => 'required|in:reschedule,refund',
            'new_start_time' => 'nullable|required_if:choice,reschedule|date',
            'new_end_time' => 'nullable|required_if:choice,reschedule|date|after:new_start_time',
            'refund_method' => 'required_if:choice,refund|in:cash,gcash,paymaya,bank_transfer',
            'refund_account_name' => 'nullable|required_if:refund_method,gcash,paymaya,bank_transfer',
            'refund_account_number' => 'nullable|required_if:refund_method,gcash,paymaya,bank_transfer',
            'refund_bank_name' => 'nullable|required_if:refund_method,bank_transfer',
        ]);

        $conflict = BookingConflict::findOrFail($id);

        // Verify ownership
        $booking = $conflict->booking();
        if ($booking->user_id !== session('user_id')) {
            abort(403, 'Unauthorized access to this conflict.');
        }

        // Check if deadline passed
        if ($conflict->isDeadlinePassed()) {
            return back()->withErrors(['error' => 'Response deadline has passed. Your booking has been automatically refunded.']);
        }

        // Check if already resolved
        if ($conflict->status === 'resolved') {
            return back()->withErrors(['error' => 'This conflict has already been resolved.']);
        }

        try {
            DB::connection('facilities_db')->beginTransaction();

            $newBookingId = null;

            if ($request->choice === 'reschedule') {
                // Check if new time slot is available
                $hasConflict = DB::connection('facilities_db')
                    ->table('bookings')
                    ->where('facility_id', $booking->facility_id)
                    ->whereIn('status', ['confirmed', 'paid'])
                    ->where(function($query) use ($request) {
                        $query->whereBetween('start_time', [$request->new_start_time, $request->new_end_time])
                              ->orWhereBetween('end_time', [$request->new_start_time, $request->new_end_time])
                              ->orWhere(function($q) use ($request) {
                                  $q->where('start_time', '<=', $request->new_start_time)
                                    ->where('end_time', '>=', $request->new_end_time);
                              });
                    })
                    ->exists();

                if ($hasConflict) {
                    return back()->withErrors(['error' => 'Selected time slot is not available. Please choose another date/time.']);
                }

                // Create new booking
                $newBookingId = DB::connection('facilities_db')
                    ->table('bookings')
                    ->insertGetId([
                        'facility_id' => $booking->facility_id,
                        'user_id' => $booking->user_id,
                        'start_time' => $request->new_start_time,
                        'end_time' => $request->new_end_time,
                        'status' => $booking->status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // Store refund details if refund was chosen
            if ($request->choice === 'refund') {
                $conflict->refund_method = $request->refund_method;
                $conflict->refund_account_name = $request->refund_account_name;
                $conflict->refund_account_number = $request->refund_account_number;
                $conflict->refund_bank_name = $request->refund_bank_name;
                $conflict->save();
            }

            // Process the conflict
            $conflict->processCitizenChoice($request->choice, $newBookingId, 'Resolved by citizen');

            // Reload conflict to get updated data
            $conflict->refresh();

            // COMMIT FIRST before sending emails
            DB::connection('facilities_db')->commit();

            // Send email notification and create in-app notification AFTER commit
            $user = DB::connection('auth_db')
                ->table('users')
                ->where('id', session('user_id'))
                ->first();
            if ($user) {
                // Send email
                if ($user->email) {
                    try {
                        Mail::to($user->email)->send(new ConflictResolvedMail($conflict));
                    } catch (\Exception $e) {
                        // Log error but don't stop the process
                        \Log::error('Failed to send conflict resolved email: ' . $e->getMessage());
                    }
                }
                
                // Create in-app notification
                try {
                    $actionType = $request->choice === 'reschedule' ? 'rescheduled' : 'refunded';
                    DB::connection('auth_db')->table('notifications')->insert([
                        'id' => \Illuminate\Support\Str::uuid(),
                        'type' => 'App\\Notifications\\ConflictResolvedNotification',
                        'notifiable_type' => 'App\\Models\\User',
                        'notifiable_id' => $user->id,
                        'data' => json_encode([
                            'message' => "Your booking conflict has been resolved. Your booking has been {$actionType} successfully.",
                            'conflict_id' => $conflict->id,
                            'choice' => $request->choice,
                            'action_url' => $request->choice === 'reschedule' ? url('/citizen/reservations') : url('/citizen/transactions'),
                        ]),
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create in-app notification: ' . $e->getMessage());
                }
            }

            $message = $request->choice === 'reschedule' 
                ? 'Booking rescheduled successfully. Your new booking is confirmed.'
                : 'Refund request submitted successfully. You will receive your refund within 3-7 business days.';

            return redirect()
                ->route('citizen.conflicts.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::connection('facilities_db')->rollBack();
            \Log::error('Failed to resolve conflict: ' . $e->getMessage(), [
                'conflict_id' => $id,
                'user_id' => session('user_id'),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to resolve conflict: ' . $e->getMessage()]);
        }
    }

    /**
     * Get available dates for rescheduling
     */
    private function getAvailableDates($facilityId, $days = 30)
    {
        $availableDates = [];
        $startDate = now()->startOfDay();

        for ($i = 1; $i <= $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            // Check if facility is available on this date
            $hasBooking = DB::connection('facilities_db')
                ->table('bookings')
                ->where('facility_id', $facilityId)
                ->whereIn('status', ['confirmed', 'paid'])
                ->whereDate('start_time', $date->toDateString())
                ->exists();

            $hasCityEvent = CityEvent::where('facility_id', $facilityId)
                ->where('status', 'scheduled')
                ->whereDate('start_time', $date->toDateString())
                ->exists();

            if (!$hasBooking && !$hasCityEvent) {
                $availableDates[] = $date->toDateString();
            }
        }

        return $availableDates;
    }
}
