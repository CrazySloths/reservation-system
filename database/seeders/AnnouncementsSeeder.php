<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnnouncementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Welcome to LGU Facility Reservation System',
                'content' => 'We are pleased to announce the launch of our new online facility reservation system. You can now book facilities, make payments, and track your reservations all in one place! Experience a seamless booking process with real-time availability checking and instant confirmation.',
                'type' => 'general',
                'priority' => 'high',
                'target_audience' => 'citizens',
                'is_active' => true,
                'is_pinned' => true,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addMonths(3)->toDateString(),
                'created_by' => 1,
                'additional_info' => 'For any questions or assistance, please contact our support team at support@lgu.gov.ph',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Facility Maintenance Schedule - December 2025',
                'content' => 'Please be advised that the Main Conference Hall will undergo scheduled maintenance from December 15-20, 2025. The facility will be temporarily unavailable for booking during this period. We apologize for any inconvenience this may cause.',
                'type' => 'maintenance',
                'priority' => 'medium',
                'target_audience' => 'all',
                'is_active' => true,
                'is_pinned' => false,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => null,
                'created_by' => 1,
                'additional_info' => 'Maintenance includes electrical upgrades, air conditioning servicing, and interior refurbishment.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Special Holiday Rates - Christmas Season',
                'content' => 'Enjoy special discounted rates for facility bookings during the Christmas season! Book now and get 20% off on all venues from December 1-31, 2025. Perfect for your holiday parties and celebrations. Limited slots available!',
                'type' => 'event',
                'priority' => 'medium',
                'target_audience' => 'citizens',
                'is_active' => true,
                'is_pinned' => false,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addMonth()->endOfMonth()->toDateString(),
                'created_by' => 1,
                'additional_info' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'New Facility Added: Sports Complex',
                'content' => 'We are excited to announce the addition of our brand new Sports Complex to the reservation system. Features include basketball courts, volleyball courts, badminton courts, and a fully-equipped fitness center. Book now and enjoy world-class sports facilities!',
                'type' => 'facility_update',
                'priority' => 'high',
                'target_audience' => 'all',
                'is_active' => true,
                'is_pinned' => true,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => null,
                'created_by' => 1,
                'additional_info' => 'Opening special: First 50 bookings get 30% discount!',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'URGENT: Payment Deadline Extension',
                'content' => 'Due to technical issues, we are extending the payment deadline for all pending reservations by 48 hours. Please ensure your payments are settled before the new deadline to avoid cancellation.',
                'type' => 'urgent',
                'priority' => 'urgent',
                'target_audience' => 'citizens',
                'is_active' => true,
                'is_pinned' => false,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(3)->toDateString(),
                'created_by' => 1,
                'additional_info' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'New Equipment Available for Rent',
                'content' => 'Great news! We have added new equipment to our inventory including premium sound systems, LED screens, and professional lighting equipment. Perfect for your events and conferences.',
                'type' => 'general',
                'priority' => 'low',
                'target_audience' => 'all',
                'is_active' => true,
                'is_pinned' => false,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => null,
                'created_by' => 1,
                'additional_info' => 'Check our equipment catalog for pricing and availability.',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
        ];

        DB::connection('facilities_db')->table('announcements')->insert($announcements);
    }
}

