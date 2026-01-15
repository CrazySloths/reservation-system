<?php

namespace App\Console\Commands;

use App\Models\BookingConflict;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConflictAutoRefundMail;

class ProcessExpiredConflicts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conflicts:process-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process expired booking conflicts and auto-refund bookings with no response';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing expired booking conflicts...');

        // Get all pending conflicts that are past their response deadline
        $expiredConflicts = BookingConflict::where('status', 'pending')
            ->where('response_deadline', '<', now())
            ->get();

        if ($expiredConflicts->isEmpty()) {
            $this->info('No expired conflicts found.');
            return 0;
        }

        $processedCount = 0;
        $failedCount = 0;

        foreach ($expiredConflicts as $conflict) {
            try {
                DB::connection('facilities_db')->beginTransaction();

                // Auto-process as no response (refund)
                $conflict->processNoResponse();

                // Reload to get updated data
                $conflict->refresh();

                // Send email notification and create in-app notification to citizen
                $booking = $conflict->booking();
                if ($booking) {
                    $citizen = DB::connection('auth_db')
                        ->table('users')
                        ->where('id', $booking->user_id)
                        ->first();
                    
                    if ($citizen) {
                        // Send email
                        if ($citizen->email) {
                            try {
                                Mail::to($citizen->email)->send(new ConflictAutoRefundMail($conflict));
                                $this->info("Email sent to {$citizen->email}");
                            } catch (\Exception $e) {
                                $this->error("Failed to send email: {$e->getMessage()}");
                                Log::error('Failed to send auto-refund email', [
                                    'conflict_id' => $conflict->id,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                        
                        // Create in-app notification
                        try {
                            DB::connection('auth_db')->table('notifications')->insert([
                                'id' => \Illuminate\Support\Str::uuid(),
                                'type' => 'App\\Notifications\\ConflictAutoRefundNotification',
                                'notifiable_type' => 'App\\Models\\User',
                                'notifiable_id' => $citizen->id,
                                'data' => json_encode([
                                    'message' => "Your booking conflict response deadline has expired. An automatic refund has been processed for booking #{$booking->booking_reference}.",
                                    'conflict_id' => $conflict->id,
                                    'booking_reference' => $booking->booking_reference,
                                    'action_url' => url('/citizen/transactions'),
                                ]),
                                'read_at' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $this->info("In-app notification created for user {$citizen->id}");
                        } catch (\Exception $e) {
                            $this->error("Failed to create in-app notification: {$e->getMessage()}");
                            Log::error('Failed to create auto-refund notification', [
                                'conflict_id' => $conflict->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }

                DB::connection('facilities_db')->commit();

                $processedCount++;
                $this->info("Processed conflict ID {$conflict->id} - Auto-refund applied");

            } catch (\Exception $e) {
                DB::connection('facilities_db')->rollBack();
                $failedCount++;
                
                $this->error("Failed to process conflict ID {$conflict->id}: {$e->getMessage()}");
                Log::error("Failed to process expired conflict", [
                    'conflict_id' => $conflict->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("\nSummary:");
        $this->info("Total expired conflicts: {$expiredConflicts->count()}");
        $this->info("Successfully processed: {$processedCount}");
        $this->info("Failed: {$failedCount}");

        return 0;
    }
}
