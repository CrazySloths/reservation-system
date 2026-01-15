<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpireUnpaidBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire bookings that haven\'t been paid within 48 hours of staff verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for unpaid bookings past 48-hour deadline...');

        // Get all staff_verified bookings
        $unpaidBookings = Booking::where('status', 'staff_verified')->get();

        $expiredCount = 0;

        foreach ($unpaidBookings as $booking) {
            // Calculate deadline: 48 hours from when booking was verified by staff
            $verifiedAt = $booking->staff_verified_at;
            
            // Skip if no verification timestamp (shouldn't happen, but safety check)
            if (!$verifiedAt) {
                continue;
            }
            
            $deadline = $verifiedAt->copy()->addHours(48);
            $now = Carbon::now();

            // Check if deadline has passed
            if ($now->greaterThan($deadline)) {
                // Expire the booking
                $booking->status = 'expired';
                $booking->expired_at = $now;
                $booking->save();

                // Send expiration notification to citizen
                try {
                    $user = User::find($booking->user_id);
                    $bookingWithFacility = DB::connection('facilities_db')
                        ->table('bookings')
                        ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                        ->where('bookings.id', $booking->id)
                        ->selectRaw('bookings.*, facilities.name as facility_name, CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference')
                        ->first();
                    
                    if ($user && $bookingWithFacility) {
                        $user->notify(new \App\Notifications\BookingExpired($bookingWithFacility));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send booking expiration notification: ' . $e->getMessage());
                }

                $expiredCount++;
                
                $this->warn("EXPIRED: Booking #{$booking->id} - {$booking->facility->name}");
                $this->line("   Citizen: {$booking->applicant_name}");
                $this->line("   Verified: {$verifiedAt->format('M d, Y h:i A')}");
                $this->line("   Deadline: {$deadline->format('M d, Y h:i A')}");
                $this->line("   Overdue by: {$deadline->diffForHumans($now, true)}");
                $this->newLine();

                // TODO: Send notification to citizen
                // Notification::send($booking->user, new BookingExpiredNotification($booking));
            }
        }

        if ($expiredCount > 0) {
            $this->info("SUCCESS: Expired {$expiredCount} booking(s) due to unpaid status.");
        } else {
            $this->info("SUCCESS: No bookings to expire. All good!");
        }

        return Command::SUCCESS;
    }
}
