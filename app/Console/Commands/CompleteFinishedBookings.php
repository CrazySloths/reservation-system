<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class CompleteFinishedBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:complete-finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark confirmed bookings as completed after the event end time has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for finished bookings to mark as completed...');

        // Get all confirmed bookings where the end time has passed
        $finishedBookings = Booking::where('status', 'confirmed')
            ->where('end_time', '<', Carbon::now())
            ->get();

        $completedCount = 0;

        foreach ($finishedBookings as $booking) {
            // Mark as completed
            $booking->status = 'completed';
            $booking->save();

            $completedCount++;
            
            $this->info("COMPLETED: Booking #{$booking->id} - {$booking->facility->name}");
            $this->line("   Citizen: {$booking->applicant_name}");
            $this->line("   Event Date: {$booking->start_time->format('M d, Y h:i A')} - {$booking->end_time->format('h:i A')}");
            $this->line("   Completed: " . Carbon::now()->format('M d, Y h:i A'));
            $this->newLine();

            // TODO: Send notification to citizen requesting a review
            // Notification::send($booking->user, new BookingCompletedNotification($booking));
        }

        if ($completedCount > 0) {
            $this->info("SUCCESS: Marked {$completedCount} booking(s) as completed.");
        } else {
            $this->info("SUCCESS: No bookings to complete. All good!");
        }

        return Command::SUCCESS;
    }
}

