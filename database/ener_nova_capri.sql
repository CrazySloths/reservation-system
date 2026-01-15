-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 08, 2026 at 11:06 AM
-- Server version: 10.11.14-MariaDB-ubu2204
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ener_nova_capri`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `area` varchar(50) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_assignments`
--

CREATE TABLE `audit_assignments` (
  `assignment_id` int(11) NOT NULL,
  `resident_user_id` int(11) NOT NULL,
  `staff_user_id` int(11) NOT NULL,
  `assigned_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `scheduled_date` date NOT NULL,
  `status` enum('Pending','Ongoing','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `reason` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_feedback`
--

CREATE TABLE `audit_feedback` (
  `feedback_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `resident_user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comments` text DEFAULT NULL,
  `feedback_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_findings`
--

CREATE TABLE `audit_findings` (
  `finding_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `finding_details` text DEFAULT NULL,
  `recommendations` text NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `completed_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangay_energy_audits`
--

CREATE TABLE `barangay_energy_audits` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `submission_date` datetime NOT NULL,
  `days_at_home_monthly` tinyint(3) UNSIGNED NOT NULL DEFAULT 30 COMMENT 'Number of days the resident is home per month, used for estimated consumption calculation.',
  `appliances_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON array of selected appliances, their default usage/wattage, and calculated monthly kWh.' CHECK (json_valid(`appliances_data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `correction_requests`
--

CREATE TABLE `correction_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reading_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `requested_kwh` decimal(10,2) DEFAULT NULL,
  `requested_bill_amount` decimal(10,2) DEFAULT NULL,
  `requested_reading_date` date DEFAULT NULL,
  `new_bill_file_path_1` varchar(255) DEFAULT NULL,
  `new_bill_file_path_2` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `high_consumption_alerts`
--

CREATE TABLE `high_consumption_alerts` (
  `alert_id` int(11) NOT NULL,
  `reading_id` int(11) NOT NULL COMMENT 'References the specific reading that triggered the alert',
  `user_id` int(11) NOT NULL COMMENT 'User who had the high consumption',
  `assigned_staff_id` int(11) DEFAULT NULL COMMENT 'ID of the staff user assigned for follow-up',
  `alert_reason` varchar(255) NOT NULL,
  `alert_status` enum('pending','assigned','resolved') NOT NULL DEFAULT 'pending',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `source` varchar(255) DEFAULT 'Admin Created',
  `link_url` varchar(255) DEFAULT NULL COMMENT 'Optional link to the original source/article',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `title`, `content`, `source`, `link_url`, `created_at`) VALUES
(3, 'Energy Efficiency vs. Energy Conservation', 'Energy Efficiency vs. Energy Conservation \r\n\r\nEnergy Efficiency: Using technology or methods that perform the same service but consume less energy (e.g., using LED or CFL bulbs instead of incandescent). \r\nEnergy Conservation: Changing behavior to reduce energy use (e.g., turning off lights when not needed). \r\nThey note people sometimes confuse the two. For example, taking the stairs instead of the elevator is conservation, not efficiency. \r\n\r\nThe Rebound Effect \r\nEven when energy efficiency improves, total energy usage might not drop proportionally because people may compensate by using more (e.g., buying bigger houses or more appliances). This offsetting is called the rebound effect. \r\n\r\nEnergy Saving Tips \r\nSome of the suggested practices to save energy: \r\nTurn on lights only when needed. \r\nUse natural daylight during daytime, with proper window positioning. \r\nUnplug electronics / appliances when not in use. \r\nKeep appliances and the home clean to ensure they operate efficiently. ', 'Energy Literacy Ph', 'https://www.energyliteracyph.com/learning-materials', '2025-10-18 04:39:53'),
(4, 'Renewable Energy in the Philippines', 'Renewable Energy in the Philippines \r\n\r\n1. Key Concepts \r\nRenewable Energy (RE): Energy from sources that are naturally restored, such as solar, hydro, wind, geothermal, biomass, and ocean energy. \r\nBIG SHOW: An acronym summarizing the Philippines’ main renewable energy sources — Biomass, Geothermal, Solar, Hydro, Ocean, and Wind. \r\nNet Metering: A system that allows consumers who generate their own electricity (e.g., through solar panels) to sell excess power back to the grid. \r\nGreen Energy Option Program (GEOP): Enables consumers to choose renewable energy sources for their electricity supply. \r\n\r\n2. Main Lesson Content \r\nThe Need for Renewable Energy \r\nThe country faces high energy costs and growing demand for electricity. Renewable energy helps reduce reliance on imported fossil fuels and lowers greenhouse gas emissions. Clean energy promotes energy security and supports the fight against climate change. \r\n\r\nRenewable Energy Sources in the Philippines \r\nSolar Energy – Converts sunlight into electricity using solar panels. Wind Energy – Uses turbines to convert wind movement into electricity. Geothermal Energy – Harnesses heat from beneath the earth’s surface to generate electricity. Biomass Energy – Uses organic waste (e.g., rice husks, coconut shells) as fuel for producing energy. Ocean Energy – Captures power from ocean currents and tides, though still under development. \r\n\r\n3. Advantages of Going Renewable \r\nEnvironmental Benefits: Reduces pollution and greenhouse gases. \r\nEconomic Benefits: Lowers electricity bills and encourages local job creation in the energy sector. \r\nEnergy Independence and Long-Term Savings. \r\n\r\n4. Supporting Programs and Policies \r\nThe government supports renewable energy through programs like Net Metering and the Green Energy Option Program, making it easier for individuals and companies to adopt clean energy systems. These programs encourage participation from both households and businesses in the country’s renewable energy transition. ', 'Energy Literacy Ph', 'https://www.energyliteracyph.com/learning-materials', '2025-10-18 04:39:53');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date_and_venue` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `official_source_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `date_and_venue`, `description`, `official_source_url`, `image_url`) VALUES
(1, 'Off-grid Renewable Energy Solutions: Driver of Agriculture Value Chain Development', '17 July 2019 – Taguig City', 'The seminar titled “Off-grid Renewable Energy Solutions: Driver of Agriculture Value Chain Development” was jointly organized by the Global Green Growth Institute (GGGI) and the Embassy of the Republic of Korea. The event brought together policymakers, investors, and energy sector experts to discuss the investment and policy challenges in delivering off-grid renewable energy solutions to rural communities, particularly those where 7 to 12 million Filipinos still lack access to reliable electricity. Topics focused on how renewable energy can strengthen the agricultural value chain, enhance productivity, create employment opportunities, and reduce poverty in off-grid areas. Speakers also highlighted the growing cost-effectiveness of renewable energy technologies such as solar and biomass in supporting inclusive growth and sustainable rural development. The session aimed to align public and private stakeholders toward accelerating investment in decentralized energy systems to drive both agricultural and economic transformation in the Philippines.', 'https://gggi.org/accelerating-off-grid-renewable-energy-investment-opportunities-in-the-philippines-gggi-and-embassy-of-the-republic-of-korea-co-organized-a-multi-stakeholder-seminar', 'uploads/news/news_68ed420137fc75.78750336.png'),
(2, 'Philippines Energy and Infrastructure Development Seminar', '21 February 2014 – Manila', 'The Philippines Energy and Infrastructure Development Seminar, held on February 21, 2014, in Manila, was organized by the Economic Research Institute for ASEAN and East Asia (ERIA). The seminar gathered high-level representatives from the Philippine and Japanese governments, private sector executives, and international organizations to exchange views on ASEAN power grid connectivity, infrastructure investment, and regional energy security. Discussions revolved around the need to strengthen bilateral cooperation in energy development, improve the efficiency of infrastructure projects, and promote sustainable growth in the energy sector. The event emphasized the strategic importance of enhancing regional integration in the ASEAN energy market and advancing shared initiatives toward a secure, sustainable, and inclusive energy future.', 'https://www.eria.org/news-and-views/philippines-energy-and-infrastructure-development-seminar/', 'uploads/news/news_68ed421979a1b7.23647563.png'),
(3, 'The 3rd Philippine Renewable Energy Conference 2025', 'August 7–8, 2025 – City of Dreams Manila', 'The 3rd Philippine Renewable Energy Conference 2025, hosted by e-vents.ph and powered by First Gen, brought together key stakeholders from the renewable energy sector to discuss the country’s clean energy transition. Supported by major sponsors such as San Miguel Global Power, SGV, Green Tiger Markets, Meralco MPower, and Berde Renewables, the event focused on the theme “Balancing Power Supply and Sustainability with Climate Resiliency and Energy Security.” The conference served as a platform to present the Philippines’ National Renewable Energy Plan (2025–2050), which aims for a 35% renewable energy share by 2030 and 50% by 2040. Expert panels tackled issues on offshore and onshore wind power, solar energy technologies, the role of Retail Electricity Suppliers (RES), and policies for strengthening the country’s energy resilience. The event underscored the importance of public-private collaboration in achieving the nation’s clean energy goals.', 'https://e-vents.ph/the-3rd-philippine-renewable-energy-conference-2025/?fbclid=IwY2xjawMr-xhleHRuA2FlbQIxMQABHlDgy0-WOFk51rkhJG6OWP2RPv8o_fXTuBYGMLXdca_iS6_n1JQboze1ZmEe_aem_Fc7AG7ImHEO2mEKrQ5yNow', 'uploads/news/news_68ed42387da759.29236355.png');

-- --------------------------------------------------------

--
-- Table structure for table `seminars`
--

CREATE TABLE `seminars` (
  `seminar_id` int(11) NOT NULL,
  `seminar_title` varchar(255) NOT NULL,
  `seminar_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `target_area` varchar(255) NOT NULL,
  `attachments_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `seminar_image_url` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seminars`
--

INSERT INTO `seminars` (`seminar_id`, `seminar_title`, `seminar_date`, `start_time`, `end_time`, `description`, `location`, `target_area`, `attachments_path`, `created_at`, `seminar_image_url`, `is_archived`) VALUES
(28, 'Energy Conservation Awareness ', '2025-10-23', '20:00:00', '22:00:00', 'A seminar focused on teaching residents how to reduce electricity consumption and use energy-efficient appliances.', 'Multi-Purpose Hall', '0', 'uploads/seminar_attachments/1761174050-68f96222a16d0.pdf', '2025-10-22 23:00:50', 'assets/seminar_img/10.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `seminar_joins`
--

CREATE TABLE `seminar_joins` (
  `join_id` int(11) NOT NULL,
  `seminar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seminar_videos`
--

CREATE TABLE `seminar_videos` (
  `video_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_type` enum('youtube','upload') NOT NULL,
  `video_url` varchar(512) NOT NULL,
  `thumbnail_url` varchar(512) DEFAULT NULL,
  `upload_date` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seminar_videos`
--

INSERT INTO `seminar_videos` (`video_id`, `title`, `description`, `video_type`, `video_url`, `thumbnail_url`, `upload_date`, `updated_at`, `is_archived`, `admin_id`) VALUES
(26, 'Energy Efficiency 101', 'This is for educational purposes only', 'youtube', 'D11iFUw_ImU', 'https://img.youtube.com/vi/D11iFUw_ImU/mqdefault.jpg', '2025-10-22 22:55:29', NULL, 0, 82),
(27, 'Lecture 8: Buildings and Energy Efficiency', 'Copy by MIT OpenCourseWare', 'upload', 'uploads/videos/1761173872-68f9617048d94.mp4', 'uploads/thumbnails/1761173872-thumb-68f9617048d97.jpg', '2025-10-22 22:57:52', NULL, 0, 82);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `sex` varchar(50) NOT NULL,
  `civil_status` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture_attachment` varchar(255) DEFAULT NULL,
  `cellphone_number` bigint(11) NOT NULL,
  `house_number` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `area` varchar(255) NOT NULL,
  `residency_status` enum('Owned','Rented') NOT NULL DEFAULT 'Owned',
  `proof_of_residency_type_name` varchar(150) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `meralco_bill_attachment` varchar(255) DEFAULT NULL,
  `rented_proof_attachment` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_role` varchar(50) NOT NULL DEFAULT 'resident',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `password_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `bar_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_name`, `last_name`, `suffix`, `birthdate`, `sex`, `civil_status`, `email`, `profile_picture_attachment`, `cellphone_number`, `house_number`, `street`, `area`, `residency_status`, `proof_of_residency_type_name`, `religion`, `meralco_bill_attachment`, `rented_proof_attachment`, `password`, `user_role`, `created_at`, `status`, `password_token`, `token_expiry`, `bar_code`) VALUES
(82, 'Christian', 'Arnaldo', 'Cando', NULL, '2003-09-21', 'Female', 'Single', 'piyasigno@gmail.com', 'admin_profile_82_68f972a1c13c8.jpg', 9085919898, 'Block 21', 'Lily', 'AREA 4', 'Owned', '', 'BORN AGAIN CHRISTIAN', '68efb548aabe8.jpg', NULL, '$2y$10$.M4HvdDqfCJpkbZnwtggxOw1cz5MrK6bPj.wIz0/BVxOddhlBm/TK', 'admin', '2025-09-02 17:57:11', 'approved', '27e1c18afafb348a38accd8355a74e7dd82d68b18c223ee1fe135a04c64f7b2c', '2025-09-09 17:25:59', 'BC25ADMIN'),
(88, 'Jhales', 'Arizo', 'Santiago', NULL, '2002-12-16', 'Male', 'Single', 'jeylzuayanokoji@gmail.com', 'resident_profile_88_68f3ce632d473.png', 9303207238, 'Block 20', 'Daisy', 'AREA 1', 'Owned', '', 'BORN AGAIN CHRISTIAN', '68efb548aabe8.jpg', NULL, '$2y$10$e90goS3FEjIfeX2IRaWZMeG8kRlR5.w.WwI122cVk6kaie2B5Co1G', 'resident', '2025-09-05 14:05:02', 'approved', 'c98d32483e26e8d36f65b6b7af6f0107a51d0c79c698dab9cfb095f29f927001', '2025-10-24 06:07:03', 'BC25TRYA'),
(2011, 'Edelyn', 'Bautista', 'Perez', NULL, '2006-01-04', 'Female', 'Single', 'sofiyaloreynnn21@gmail.com', 'staff_profile_2011_68f9667f20190.jpg', 9321312111, 'Block 31', 'Camia', 'AREA 3', 'Owned', '', 'NO RELIGION', '68f8e00e48746.jpg', NULL, '$2y$10$UZEB5Vxt84J52.2oN3MtM.2ty0EzCkKrzOofxS6RI2jg3Aoj7Zm6C', 'staff', '2025-10-22 13:45:50', 'approved', NULL, NULL, 'BC25STAFF');

-- --------------------------------------------------------

--
-- Table structure for table `user_electricity_readings`
--

CREATE TABLE `user_electricity_readings` (
  `reading_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meter_reading` decimal(10,2) NOT NULL,
  `bill_amount` decimal(10,2) NOT NULL,
  `reading_date` date NOT NULL,
  `bill_file_path_1` varchar(255) DEFAULT NULL,
  `bill_file_path_2` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `correction_status` enum('original','corrected') NOT NULL DEFAULT 'original'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_views`
--

CREATE TABLE `video_views` (
  `view_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `view_timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_views`
--

INSERT INTO `video_views` (`view_id`, `video_id`, `user_id`, `view_timestamp`) VALUES
(27, 26, 88, '2025-10-23 17:16:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `audit_assignments`
--
ALTER TABLE `audit_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `resident_user_id` (`resident_user_id`),
  ADD KEY `staff_user_id` (`staff_user_id`);

--
-- Indexes for table `audit_feedback`
--
ALTER TABLE `audit_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `resident_user_id` (`resident_user_id`);

--
-- Indexes for table `audit_findings`
--
ALTER TABLE `audit_findings`
  ADD PRIMARY KEY (`finding_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `correction_requests`
--
ALTER TABLE `correction_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reading_id` (`reading_id`);

--
-- Indexes for table `high_consumption_alerts`
--
ALTER TABLE `high_consumption_alerts`
  ADD PRIMARY KEY (`alert_id`),
  ADD UNIQUE KEY `unique_reading_alert` (`reading_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_staff_id` (`assigned_staff_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seminars`
--
ALTER TABLE `seminars`
  ADD PRIMARY KEY (`seminar_id`);

--
-- Indexes for table `seminar_joins`
--
ALTER TABLE `seminar_joins`
  ADD PRIMARY KEY (`join_id`),
  ADD UNIQUE KEY `seminar_user_unique` (`seminar_id`,`user_id`),
  ADD KEY `seminar_id` (`seminar_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `seminar_videos`
--
ALTER TABLE `seminar_videos`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `bar_code` (`bar_code`);

--
-- Indexes for table `user_electricity_readings`
--
ALTER TABLE `user_electricity_readings`
  ADD PRIMARY KEY (`reading_id`),
  ADD KEY `fk_user_id_cascade` (`user_id`);

--
-- Indexes for table `video_views`
--
ALTER TABLE `video_views`
  ADD PRIMARY KEY (`view_id`),
  ADD UNIQUE KEY `unique_view` (`video_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `audit_assignments`
--
ALTER TABLE `audit_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `audit_feedback`
--
ALTER TABLE `audit_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `audit_findings`
--
ALTER TABLE `audit_findings`
  MODIFY `finding_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `correction_requests`
--
ALTER TABLE `correction_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `high_consumption_alerts`
--
ALTER TABLE `high_consumption_alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `seminars`
--
ALTER TABLE `seminars`
  MODIFY `seminar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `seminar_joins`
--
ALTER TABLE `seminar_joins`
  MODIFY `join_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seminar_videos`
--
ALTER TABLE `seminar_videos`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3014;

--
-- AUTO_INCREMENT for table `user_electricity_readings`
--
ALTER TABLE `user_electricity_readings`
  MODIFY `reading_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10037;

--
-- AUTO_INCREMENT for table `video_views`
--
ALTER TABLE `video_views`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `audit_assignments`
--
ALTER TABLE `audit_assignments`
  ADD CONSTRAINT `audit_assignments_ibfk_1` FOREIGN KEY (`resident_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `audit_assignments_ibfk_2` FOREIGN KEY (`staff_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_feedback`
--
ALTER TABLE `audit_feedback`
  ADD CONSTRAINT `audit_feedback_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `audit_assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `audit_feedback_ibfk_2` FOREIGN KEY (`resident_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_findings`
--
ALTER TABLE `audit_findings`
  ADD CONSTRAINT `audit_findings_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `audit_assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- Constraints for table `correction_requests`
--
ALTER TABLE `correction_requests`
  ADD CONSTRAINT `correction_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `correction_requests_ibfk_2` FOREIGN KEY (`reading_id`) REFERENCES `user_electricity_readings` (`reading_id`);

--
-- Constraints for table `high_consumption_alerts`
--
ALTER TABLE `high_consumption_alerts`
  ADD CONSTRAINT `high_consumption_alerts_ibfk_1` FOREIGN KEY (`reading_id`) REFERENCES `user_electricity_readings` (`reading_id`),
  ADD CONSTRAINT `high_consumption_alerts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `high_consumption_alerts_ibfk_3` FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `seminar_videos`
--
ALTER TABLE `seminar_videos`
  ADD CONSTRAINT `seminar_videos_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_electricity_readings`
--
ALTER TABLE `user_electricity_readings`
  ADD CONSTRAINT `fk_user_id_cascade` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `video_views`
--
ALTER TABLE `video_views`
  ADD CONSTRAINT `video_views_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `seminar_videos` (`video_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_views_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
