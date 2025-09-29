-- Updated HEMANGOO Database Schema
-- This schema matches the app's data structure requirements

DROP DATABASE IF EXISTS `hemango`;
CREATE DATABASE `hemango` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hemango`;

-- Users table
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `full_name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL UNIQUE,
    `mobile` varchar(15) NOT NULL,
    `password` varchar(255) NOT NULL,
    `role` enum('Farmer','Admin') NOT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Factories table
CREATE TABLE `factories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `location` varchar(255) NOT NULL,
    `address` text NOT NULL,
    `phone` varchar(15) NOT NULL,
    `email` varchar(255) NOT NULL,
    `capacity` int(11) NOT NULL DEFAULT 1000,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_location` (`location`),
    KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Mango varieties table
CREATE TABLE `mango_varieties` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `type` varchar(50) NOT NULL,
    `season_start` varchar(20) NOT NULL,
    `season_end` varchar(20) NOT NULL,
    `description` text,
    `image_url` varchar(500),
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`),
    KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Time slots table for factories
CREATE TABLE `factory_time_slots` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `factory_id` int(11) NOT NULL,
    `slot_date` date NOT NULL,
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `max_capacity_kg` int(11) NOT NULL DEFAULT 1000,
    `current_bookings_kg` int(11) DEFAULT 0,
    `price_per_kg` decimal(10,2) DEFAULT 0.00,
    `is_available` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`factory_id`) REFERENCES `factories`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_slot` (`factory_id`, `slot_date`, `start_time`),
    KEY `idx_factory_date` (`factory_id`, `slot_date`),
    KEY `idx_available` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Quality reports table
CREATE TABLE `quality_reports` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `mango_type` varchar(50) NOT NULL,
    `mango_variety` varchar(50) NOT NULL,
    `estimated_quantity` decimal(8,2) NOT NULL,
    `unit` varchar(10) DEFAULT 'kg',
    `harvest_date` date NOT NULL,
    `ripeness_level` enum('Unripe', 'Partially Ripe', 'Fully Ripe') NOT NULL,
    `colour` enum('Greenish', 'Yellow', 'Golden', 'Mixed') NOT NULL,
    `size` enum('Small', 'Medium', 'Large') NOT NULL,
    `bruising_level` enum('None', 'Light', 'Moderate', 'Heavy') NOT NULL,
    `pest_presence` tinyint(1) NOT NULL DEFAULT 0,
    `additional_notes` text,
    `images` json,
    `admin_review_status` enum('pending', 'approved', 'rejected') DEFAULT 'pending',
    `admin_notes` text,
    `reviewed_by` int(11),
    `reviewed_at` timestamp NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    KEY `idx_user` (`user_id`),
    KEY `idx_review_status` (`admin_review_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bookings table
CREATE TABLE `bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `factory_id` int(11) NOT NULL,
    `time_slot_id` int(11) NOT NULL,
    `quality_report_id` int(11),
    
    -- Mango details
    `mango_type` varchar(50) NOT NULL,
    `mango_variety` varchar(50) NOT NULL,
    `quantity` decimal(8,2) NOT NULL,
    `unit` varchar(10) DEFAULT 'kg',
    
    -- Booking details
    `booking_date` date NOT NULL,
    `slot_time` varchar(50) NOT NULL,
    `status` enum('pending', 'confirmed', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    
    -- Admin review
    `reviewed_by` int(11),
    `reviewed_at` timestamp NULL,
    `admin_notes` text,
    `rejection_reason` text,
    
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`factory_id`) REFERENCES `factories`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`time_slot_id`) REFERENCES `factory_time_slots`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`quality_report_id`) REFERENCES `quality_reports`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    KEY `idx_user` (`user_id`),
    KEY `idx_factory` (`factory_id`),
    KEY `idx_date` (`booking_date`),
    KEY `idx_status` (`status`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Market prices table
CREATE TABLE `market_prices` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `mango_type` varchar(50) NOT NULL,
    `mango_variety` varchar(50),
    `price_per_kg` decimal(8,2) NOT NULL,
    `market_location` varchar(100),
    `price_date` date NOT NULL,
    `source` varchar(50) DEFAULT 'wholesale',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_type_variety` (`mango_type`, `mango_variety`),
    KEY `idx_date` (`price_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Market activity table
CREATE TABLE `market_activity` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` date NOT NULL DEFAULT (CURDATE()),
    `average_buyers` int(11) DEFAULT 0,
    `active_buyers` int(11) DEFAULT 0,
    `trend` varchar(50) DEFAULT 'Stable',
    `total_volume_kg` decimal(10,2) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data
INSERT INTO `users` (`full_name`, `email`, `mobile`, `password`, `role`) VALUES
('Admin User', 'admin@hemango.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin'),
('Test Farmer', 'farmer@hemango.com', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Farmer');

INSERT INTO `factories` (`name`, `location`, `address`, `phone`, `email`, `capacity`) VALUES
('Mango Processing Plant A', 'Mumbai, Maharashtra', '123 Industrial Area, Mumbai', '9876543210', 'plantA@hemango.com', 1000),
('Mango Processing Plant B', 'Pune, Maharashtra', '456 Agricultural Zone, Pune', '9876543211', 'plantB@hemango.com', 800),
('Mango Processing Plant C', 'Nashik, Maharashtra', '789 Farm District, Nashik', '9876543212', 'plantC@hemango.com', 1200);

INSERT INTO `mango_varieties` (`name`, `type`, `season_start`, `season_end`, `description`) VALUES
('Alphonso', 'Mango', 'March', 'June', 'Premium quality mango with sweet taste'),
('Kesar', 'Mango', 'April', 'July', 'Saffron colored mango with rich flavor'),
('Banganapalli', 'Mango', 'May', 'August', 'Large sized mango with mild sweetness'),
('Organic Mango', 'Organic Mango', 'March', 'July', 'Certified organic mango variety'),
('Premium Mango', 'Premium Mango', 'April', 'June', 'High-grade premium mango selection');

-- Generate time slots for the next 7 days for each factory
INSERT INTO `factory_time_slots` (`factory_id`, `slot_date`, `start_time`, `end_time`, `max_capacity_kg`, `price_per_kg`)
SELECT 
    f.id as factory_id,
    DATE_ADD(CURDATE(), INTERVAL day_offset DAY) as slot_date,
    slot_times.start_time,
    slot_times.end_time,
    1000 as max_capacity_kg,
    150.00 as price_per_kg
FROM factories f
CROSS JOIN (
    SELECT 0 as day_offset UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
) days
CROSS JOIN (
    SELECT '09:00:00' as start_time, '10:00:00' as end_time UNION
    SELECT '10:00:00', '11:00:00' UNION
    SELECT '11:00:00', '12:00:00' UNION
    SELECT '14:00:00', '15:00:00' UNION
    SELECT '15:00:00', '16:00:00' UNION
    SELECT '16:00:00', '17:00:00'
) slot_times;

INSERT INTO `market_prices` (`mango_type`, `mango_variety`, `price_per_kg`, `price_date`) VALUES
('Mango', 'Alphonso', 150.00, CURDATE()),
('Mango', 'Kesar', 140.00, CURDATE()),
('Mango', 'Banganapalli', 130.00, CURDATE()),
('Organic Mango', 'Alphonso', 180.00, CURDATE()),
('Premium Mango', 'Alphonso', 200.00, CURDATE());

INSERT INTO `market_activity` (`date`, `average_buyers`, `active_buyers`, `trend`, `total_volume_kg`) VALUES
(CURDATE(), 25, 18, 'Stable', 5000.00),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), 30, 22, 'Rising', 6000.00),
(DATE_ADD(CURDATE(), INTERVAL 2 DAY), 20, 15, 'Falling', 4000.00);
