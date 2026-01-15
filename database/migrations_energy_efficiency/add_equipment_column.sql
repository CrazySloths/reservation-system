-- ========================================================================
-- ADD EQUIPMENT_PROVIDED COLUMN TO FACILITY_BOOKING_CONFIRMATIONS
-- ========================================================================
-- Add equipment provision tracking to Energy Efficiency database
-- ========================================================================

USE ener_nova_capri;

-- Check if column doesn't exist before adding
ALTER TABLE facility_booking_confirmations
ADD COLUMN IF NOT EXISTS equipment_provided JSON COMMENT '[
    {
        "name": "Projector",
        "quantity": 2,
        "model": "Epson EB-X05",
        "provided_by": "LGU Caloocan"
    },
    {
        "name": "Wireless Microphone",
        "quantity": 4,
        "model": "Shure SM58",
        "provided_by": "LGU Caloocan"
    },
    {
        "name": "Laptop",
        "quantity": 1,
        "model": "Dell Latitude",
        "provided_by": "LGU Caloocan"
    }
]' AFTER confirmed_speakers;

-- ========================================================================
-- NOTES
-- ========================================================================
-- This column stores the list of equipment that LGU provides for the seminar
-- Examples: projectors, microphones, laptops, sound systems, etc.
-- Data comes from the LGU admin when they accept the seminar request
-- ========================================================================

