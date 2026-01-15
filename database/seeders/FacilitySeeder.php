<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get locations (assuming they're already seeded)
        $caloocan = Location::where('location_code', 'CAL')->first();
        $quezoncity = Location::where('location_code', 'QC')->first();

        // If locations don't exist, create them
        if (!$caloocan) {
            $caloocan = Location::create([
                'location_name' => 'South Caloocan City',
                'location_code' => 'CAL',
                'address' => '10th Ave, Caloocan, Metro Manila',
                'city' => 'Caloocan City',
                'province' => 'Metro Manila',
                'zip_code' => '1400',
                'phone' => '(02) 8-288-8181',
                'email' => 'info@caloocan.gov.ph',
                'config' => json_encode([
                    'payment_mode' => 'per_person',
                    'base_rate' => 150,
                    'currency' => 'PHP',
                    'operating_hours' => ['start' => '06:00', 'end' => '22:00'],
                    'advance_booking_days' => 90,
                    'cancellation_deadline_hours' => 48,
                    'approval_levels' => ['staff', 'admin'],
                    'discount_tiers' => ['pwd' => 20, 'senior' => 20, 'student' => 20],
                    'requires_full_payment' => true,
                    'payment_policy' => 'Full payment required before reservation confirmation'
                ]),
                'is_active' => true
            ]);
        }

        if (!$quezoncity) {
            $quezoncity = Location::create([
                'location_name' => 'Quezon City M.I.C.E. Center',
                'location_code' => 'QC',
                'address' => 'Elliptical Road, Quezon City, Metro Manila',
                'city' => 'Quezon City',
                'province' => 'Metro Manila',
                'zip_code' => '1100',
                'phone' => '(02) 8-988-4242',
                'email' => 'mice@quezoncity.gov.ph',
                'config' => json_encode([
                    'payment_mode' => 'per_person',
                    'base_rate' => 150,
                    'currency' => 'PHP',
                    'operating_hours' => ['start' => '07:00', 'end' => '21:00'],
                    'advance_booking_days' => 180,
                    'cancellation_deadline_hours' => 72,
                    'approval_levels' => ['staff', 'admin'],
                    'discount_tiers' => ['pwd' => 20, 'senior' => 20, 'student' => 20],
                    'requires_full_payment' => true,
                    'payment_policy' => 'Full payment required before reservation confirmation',
                    'ordinance_status' => 'pending',
                    'public_booking_status' => 'coming_soon'
                ]),
                'is_active' => true
            ]);
        }

        // ============ CALOOCAN CITY FACILITIES (From Interview) ============
        
        // 1. Buena Park Sports Complex
        Facility::create([
            'location_id' => $caloocan->id,
            'facility_name' => 'Buena Park Sports Complex',
            'facility_type' => 'sports_complex',
            'description' => 'A multi-purpose sports complex ideal for sports events, tournaments, and large gatherings. Features outdoor courts and ample space for various activities.',
            'capacity' => 500,
            'hourly_rate' => null,
            'per_person_rate' => 150.00,
            'deposit_amount' => 1500.00,
            'amenities' => ['basketball_court', 'parking', 'restrooms', 'lighting', 'sound_system'],
            'rules' => 'No smoking inside the premises. Maintain cleanliness. No alcohol allowed. Sports equipment must be handled with care.',
            'terms_and_conditions' => 'Booking requires 48-hour advance notice. Cancellations must be made at least 48 hours before the event. Deposit is non-refundable but can be used for rescheduling.',
            'is_available' => true,
            'advance_booking_days' => 90,
            'min_booking_hours' => 3,
            'max_booking_hours' => 8,
            'operating_hours' => json_encode([
                'monday' => ['open' => '06:00', 'close' => '22:00'],
                'tuesday' => ['open' => '06:00', 'close' => '22:00'],
                'wednesday' => ['open' => '06:00', 'close' => '22:00'],
                'thursday' => ['open' => '06:00', 'close' => '22:00'],
                'friday' => ['open' => '06:00', 'close' => '22:00'],
                'saturday' => ['open' => '06:00', 'close' => '22:00'],
                'sunday' => ['open' => '06:00', 'close' => '22:00']
            ]),
            'address' => 'Buena Park, Caloocan City, Metro Manila',
            'google_maps_url' => null,
            'status' => 'active',
            'display_order' => 1
        ]);

        // 2. Bulwagan (Function Hall)
        Facility::create([
            'location_id' => $caloocan->id,
            'facility_name' => 'Bulwagan Function Hall',
            'facility_type' => 'function_hall',
            'description' => 'An elegant function hall perfect for weddings, birthdays, corporate events, and seminars. Fully air-conditioned with modern amenities.',
            'capacity' => 300,
            'hourly_rate' => null,
            'per_person_rate' => 120.00,
            'deposit_amount' => 1000.00,
            'amenities' => ['air_conditioning', 'sound_system', 'projector', 'wifi', 'parking', 'kitchen', 'restrooms', 'tables_and_chairs'],
            'rules' => 'No confetti or glitter decorations. Smoking is prohibited indoors. Must coordinate with catering services 48 hours in advance.',
            'terms_and_conditions' => 'Maximum booking is 6 hours. Additional hours charged at regular rate. Setup and cleanup time is included in booking hours.',
            'is_available' => true,
            'advance_booking_days' => 90,
            'min_booking_hours' => 3,
            'max_booking_hours' => 6,
            'operating_hours' => json_encode([
                'monday' => ['open' => '08:00', 'close' => '22:00'],
                'tuesday' => ['open' => '08:00', 'close' => '22:00'],
                'wednesday' => ['open' => '08:00', 'close' => '22:00'],
                'thursday' => ['open' => '08:00', 'close' => '22:00'],
                'friday' => ['open' => '08:00', 'close' => '22:00'],
                'saturday' => ['open' => '08:00', 'close' => '22:00'],
                'sunday' => ['open' => '08:00', 'close' => '22:00']
            ]),
            'address' => 'City General Services Department, Caloocan City',
            'google_maps_url' => null,
            'status' => 'active',
            'display_order' => 2
        ]);

        // 3. Pacquiao Court
        Facility::create([
            'location_id' => $caloocan->id,
            'facility_name' => 'Pacquiao Court',
            'facility_type' => 'sports_complex',
            'description' => 'A covered basketball court named after the boxing legend. Perfect for basketball tournaments, community sports events, and recreational activities.',
            'capacity' => 200,
            'hourly_rate' => null,
            'per_person_rate' => 100.00,
            'deposit_amount' => 600.00,
            'amenities' => ['basketball_court', 'lighting', 'covered', 'restrooms', 'parking'],
            'rules' => 'Proper sports attire required. No street shoes on the court. Clean up after use. Maximum 2 teams at a time.',
            'terms_and_conditions' => 'Booking slots are in 2-hour increments. Courts must be vacated 15 minutes after booking ends.',
            'is_available' => true,
            'advance_booking_days' => 90,
            'min_booking_hours' => 2,
            'max_booking_hours' => 5,
            'operating_hours' => json_encode([
                'monday' => ['open' => '06:00', 'close' => '22:00'],
                'tuesday' => ['open' => '06:00', 'close' => '22:00'],
                'wednesday' => ['open' => '06:00', 'close' => '22:00'],
                'thursday' => ['open' => '06:00', 'close' => '22:00'],
                'friday' => ['open' => '06:00', 'close' => '22:00'],
                'saturday' => ['open' => '06:00', 'close' => '22:00'],
                'sunday' => ['open' => '06:00', 'close' => '22:00']
            ]),
            'address' => 'Caloocan City, Metro Manila',
            'google_maps_url' => null,
            'status' => 'active',
            'display_order' => 3
        ]);

        // 4. Katipunan Hall (for city events - limited public access)
        Facility::create([
            'location_id' => $caloocan->id,
            'facility_name' => 'Katipunan Hall',
            'facility_type' => 'auditorium',
            'description' => 'A formal auditorium primarily used for city events, but available for community organizations and large-scale seminars.',
            'capacity' => 400,
            'hourly_rate' => null,
            'per_person_rate' => 130.00,
            'deposit_amount' => 1200.00,
            'amenities' => ['air_conditioning', 'stage', 'sound_system', 'projector', 'lighting', 'parking', 'restrooms'],
            'rules' => 'Government and LGU events have priority. Formal attire recommended. No food or drinks inside the main hall.',
            'terms_and_conditions' => 'Bookings require advance approval from the City Mayor\'s office. Non-profit organizations may receive discounted rates.',
            'is_available' => true,
            'advance_booking_days' => 90,
            'min_booking_hours' => 3,
            'max_booking_hours' => 8,
            'operating_hours' => json_encode([
                'monday' => ['open' => '07:00', 'close' => '22:00'],
                'tuesday' => ['open' => '07:00', 'close' => '22:00'],
                'wednesday' => ['open' => '07:00', 'close' => '22:00'],
                'thursday' => ['open' => '07:00', 'close' => '22:00'],
                'friday' => ['open' => '07:00', 'close' => '22:00'],
                'saturday' => ['open' => '07:00', 'close' => '20:00'],
                'sunday' => ['open' => '08:00', 'close' => '18:00']
            ]),
            'address' => 'Caloocan City Hall Complex, Caloocan City',
            'google_maps_url' => null,
            'status' => 'active',
            'display_order' => 4
        ]);

        // ============ QUEZON CITY M.I.C.E. CENTER FACILITIES (From Interview) ============
        
        // 1. Convention / Exhibit Hall
        Facility::create([
            'location_id' => $quezoncity->id,
            'facility_name' => 'QC M.I.C.E. Convention & Exhibit Hall',
            'facility_type' => 'convention_center',
            'description' => 'A state-of-the-art convention and exhibit hall suitable for large-scale conferences, trade shows, exhibitions, and city-sponsored programs. Features spacious modern interiors with flexible layout options. **Currently accepting only QC-LGU events while ordinance is being finalized.**',
            'capacity' => 1000,
            'hourly_rate' => null,
            'per_person_rate' => 150.00,
            'deposit_amount' => null,
            'amenities' => ['air_conditioning', 'wifi', 'projector', 'sound_system', 'stage', 'exhibition_booths', 'parking', 'restrooms', 'accessibility_features'],
            'rules' => 'LGU and government-sponsored events have priority. Exhibition materials must be fire-rated. No smoking anywhere in the building.',
            'terms_and_conditions' => 'Currently prioritizing Quezon City LGU events. Private bookings pending approval of M.I.C.E. Center ordinance. Advance booking required with detailed event proposal.',
            'is_available' => false,
            'advance_booking_days' => 180,
            'min_booking_hours' => 4,
            'max_booking_hours' => 12,
            'operating_hours' => json_encode([
                'monday' => ['open' => '07:00', 'close' => '21:00'],
                'tuesday' => ['open' => '07:00', 'close' => '21:00'],
                'wednesday' => ['open' => '07:00', 'close' => '21:00'],
                'thursday' => ['open' => '07:00', 'close' => '21:00'],
                'friday' => ['open' => '07:00', 'close' => '21:00'],
                'saturday' => ['open' => '08:00', 'close' => '20:00'],
                'sunday' => ['open' => '08:00', 'close' => '18:00']
            ]),
            'address' => 'Elliptical Road, Quezon City, Metro Manila',
            'google_maps_url' => null,
            'status' => 'active',
            'display_order' => 1
        ]);

        // 2. Breakout Room 1
        Facility::create([
            'location_id' => $quezoncity->id,
            'facility_name' => 'M.I.C.E. Breakout Room 1',
            'facility_type' => 'meeting_room',
            'description' => 'A modern breakout room perfect for small to medium-sized seminars, training sessions, and workshops. Fully equipped with presentation facilities. **Currently accepting only QC-LGU events while ordinance is being finalized.**',
            'capacity' => 50,
            'hourly_rate' => null,
            'per_person_rate' => 100.00,
            'deposit_amount' => null,
            'amenities' => ['air_conditioning', 'wifi', 'projector', 'whiteboard', 'sound_system', 'tables_and_chairs', 'restrooms'],
            'rules' => 'Maintain room cleanliness. Equipment must be returned to original positions. No food with strong odors.',
            'terms_and_conditions' => 'Breakout rooms are in high demand. Booking confirmation sent within 48 hours of request. Government seminars receive priority. Currently prioritizing QC-LGU events pending ordinance approval.',
            'is_available' => false,
            'advance_booking_days' => 180,
            'min_booking_hours' => 2,
            'max_booking_hours' => 8,
            'operating_hours' => json_encode([
                'monday' => ['open' => '07:00', 'close' => '21:00'],
                'tuesday' => ['open' => '07:00', 'close' => '21:00'],
                'wednesday' => ['open' => '07:00', 'close' => '21:00'],
                'thursday' => ['open' => '07:00', 'close' => '21:00'],
                'friday' => ['open' => '07:00', 'close' => '21:00'],
                'saturday' => ['open' => '08:00', 'close' => '18:00'],
                'sunday' => ['open' => '08:00', 'close' => '18:00']
            ]),
            'address' => 'QC M.I.C.E. Center, 2nd Floor, Quezon City',
            'google_maps_url' => null,
            'status' => 'active',
            'display_order' => 2
        ]);

        // 3. Breakout Room 2
        Facility::create([
            'location_id' => $quezoncity->id,
            'facility_name' => 'M.I.C.E. Breakout Room 2',
            'facility_type' => 'meeting_room',
            'description' => 'Another versatile breakout room ideal for corporate meetings, team building activities, and educational seminars with smaller groups. **Currently accepting only QC-LGU events while ordinance is being finalized.**',
            'capacity' => 40,
            'hourly_rate' => null,
            'per_person_rate' => 100.00,
            'deposit_amount' => null,
            'amenities' => ['air_conditioning', 'wifi', 'projector', 'whiteboard', 'sound_system', 'tables_and_chairs', 'restrooms'],
            'rules' => 'Respect scheduled time slots. Report any equipment damage immediately. Keep noise levels reasonable.',
            'terms_and_conditions' => 'Same terms as Breakout Room 1. Can be combined with Breakout Room 1 for larger events with prior approval. Currently prioritizing QC-LGU events pending ordinance approval.',
            'is_available' => false,
            'advance_booking_days' => 180,
            'min_booking_hours' => 2,
            'max_booking_hours' => 8,
            'operating_hours' => json_encode([
                'monday' => ['open' => '07:00', 'close' => '21:00'],
                'tuesday' => ['open' => '07:00', 'close' => '21:00'],
                'wednesday' => ['open' => '07:00', 'close' => '21:00'],
                'thursday' => ['open' => '07:00', 'close' => '21:00'],
                'friday' => ['open' => '07:00', 'close' => '21:00'],
                'saturday' => ['open' => '08:00', 'close' => '18:00'],
                'sunday' => ['open' => '08:00', 'close' => '18:00']
            ]),
            'address' => 'QC M.I.C.E. Center, 2nd Floor, Quezon City',
            'google_maps_url' => null,
            'status' => 'active',
            'display_order' => 3
        ]);

        // 4. QC M.I.C.E. Auditorium (Under Construction - 3rd & 4th floors)
        Facility::create([
            'location_id' => $quezoncity->id,
            'facility_name' => 'QC M.I.C.E. Auditorium',
            'facility_type' => 'auditorium',
            'description' => 'A large-capacity auditorium currently under construction. Once completed, it will serve as a premier venue for major conferences, concerts, and city-wide events.',
            'capacity' => 800,
            'hourly_rate' => null,
            'per_person_rate' => 200.00,
            'deposit_amount' => null,
            'amenities' => ['air_conditioning', 'stage', 'professional_sound_system', 'lighting', 'projection', 'backstage_area', 'vip_lounge', 'parking', 'accessibility_features'],
            'rules' => 'Facility not yet available. Rules and guidelines will be announced upon completion.',
            'terms_and_conditions' => 'Expected to open in 2026. Pre-booking not yet available. Updates will be posted on the QC LGU website.',
            'is_available' => false,
            'advance_booking_days' => 180,
            'min_booking_hours' => 4,
            'max_booking_hours' => 12,
            'operating_hours' => null,
            'address' => 'QC M.I.C.E. Center, 3rd & 4th Floor, Quezon City',
            'google_maps_url' => null,
            'status' => 'under_construction',
            'display_order' => 4
        ]);

        $this->command->info('SUCCESS: Facilities seeded successfully! Created facilities for Caloocan City and Quezon City.');
    }
}
