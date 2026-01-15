-- ========================================================================
-- TEST DATA FOR ENERGY EFFICIENCY DATABASE (ener_nova_capri)
-- ========================================================================
-- 
-- PURPOSE: Add sample seminar requests to test the integration
-- 
-- INSTRUCTIONS:
-- 1. Open phpMyAdmin
-- 2. Select the 'ener_nova_capri' database
-- 3. Go to SQL tab
-- 4. Copy and paste these INSERT statements
-- 5. Click "Go" to execute
-- 6. Refresh your LGU admin panel to see the new pending requests
-- 
-- ========================================================================

USE ener_nova_capri;

-- ========================================================================
-- Sample Seminar 1: Solar Energy Workshop
-- ========================================================================

INSERT INTO `seminars` (
    `seminar_id`,
    `seminar_title`,
    `seminar_date`,
    `start_time`,
    `end_time`,
    `description`,
    `location`,
    `target_area`,
    `attachments_path`,
    `created_at`,
    `seminar_image_url`,
    `is_archived`
) VALUES (
    29,
    'Solar Energy Benefits and Installation Guide',
    '2026-02-15',
    '09:00:00',
    '12:00:00',
    'Learn about the benefits of solar energy, government incentives for solar panel installation, and step-by-step guide on how to install residential solar systems. This seminar includes demonstrations and Q&A with certified solar technicians.',
    'Community Center (To be assigned by LGU)',
    'AREA 1',
    'uploads/seminar_attachments/solar_guide_2026.pdf',
    NOW(),
    'assets/seminar_img/solar.jpg',
    0
);

-- Add some attendees for this seminar
INSERT INTO `seminar_joins` (`seminar_id`, `user_id`, `joined_at`) VALUES
(29, 88, NOW()),
(29, 82, NOW());

-- ========================================================================
-- Sample Seminar 2: LED Lighting Efficiency
-- ========================================================================

INSERT INTO `seminars` (
    `seminar_id`,
    `seminar_title`,
    `seminar_date`,
    `start_time`,
    `end_time`,
    `description`,
    `location`,
    `target_area`,
    `attachments_path`,
    `created_at`,
    `seminar_image_url`,
    `is_archived`
) VALUES (
    30,
    'LED Lighting: Save Money, Save Energy',
    '2026-02-20',
    '14:00:00',
    '16:30:00',
    'Discover how switching to LED lighting can reduce your electricity bill by up to 80%. This seminar covers LED types, pricing comparison, lifespan calculations, and FREE LED bulb distribution to all attendees!',
    'Barangay Hall (Facility needed)',
    'AREA 2',
    'uploads/seminar_attachments/led_comparison_guide.pdf',
    NOW(),
    'assets/seminar_img/led.jpg',
    0
);

-- Add some attendees
INSERT INTO `seminar_joins` (`seminar_id`, `user_id`, `joined_at`) VALUES
(30, 88, NOW()),
(30, 82, NOW()),
(30, 2011, NOW());

-- ========================================================================
-- Sample Seminar 3: Smart Home Energy Management
-- ========================================================================

INSERT INTO `seminars` (
    `seminar_id`,
    `seminar_title`,
    `seminar_date`,
    `start_time`,
    `end_time`,
    `description`,
    `location`,
    `target_area`,
    `attachments_path`,
    `created_at`,
    `seminar_image_url`,
    `is_archived`
) VALUES (
    31,
    'Smart Home Technology for Energy Savings',
    '2026-03-05',
    '13:00:00',
    '17:00:00',
    'Explore modern smart home devices that help monitor and reduce energy consumption. Topics include smart thermostats, automated lighting, energy monitoring apps, and IoT integration. Hands-on demos with actual devices included.',
    'Multi-Purpose Hall or Conference Room needed',
    'AREA 3',
    'uploads/seminar_attachments/smart_home_tech.pdf',
    NOW(),
    'assets/seminar_img/smarthome.jpg',
    0
);

-- Add attendees
INSERT INTO `seminar_joins` (`seminar_id`, `user_id`, `joined_at`) VALUES
(31, 88, NOW()),
(31, 82, NOW()),
(31, 2011, NOW());

-- ========================================================================
-- Sample Seminar 4: Appliance Energy Audit
-- ========================================================================

INSERT INTO `seminars` (
    `seminar_id`,
    `seminar_title`,
    `seminar_date`,
    `start_time`,
    `end_time`,
    `description`,
    `location`,
    `target_area`,
    `attachments_path`,
    `created_at`,
    `seminar_image_url`,
    `is_archived`
) VALUES (
    32,
    'Home Appliance Energy Audit and Optimization',
    '2026-03-12',
    '10:00:00',
    '13:00:00',
    'Learn how to conduct your own home energy audit. Identify vampire power loads, understand appliance energy ratings, and get tips on optimal usage patterns for refrigerators, air conditioners, washing machines, and more.',
    'Large venue needed for 200+ participants',
    'AREA 4',
    'uploads/seminar_attachments/energy_audit_checklist.pdf',
    NOW(),
    'assets/seminar_img/audit.jpg',
    0
);

-- Add many attendees (popular seminar!)
INSERT INTO `seminar_joins` (`seminar_id`, `user_id`, `joined_at`) VALUES
(32, 88, NOW()),
(32, 82, NOW()),
(32, 2011, NOW());

-- ========================================================================
-- Sample Seminar 5: Electric Vehicle Charging at Home
-- ========================================================================

INSERT INTO `seminars` (
    `seminar_id`,
    `seminar_title`,
    `seminar_date`,
    `start_time`,
    `end_time`,
    `description`,
    `location`,
    `target_area`,
    `attachments_path`,
    `created_at`,
    `seminar_image_url`,
    `is_archived`
) VALUES (
    33,
    'Electric Vehicle Home Charging: Cost Analysis',
    '2026-03-20',
    '15:00:00',
    '18:00:00',
    'Planning to buy an electric vehicle? This seminar covers home charging setup costs, electricity consumption calculations, time-of-use rates, and how to optimize charging schedules to minimize your electric bill.',
    'Conference room preferred',
    'AREA 1',
    'uploads/seminar_attachments/ev_charging_guide.pdf',
    NOW(),
    'assets/seminar_img/ev.jpg',
    0
);

-- Add attendees
INSERT INTO `seminar_joins` (`seminar_id`, `user_id`, `joined_at`) VALUES
(33, 88, NOW()),
(33, 82, NOW());

-- ========================================================================
-- VERIFICATION QUERY
-- ========================================================================
-- Run this to verify the data was inserted correctly:

SELECT 
    s.seminar_id,
    s.seminar_title,
    s.seminar_date,
    s.start_time,
    s.end_time,
    s.target_area,
    COUNT(sj.join_id) as total_attendees
FROM seminars s
LEFT JOIN seminar_joins sj ON s.seminar_id = sj.seminar_id
WHERE s.seminar_id >= 29
GROUP BY s.seminar_id
ORDER BY s.seminar_date ASC;

-- ========================================================================
-- NOTES
-- ========================================================================
-- 
-- ✅ All seminars are scheduled for future dates (Feb-Mar 2026)
-- ✅ All seminars have is_archived = 0 (active)
-- ✅ Each seminar has different time slots and areas
-- ✅ Attendee counts vary (2-3 people per seminar for testing)
-- ✅ Using existing user IDs from your database (82, 88, 2011)
-- 
-- After inserting this data:
-- 1. Go to your LGU admin panel
-- 2. Click "Program Requests" in the sidebar
-- 3. You should see 5 new pending requests
-- 4. Click "Review Request" to see details
-- 5. Click "Accept & Assign Facility" to process
-- 6. Fill in facility, speakers, and budget
-- 7. Check phpMyAdmin > facility_booking_confirmations table
-- 
-- ========================================================================

