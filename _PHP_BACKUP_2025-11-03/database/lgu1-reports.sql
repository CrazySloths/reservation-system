-- LGU1 Centralized Reports Database - Data Warehouse Design
-- All subsystem transaction outputs are logged here

CREATE DATABASE IF NOT EXISTS lgu1_reports_db;
USE lgu1_reports_db;

-- UNIFIED DATA MODEL - DIMENSION TABLES

-- Master Citizens Registry (Single Source of Truth)
CREATE TABLE dim_citizens (
    citizen_id VARCHAR(36) PRIMARY KEY,
    full_name VARCHAR(200) NOT NULL,
    birth_date DATE,
    gender ENUM('M', 'F', 'Other'),
    civil_status ENUM('Single', 'Married', 'Widowed', 'Separated'),
    contact_number VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    barangay_code VARCHAR(10),
    zone_code VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (full_name),
    INDEX idx_barangay (barangay_code),
    INDEX idx_zone (zone_code)
);

-- Master Projects Registry (All Infrastructure/Housing/Utility Projects)
CREATE TABLE dim_projects (
    project_id VARCHAR(36) PRIMARY KEY,
    project_name VARCHAR(200) NOT NULL,
    project_type ENUM('Housing', 'Infrastructure', 'Utility', 'Road', 'Energy', 'Facility') NOT NULL,
    subsystem_source VARCHAR(100) NOT NULL,
    location_code VARCHAR(20),
    barangay_code VARCHAR(10),
    status ENUM('Planning', 'Ongoing', 'Completed', 'Cancelled') DEFAULT 'Planning',
    budget_allocated DECIMAL(15,2),
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (project_type),
    INDEX idx_subsystem (subsystem_source),
    INDEX idx_location (location_code),
    INDEX idx_status (status)
);

-- Master Locations Registry (Standardized Geographic Codes)
CREATE TABLE dim_locations (
    location_code VARCHAR(20) PRIMARY KEY,
    barangay_code VARCHAR(10) NOT NULL,
    barangay_name VARCHAR(100) NOT NULL,
    zone_code VARCHAR(10),
    zone_name VARCHAR(50),
    district VARCHAR(50),
    coordinates_lat DECIMAL(10, 8),
    coordinates_lng DECIMAL(11, 8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_barangay (barangay_code),
    INDEX idx_zone (zone_code)
);

-- FACT TABLES (Star Schema)

-- Citizen Services Fact Table
CREATE TABLE fact_citizen_services (
    fact_id VARCHAR(36) PRIMARY KEY,
    citizen_id VARCHAR(36) NOT NULL,
    project_id VARCHAR(36),
    location_code VARCHAR(20),
    subsystem_source VARCHAR(100) NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    service_category ENUM('Application', 'Approval', 'Payment', 'Connection', 'Maintenance', 'Complaint') NOT NULL,
    transaction_amount DECIMAL(12,2) DEFAULT 0,
    status VARCHAR(50),
    transaction_date DATE NOT NULL,
    processing_time_hours DECIMAL(8,2),
    source_system VARCHAR(100) NOT NULL,
    source_record_id VARCHAR(50) NOT NULL,
    sync_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES dim_citizens(citizen_id),
    FOREIGN KEY (project_id) REFERENCES dim_projects(project_id),
    FOREIGN KEY (location_code) REFERENCES dim_locations(location_code),
    INDEX idx_citizen (citizen_id),
    INDEX idx_project (project_id),
    INDEX idx_subsystem (subsystem_source),
    INDEX idx_service_type (service_type),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_source (source_system, source_record_id)
);

-- Financial Transactions Fact Table
CREATE TABLE fact_financial_transactions (
    fact_id VARCHAR(36) PRIMARY KEY,
    citizen_id VARCHAR(36),
    project_id VARCHAR(36),
    location_code VARCHAR(20),
    subsystem_source VARCHAR(100) NOT NULL,
    transaction_type ENUM('Payment', 'Billing', 'Loan', 'Fee', 'Fine', 'Refund') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'PHP',
    payment_method VARCHAR(50),
    transaction_date DATE NOT NULL,
    due_date DATE,
    status ENUM('Pending', 'Paid', 'Overdue', 'Cancelled') NOT NULL,
    source_system VARCHAR(100) NOT NULL,
    source_record_id VARCHAR(50) NOT NULL,
    sync_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES dim_citizens(citizen_id),
    FOREIGN KEY (project_id) REFERENCES dim_projects(project_id),
    FOREIGN KEY (location_code) REFERENCES dim_locations(location_code),
    INDEX idx_citizen (citizen_id),
    INDEX idx_project (project_id),
    INDEX idx_subsystem (subsystem_source),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_amount (amount),
    INDEX idx_status (status)
);

-- LGU1 Subsystem transaction logs
CREATE TABLE subsystem_transactions (
    transaction_id VARCHAR(36) PRIMARY KEY,
    subsystem_name ENUM(
        'infrastructure_project_management',
        'utility_billing_monitoring',
        'road_transportation_infrastructure',
        'public_facilities_reservation',
        'community_infrastructure_maintenance',
        'urban_planning_development',
        'land_registration_titling',
        'housing_resettlement_management',
        'renewable_energy_project',
        'energy_efficiency_conservation'
    ) NOT NULL,
    module_name VARCHAR(100),
    operation_type ENUM('CREATE', 'UPDATE', 'DELETE', 'SYNC', 'APPROVE', 'REJECT') NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id VARCHAR(50) NOT NULL,
    user_id INT,
    user_role VARCHAR(50),
    department VARCHAR(100),
    request_data JSON,
    response_data JSON,
    status ENUM('SUCCESS', 'FAILED', 'PENDING') DEFAULT 'PENDING',
    error_message TEXT,
    processing_time_ms INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_subsystem (subsystem_name),
    INDEX idx_module (module_name),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_department (department)
);

-- LGU1 Subsystem health monitoring
CREATE TABLE subsystem_health (
    subsystem_name VARCHAR(100) PRIMARY KEY,
    status ENUM('HEALTHY', 'UNHEALTHY', 'DEGRADED', 'MAINTENANCE') DEFAULT 'HEALTHY',
    last_check TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response_time_ms INT,
    error_count INT DEFAULT 0,
    success_count INT DEFAULT 0,
    load_factor DECIMAL(3,2) DEFAULT 0.00,
    active_users INT DEFAULT 0,
    daily_transactions INT DEFAULT 0,
    INDEX idx_status (status),
    INDEX idx_last_check (last_check)
);

-- API request logs for load balancing
CREATE TABLE api_requests (
    request_id VARCHAR(36) PRIMARY KEY,
    service_endpoint VARCHAR(100) NOT NULL,
    method ENUM('GET', 'POST', 'PUT', 'DELETE') NOT NULL,
    client_ip VARCHAR(45),
    user_agent TEXT,
    request_size INT,
    response_size INT,
    response_time_ms INT,
    status_code INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_endpoint (service_endpoint),
    INDEX idx_created (created_at),
    INDEX idx_status (status_code)
);

-- Cross-subsystem integration logs
CREATE TABLE integration_logs (
    integration_id VARCHAR(36) PRIMARY KEY,
    source_subsystem VARCHAR(100) NOT NULL,
    target_subsystem VARCHAR(100) NOT NULL,
    integration_type ENUM('DATA_SYNC', 'WORKFLOW', 'NOTIFICATION', 'APPROVAL') NOT NULL,
    payload JSON,
    status ENUM('SUCCESS', 'FAILED', 'PENDING') DEFAULT 'PENDING',
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_source (source_subsystem),
    INDEX idx_target (target_subsystem),
    INDEX idx_type (integration_type),
    INDEX idx_status (status)
);

-- ETL Sync Metadata
CREATE TABLE etl_sync_log (
    sync_id VARCHAR(36) PRIMARY KEY,
    source_system VARCHAR(100) NOT NULL,
    target_table VARCHAR(100) NOT NULL,
    sync_type ENUM('FULL', 'INCREMENTAL') NOT NULL,
    records_processed INT DEFAULT 0,
    records_success INT DEFAULT 0,
    records_failed INT DEFAULT 0,
    sync_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sync_end TIMESTAMP NULL,
    status ENUM('RUNNING', 'SUCCESS', 'FAILED') DEFAULT 'RUNNING',
    error_message TEXT,
    INDEX idx_source (source_system),
    INDEX idx_status (status),
    INDEX idx_sync_start (sync_start)
);

-- Insert initial subsystem health records
INSERT INTO subsystem_health (subsystem_name, status) VALUES
('infrastructure_project_management', 'HEALTHY'),
('utility_billing_monitoring', 'HEALTHY'),
('road_transportation_infrastructure', 'HEALTHY'),
('public_facilities_reservation', 'HEALTHY'),
('community_infrastructure_maintenance', 'HEALTHY'),
('urban_planning_development', 'HEALTHY'),
('land_registration_titling', 'HEALTHY'),
('housing_resettlement_management', 'HEALTHY'),
('renewable_energy_project', 'HEALTHY'),
('energy_efficiency_conservation', 'HEALTHY');

-- Sample location data
INSERT INTO dim_locations (location_code, barangay_code, barangay_name, zone_code, zone_name, district) VALUES
('QC-001-01', 'QC-001', 'Barangay Commonwealth', '01', 'Zone 1', 'District 1'),
('QC-002-01', 'QC-002', 'Barangay Batasan Hills', '01', 'Zone 1', 'District 2'),
('QC-003-01', 'QC-003', 'Barangay Fairview', '01', 'Zone 1', 'District 3');