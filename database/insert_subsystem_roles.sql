-- Clear existing subsystem roles and insert new ones
DELETE FROM subsystem_roles;

-- Insert subsystem-specific roles for all subsystems
INSERT INTO subsystem_roles (subsystem_id, role_name, description) VALUES
-- Infrastructure Project Management (subsystem_id = 1)
(1, 'Admin', 'Infrastructure system administrator'),
(1, 'Project Manager', 'Manage infrastructure projects'),
(1, 'Engineer', 'Technical engineering support'),
(1, 'Contractor', 'External contractor access'),
-- Utility Billing and Monitoring (subsystem_id = 2)
(2, 'Admin', 'Utility system administrator'),
(2, 'Billing Officer', 'Manage utility billing'),
(2, 'Meter Reader', 'Read and monitor meters'),
(2, 'Customer', 'Utility customer access'),
-- Road and Transportation Infrastructure Monitoring (subsystem_id = 3)
(3, 'Admin', 'Transportation system administrator'),
(3, 'Inspector', 'Infrastructure inspection and monitoring'),
(3, 'Maintenance Staff', 'Road maintenance operations'),
(3, 'Citizen', 'Public transportation access'),
-- Public Facilities Reservation (subsystem_id = 4)
(4, 'Admin', 'Facilities system administrator'),
(4, 'Facility Manager', 'Manage facility operations'),
(4, 'Reservations Staff', 'Handle reservations'),
(4, 'Applicant', 'Facility reservation applicant'),
-- Community Infrastructure Maintenance (subsystem_id = 5)
(5, 'Admin', 'Maintenance system administrator'),
(5, 'Maintenance Supervisor', 'Supervise maintenance operations'),
(5, 'Technician', 'Perform maintenance tasks'),
(5, 'Resident', 'Report maintenance issues'),
-- Urban Planning and Development (subsystem_id = 6)
(6, 'Admin', 'Planning system administrator'),
(6, 'Urban Planner', 'City planning and development'),
(6, 'Zoning Officer', 'Manage zoning regulations'),
(6, 'Developer', 'Development project access'),
-- Land Registration and Titling System (subsystem_id = 7)
(7, 'Cremco Officer/Encoder', 'Data encoding and processing'),
(7, 'Survey & Mapping Specialist', 'Land surveying and mapping services'),
(7, 'Valuation Officer', 'Property valuation services'),
(7, 'Legal Advisor', 'Legal consultation and advice'),
(7, 'Technical Consultant', 'Technical consultation services'),
(7, 'Approver/Review Officer', 'Review and approve applications'),
(7, 'Administrator/System Manager', 'System administration and management'),
(7, 'Citizen', 'Land registration citizen access'),
-- Housing and Resettlement Management (subsystem_id = 8)
(8, 'Admin', 'Housing system administrator'),
(8, 'Census & Planning Staff', 'Conduct surveys and verify eligibility'),
(8, 'MIS Officer', 'Manage digital registry and MIS'),
(8, 'Records Staff', 'Administrative and records management'),
(8, 'Housing & Resettlement Staff', 'Manage unit allocation and resettlement'),
(8, 'Support Services Staff', 'Handle loans and financing'),
(8, 'Applicant', 'Housing applicant access'),
-- Renewable Energy Project Management (subsystem_id = 9)
(9, 'Admin', 'Renewable energy system administrator'),
(9, 'Project Coordinator', 'Coordinate renewable energy projects'),
(9, 'Technical Officer', 'Technical renewable energy support'),
(9, 'Community Representative', 'Community renewable energy access'),
-- Energy Efficiency and Conservation (subsystem_id = 10)
(10, 'Admin', 'Energy system administrator'),
(10, 'Coordinator Staff', 'Energy program coordination'),
(10, 'Residents', 'Resident energy management access');