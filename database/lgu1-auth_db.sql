CREATE DATABASE lgu1_auth_db;
USE lgu1_auth_db;

-- Districts table
CREATE TABLE districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    district_number INT NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL
);

-- Barangays table
CREATE TABLE barangays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    district_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    alternate_name VARCHAR(200),
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE CASCADE,
    INDEX idx_district (district_id)
);

-- Roles table (lookup table for roles)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Permissions table (lookup table for permissions)
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255)
);

-- Subsystems table (lookup table for subsystems)
CREATE TABLE subsystems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Subsystem roles table (roles specific to each subsystem)
CREATE TABLE subsystem_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subsystem_id INT NOT NULL,
    role_name VARCHAR(100) NOT NULL,
    description TEXT,
    FOREIGN KEY (subsystem_id) REFERENCES subsystems(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subsystem_role (subsystem_id, role_name)
);

-- Users table (enhanced with more profile fields and email verification)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT DEFAULT NULL,
    subsystem_id INT DEFAULT NULL,
    subsystem_role_id INT DEFAULT NULL,
    birthdate DATE,
    mobile_number VARCHAR(15),
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    civil_status ENUM('single', 'married', 'divorced', 'widowed', 'separated') DEFAULT NULL,
    nationality VARCHAR(50) DEFAULT 'Filipino',
    district_id INT,
    barangay_id INT,
    current_address TEXT,
    zip_code VARCHAR(10),
    valid_id_type VARCHAR(50),
    valid_id_front_image VARCHAR(255),
    valid_id_back_image VARCHAR(255),
    selfie_with_id_image VARCHAR(255),
    id_verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    id_verified_at DATETIME DEFAULT NULL,
    id_verified_by INT DEFAULT NULL,
    id_verification_notes TEXT DEFAULT NULL,
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    last_login DATETIME DEFAULT NULL,
    is_email_verified TINYINT(1) DEFAULT 0,
    email_verification_token VARCHAR(255) DEFAULT NULL,
    email_verified_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE SET NULL,
    FOREIGN KEY (barangay_id) REFERENCES barangays(id) ON DELETE SET NULL,
    FOREIGN KEY (id_verified_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL,
    FOREIGN KEY (subsystem_id) REFERENCES subsystems(id) ON DELETE SET NULL,
    FOREIGN KEY (subsystem_role_id) REFERENCES subsystem_roles(id) ON DELETE SET NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_district (district_id),
    INDEX idx_barangay (barangay_id),
    INDEX idx_id_verification (id_verification_status)
);

-- Pivot table to assign permissions to roles
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Pivot table to assign permissions directly to users (overrides)
CREATE TABLE user_permissions (
    user_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (user_id, permission_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Audit logs table for tracking user actions
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Password resets table
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reset_token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- OTP table for user authentication (email/2FA)
CREATE TABLE user_otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    otp_code VARCHAR(20) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Insert subsystems
INSERT INTO subsystems (name) VALUES
('Infrastructure Project Management'),
('Utility Billing and Monitoring Management (Water, Electricity)'),
('Road and Transportation Infrastructure Monitoring'),
('Public Facilities Reservation System'),
('Community Infrastructure Maintenance Management'),
('Urban Planning and Development'),
('Land Registration and Titling System'),
('Housing and Resettlement Management'),
('Renewable Energy Project Management'),
('Energy Efficiency and Conservative Management');

-- Insert roles (global roles)
INSERT INTO roles (name) VALUES
('super admin'),
('citizen');

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
(8, 'Administrative & Records Staff', 'Administrative and records management'),
(8, 'Census & Planning Staff', 'Conduct surveys and verify eligibility'),
(8, 'Housing & Resettlement Staff', 'Manage unit allocation and resettlement'),
(8, 'Support Services Staff', 'Handle loans and financing'),
(8, 'Community Development Officers', 'Community development and coordination'),
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

-- Insert permissions
INSERT INTO permissions (name, description) VALUES
('view_users', 'View user list and details'),
('edit_users', 'Edit user information'),
('delete_users', 'Delete users'),
('manage_roles', 'Manage roles and assignments'),
('view_audit_logs', 'View audit logs'),
('reset_passwords', 'Reset user passwords'),
('access_housing', 'Access housing subsystem'),
('access_utility', 'Access utility subsystem');

-- Enhanced permissions for housing and resettlement system
INSERT INTO permissions (name, description) VALUES
('process_documents', 'Process client document submissions'),
('conduct_surveys', 'Conduct beneficiary surveys and verification'),
('verify_eligibility', 'Verify beneficiary eligibility'),
('collect_socioeconomic_data', 'Collect and manage socio-economic data'),
('manage_digital_registry', 'Manage digital registry and MIS system'),
('track_applications', 'Track and monitor application status'),
('manage_raffles', 'Manage unit allocation raffles'),
('monitor_occupancy', 'Monitor unit occupancy status'),
('coordinate_resettlement', 'Coordinate resettlement execution and turnover'),
('process_loans', 'Process and manage loan applications'),
('manage_financing', 'Secure and manage financing options'),
('coordinate_utilities', 'Coordinate utility services for occupants');

-- Assign permissions to global roles
INSERT INTO role_permissions (role_id, permission_id) VALUES
-- Super Admin: All permissions (1-20)
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11), (1, 12), (1, 13), (1, 14), (1, 15), (1, 16), (1, 17), (1, 18), (1, 19), (1, 20),
-- System Admin: Basic admin permissions
(2, 1), (2, 2), (2, 4), (2, 5), (2, 6);

-- Insert districts
INSERT INTO districts (district_number, name) VALUES
(1, 'District 1'),
(2, 'District 2'),
(3, 'District 3'),
(4, 'District 4'),
(5, 'District 5'),
(6, 'District 6');

-- Insert barangays for District 1
INSERT INTO barangays (district_id, name, alternate_name) VALUES
(1, 'Alicia', 'Bago Bantay'),
(1, 'Bagong Pag-asa', 'North-EDSA, Diliman (southern part), Triangle Park (southern triangle)'),
(1, 'Bahay Toro', 'Project 8, Pugadlawin'),
(1, 'Balingasa', 'Balintawak, Cloverleaf'),
(1, 'Bungad', 'Project 7'),
(1, 'Damar', 'Balintawak'),
(1, 'Damayan', 'San Francisco del Monte, Frisco'),
(1, 'Del Monte', 'San Francisco del Monte, Frisco'),
(1, 'Katipunan', 'Muñoz'),
(1, 'Lourdes', 'Santa Mesa Heights'),
(1, 'Maharlika', 'Santa Mesa Heights'),
(1, 'Manresa', 'Balintawak, San Francisco del Monte, Frisco'),
(1, 'Mariblo', 'San Francisco del Monte, Frisco'),
(1, 'Masambong', 'San Francisco del Monte, Frisco'),
(1, 'N.S. Amoranto (Gintong Silahis)', 'La Loma'),
(1, 'Nayong Kanluran', 'West Avenue'),
(1, 'Paang Bundok', 'La Loma'),
(1, 'Pag-ibig sa Nayon', 'Balintawak'),
(1, 'Paltok', 'San Francisco del Monte, Frisco'),
(1, 'Paraiso', 'San Francisco del Monte, Frisco'),
(1, 'Phil-Am', 'West Triangle, Diliman'),
(1, 'Project 6', 'Diliman (southeast quarter), Triangle Park (southern half)'),
(1, 'Ramon Magsaysay', 'Bago Bantay, Muñoz'),
(1, 'Saint Peter', 'Santa Mesa Heights'),
(1, 'Salvacion', 'La Loma'),
(1, 'San Antonio', 'San Francisco del Monte, Frisco'),
(1, 'San Isidro Labrador', 'La Loma'),
(1, 'San Jose', 'La Loma'),
(1, 'Santa Cruz', 'Pantranco, Heroes Hill'),
(1, 'Santa Teresita', 'Santa Mesa Heights'),
(1, 'Sto. Cristo', 'Bago Bantay'),
(1, 'Santo Domingo (Matalahib)', 'Matalahib, Santa Mesa Heights'),
(1, 'Siena', 'Santa Mesa Heights'),
(1, 'Talayan', 'San Francisco del Monte, Frisco'),
(1, 'Vasra', 'Diliman (mostly)'),
(1, 'Veterans Village', 'Project 7, Muñoz'),
(1, 'West Triangle', 'Diliman');

-- Insert barangays for District 2
INSERT INTO barangays (district_id, name, alternate_name) VALUES
(2, 'Bagong Silangan', 'Payatas'),
(2, 'Batasan Hills', 'Constitution Hills'),
(2, 'Commonwealth', 'Manggahan, Litex'),
(2, 'Holy Spirit', 'Don Antonio, Luzon'),
(2, 'Payatas', 'Litex');

-- Insert barangays for District 3
INSERT INTO barangays (district_id, name, alternate_name) VALUES
(3, 'Amihan', 'Project 3'),
(3, 'Bagumbayan', 'Eastwood, Acropolis, Citybank, Gentex, Libis'),
(3, 'Bagumbuhay', 'Project 4'),
(3, 'Bayanihan', 'Project 4'),
(3, 'Blue Ridge A', 'Project 4'),
(3, 'Blue Ridge B', 'Project 4'),
(3, 'Camp Aguinaldo', 'Armed Forces (AFP), Camp General Emilio Aguinaldo'),
(3, 'Claro (Quirino 3-B)', 'Project 3'),
(3, 'Dioquino Zobel', 'Project 4'),
(3, 'Duyan-duyan', 'Project 3'),
(3, 'E. Rodriguez', 'Project 5, Cubao'),
(3, 'East Kamias', 'Project 1, Kamias'),
(3, 'Escopa I', 'Project 4'),
(3, 'Escopa II', 'Project 4'),
(3, 'Escopa III', 'Project 4'),
(3, 'Escopa IV', 'Project 4'),
(3, 'Libis', 'Camp Atienza, Eastwood'),
(3, 'Loyola Heights', 'Katipunan'),
(3, 'Mangga', 'Cubao, Anonas, T.I.P.'),
(3, 'Marilag', 'Project 4'),
(3, 'Masagana', 'Project 4, Jacobo Zobel'),
(3, 'Matandang Balara', 'Old Balara, Luzon, Tandang Sora'),
(3, 'Milagrosa', 'Project 4'),
(3, 'Pansol', 'Balara, Katipunan'),
(3, 'Quirino 2-A', 'Project 2, Anonas'),
(3, 'Quirino 2-B', 'Project 2, Anonas'),
(3, 'Quirino 2-C', 'Project 2, Anonas'),
(3, 'Quirino 3-A', 'Project 3, Anonas'),
(3, 'St. Ignatius', 'Project 4, Katipunan'),
(3, 'San Roque', 'Cubao'),
(3, 'Silangan', 'Cubao'),
(3, 'Socorro', 'Cubao, Araneta City'),
(3, 'Tagumpay', 'Project 4'),
(3, 'Ugong Norte', 'Green Meadows, Corinthian, Ortigas'),
(3, 'Villa Maria Clara', 'Project 4'),
(3, 'West Kamias', 'Project 5, Kamias'),
(3, 'White Plains', 'Camp Aguinaldo, Katipunan');

-- Insert barangays for District 4
INSERT INTO barangays (district_id, name, alternate_name) VALUES
(4, 'Bagong Lipunan ng Crame', 'Camp Crame, Philippine National Police (PNP)'),
(4, 'Botocan', 'Diliman (northern half)'),
(4, 'Central', 'Diliman, Quezon City Hall'),
(4, 'Damayang Lagi', 'New Manila'),
(4, 'Don Manuel', 'Galas'),
(4, 'Doña Aurora', 'Galas'),
(4, 'Doña Imelda', 'Galas, Sta. Mesa (border with City of Manila)'),
(4, 'Doña Josefa', 'Galas'),
(4, 'Horseshoe', 'New Manila'),
(4, 'Immaculate Concepcion', 'Cubao'),
(4, 'Kalusugan', 'St. Luke\'s'),
(4, 'Kamuning', 'Project 1, Scout Area'),
(4, 'Kaunlaran', 'Cubao'),
(4, 'Kristong Hari', 'E. Rodriguez, New Manila'),
(4, 'Krus na Ligas', 'Diliman'),
(4, 'Laging Handa', 'Diliman, Scout Area'),
(4, 'Malaya', 'Diliman'),
(4, 'Mariana', 'New Manila'),
(4, 'Obrero', 'Diliman (northern half), Project 1 (southern half)'),
(4, 'Old Capitol Site', 'Diliman'),
(4, 'Paligsahan', 'Diliman, Scout Area'),
(4, 'Pinagkaisahan', 'Cubao'),
(4, 'Pinyahan', 'Diliman, Triangle Park (northern triangle)'),
(4, 'Roxas', 'Project 1'),
(4, 'Sacred Heart', 'Kamuning, Diliman, Scout Area'),
(4, 'San Isidro Galas', 'Galas'),
(4, 'San Martin de Porres', 'Cubao, Arayat'),
(4, 'San Vicente', 'Diliman, UP Bliss'),
(4, 'Santol', 'Galas'),
(4, 'Sikatuna Village', 'Diliman'),
(4, 'South Triangle', 'Diliman, Scout Area'),
(4, 'Santo Niño', 'Galas'),
(4, 'Tatalon', 'Sanctuarium, Araneta Avenue'),
(4, 'Teacher\'s Village East', 'Diliman'),
(4, 'Teacher\'s Village West', 'Diliman'),
(4, 'U.P. Campus', 'Diliman'),
(4, 'U.P. Village', 'Diliman'),
(4, 'Valencia', 'New Manila, Gilmore Ave., N. Domingo Ave.');

-- Insert barangays for District 5
INSERT INTO barangays (district_id, name, alternate_name) VALUES
(5, 'Bagbag', 'Novaliches District, Sauyo'),
(5, 'Capri', 'Novaliches District'),
(5, 'Fairview', 'Novaliches District, La Mesa, West Fairview'),
(5, 'Gulod', 'Novaliches District, Susano, Nitang'),
(5, 'Greater Lagro', 'Novaliches District, Lagro, Fairview'),
(5, 'Kaligayahan', 'Novaliches District, Zabarte'),
(5, 'Nagkaisang Nayon', 'Novaliches District, General Luis'),
(5, 'North Fairview', 'Novaliches District'),
(5, 'Novaliches Proper', 'Novaliches Bayan, Glori, Bayan'),
(5, 'Pasong Putik Proper', 'Novaliches District, Maligaya Drive, Fairview'),
(5, 'San Agustin', 'Novaliches District, Susano'),
(5, 'San Bartolome', 'Novaliches District, Holy Cross'),
(5, 'Sta. Lucia', 'Novaliches District, San Gabriel'),
(5, 'Sta. Monica', 'Novaliches District');

-- Subsystem role permissions (link subsystem roles to permissions)
CREATE TABLE subsystem_role_permissions (
    subsystem_role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (subsystem_role_id, permission_id),
    FOREIGN KEY (subsystem_role_id) REFERENCES subsystem_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Assign permissions to subsystem roles
INSERT INTO subsystem_role_permissions (subsystem_role_id, permission_id) VALUES
-- Housing Administrative & Records Staff (subsystem_role_id = 29): Records and document processing
(29, 7), (29, 9), (29, 13), (29, 14),
-- Housing Census & Planning Staff (subsystem_role_id = 30): Surveys and eligibility verification
(30, 7), (30, 10), (30, 11), (30, 12),
-- Housing & Resettlement Staff (subsystem_role_id = 31): Unit allocation and resettlement
(31, 7), (31, 15), (31, 16), (31, 17),
-- Housing Support Services Staff (subsystem_role_id = 32): Loans and financing
(32, 7), (32, 18), (32, 19), (32, 20),
-- Housing Community Development Officers (subsystem_role_id = 33): Community coordination
(33, 7), (33, 17), (33, 20),
-- Housing Applicant (subsystem_role_id = 34): Basic access
(34, 7),
-- Energy Admin (subsystem_role_id = 8)
(8, 1), (8, 2), (8, 3), (8, 4), (8, 5), (8, 6), (8, 8),
-- Energy Coordinator Staff (subsystem_role_id = 9)
(9, 8),
-- Energy Residents (subsystem_role_id = 10)
(10, 8);

-- Insert barangays for District 6
INSERT INTO barangays (district_id, name, alternate_name) VALUES
(6, 'Apolonio Samson', 'Balintawak, Kaingin, Kangkong'),
(6, 'Baesa', 'Project 8, Novaliches District'),
(6, 'Balon Bato', 'Balintawak'),
(6, 'Culiat', 'Tandang Sora'),
(6, 'New Era', 'Iglesia ni Cristo/Central, Tandang Sora'),
(6, 'Pasong Tamo', 'Pingkian, Philand'),
(6, 'Sangandaan', 'Project 8'),
(6, 'Sauyo', 'Novaliches District'),
(6, 'Talipapa', 'Novaliches District'),
(6, 'Tandang Sora', 'Banlat'),
(6, 'Unang Sigaw', 'Balintawak, Cloverleaf');

