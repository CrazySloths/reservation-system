<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncDataToCloud implements ShouldQueue
{
    use Queueable;

    /**
     * Main execution logic for bidirectional synchronization.
     */
    public function handle(): void
    {
        // Fetch environment variables
        $baseUrl = rtrim(env('LIVE_SYSTEM_URL'), '/');
        $key = env('SYNC_API_KEY');
        $syncEndpoint = $baseUrl . '/api/sync-data';

        /**
         * Table Configuration
         * Note: 'facilities' uses 'facility_id', others use 'id'.
         */
        $syncConfigs = [
            ['table' => 'users', 'connection' => 'auth_db', 'pk' => 'id'],
            ['table' => 'bookings', 'connection' => 'facilities_db', 'pk' => 'id'],
            ['table' => 'activity_logs', 'connection' => 'auth_db', 'pk' => 'id'],
            ['table' => 'payment_slips', 'connection' => 'facilities_db', 'pk' => 'id'],
            ['table' => 'announcements', 'connection' => 'facilities_db', 'pk' => 'id'],
            ['table' => 'facilities', 'connection' => 'facilities_db', 'pk' => 'facility_id'],
        ];

        foreach ($syncConfigs as $config) {
            // Task 1: Push local updates to the Cloud
            $this->uploadToCloud($config, $syncEndpoint, $key);

            // Task 2: Pull new data from the Cloud to Local
            $this->downloadFromCloud($config, $syncEndpoint, $key);
        }
    }

    /**
     * Upload: Send local records where is_synced = 0 to Cloud server.
     */
    private function uploadToCloud($config, $url, $key)
    {
        $table = $config['table'];
        $conn = $config['connection'];
        $pk = $config['pk'];

        DB::connection($conn)
            ->table($table)
            ->where('is_synced', 0)
            ->orderBy($pk)
            ->chunk(50, function ($records) use ($table, $url, $key, $conn, $pk) {
                try {
                    // Execute POST request
                    $response = Http::withHeaders(['X-Sync-Key' => $key])
                        ->timeout(120)
                        ->post($url, [
                            'action' => 'upload',
                            'table'  => $table,
                            'data'   => $records->toArray()
                        ]);

                    // Structured JSON log for the report
                    Log::info("SYNC_UPLOAD_REPORT", [
                        'table'  => $table,
                        'status' => $response->status(),
                        'count'  => $records->count(),
                        'ok'     => $response->successful()
                    ]);

                    if ($response->successful()) {
                        // Mark records as synced in local database
                        DB::connection($conn)
                            ->table($table)
                            ->whereIn($pk, $records->pluck($pk))
                            ->update([
                                'is_synced' => 1,
                                'last_synced_at' => now()
                            ]);
                    }
                } catch (\Exception $e) {
                    Log::error("SYNC_UPLOAD_EXCEPTION", [
                        'table'   => $table,
                        'message' => $e->getMessage()
                    ]);
                }
            });
    }

    /**
     * Download: Fetch records from Cloud and save/update in Local database.
     */
    private function downloadFromCloud($config, $url, $key)
    {
        $table = $config['table'];
        $conn = $config['connection'];
        $pk = $config['pk'];

        try {
            // Execute GET request
            $response = Http::withHeaders(['X-Sync-Key' => $key])
                ->get($url, [
                    'action' => 'download',
                    'table'  => $table
                ]);

            $status = $response->status();
            $dataFound = isset($response->json()['data']) ? count($response->json()['data']) : 0;

            Log::info("SYNC_DOWNLOAD_REPORT", [
                'table'  => $table,
                'status' => $status,
                'found'  => $dataFound,
                'ok'     => $response->successful()
            ]);

            if ($response->successful() && $dataFound > 0) {
                $cloudRecords = $response->json()['data'];

                foreach ($cloudRecords as $record) {
                    // Convert to array to handle key access safely
                    $recordArray = (array)$record;
                    
                    // Mark as synced locally
                    $recordArray['is_synced'] = 1;
                    $recordArray['last_synced_at'] = now();

                    // Check if PK exists in record before performing Upsert
                    if (isset($recordArray[$pk])) {
                        DB::connection($conn)
                            ->table($table)
                            ->updateOrInsert([$pk => $recordArray[$pk]], $recordArray);
                    } else {
                        Log::warning("SYNC_PK_MISSING", [
                            'table' => $table, 
                            'expected_pk' => $pk
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("SYNC_DOWNLOAD_EXCEPTION", [
                'table'   => $table,
                'message' => $e->getMessage()
            ]);
        }
    }
}