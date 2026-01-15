<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\PaymentReminder24Hours;
use App\Notifications\PaymentReminder6Hours;
use Carbon\Carbon;
use DB;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders';
    protected $description = 'Send payment deadline reminders (24 hours and 6 hours before expiration)';

    public function handle()
    {
        $this->info('Checking for payment reminders to send...');

        $now = Carbon::now();
        $reminder24Hours = $now->copy()->addHours(24);
        $reminder6Hours = $now->copy()->addHours(6);

        // Find payment slips that need 24-hour reminders
        $slips24Hours = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->where('payment_slips.status', 'unpaid')
            ->whereBetween('payment_slips.payment_deadline', [
                $reminder24Hours->copy()->subMinutes(30),
                $reminder24Hours->copy()->addMinutes(30)
            ])
            ->whereNull('payment_slips.reminder_24h_sent_at')
            ->select('payment_slips.*', 'bookings.user_id', 'bookings.applicant_name', 'bookings.start_time', 'bookings.end_time')
            ->get();

        // Find payment slips that need 6-hour reminders
        $slips6Hours = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->where('payment_slips.status', 'unpaid')
            ->whereBetween('payment_slips.payment_deadline', [
                $reminder6Hours->copy()->subMinutes(30),
                $reminder6Hours->copy()->addMinutes(30)
            ])
            ->whereNull('payment_slips.reminder_6h_sent_at')
            ->select('payment_slips.*', 'bookings.user_id', 'bookings.applicant_name', 'bookings.start_time', 'bookings.end_time')
            ->get();

        $count24h = 0;
        $count6h = 0;

        // Send 24-hour reminders
        foreach ($slips24Hours as $slip) {
            $user = User::find($slip->user_id);
            if ($user) {
                $booking = DB::connection('facilities_db')
                    ->table('bookings')
                    ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                    ->where('bookings.id', $slip->booking_id)
                    ->selectRaw('bookings.*, facilities.name as facility_name, CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference')
                    ->first();

                try {
                    $user->notify(new PaymentReminder24Hours($booking, $slip));
                    
                    DB::connection('facilities_db')
                        ->table('payment_slips')
                        ->where('id', $slip->id)
                        ->update(['reminder_24h_sent_at' => now()]);
                    
                    $count24h++;
                    $this->line(" - 24h reminder sent: {$slip->slip_number}");
                } catch (\Exception $e) {
                    $this->error("Failed to send 24h reminder for {$slip->slip_number}: {$e->getMessage()}");
                }
            }
        }

        // Send 6-hour reminders
        foreach ($slips6Hours as $slip) {
            $user = User::find($slip->user_id);
            if ($user) {
                $booking = DB::connection('facilities_db')
                    ->table('bookings')
                    ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                    ->where('bookings.id', $slip->booking_id)
                    ->selectRaw('bookings.*, facilities.name as facility_name, CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference')
                    ->first();

                try {
                    $user->notify(new PaymentReminder6Hours($booking, $slip));
                    
                    DB::connection('facilities_db')
                        ->table('payment_slips')
                        ->where('id', $slip->id)
                        ->update(['reminder_6h_sent_at' => now()]);
                    
                    $count6h++;
                    $this->line(" - 6h reminder sent: {$slip->slip_number}");
                } catch (\Exception $e) {
                    $this->error("Failed to send 6h reminder for {$slip->slip_number}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Sent {$count24h} 24-hour reminders and {$count6h} 6-hour reminders.");
        
        return Command::SUCCESS;
    }
}
