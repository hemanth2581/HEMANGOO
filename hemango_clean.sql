-- Clean HEMANGOO Database Schema
-- Drop existing database and recreate with proper structure

DROP DATABASE IF EXISTS `hemango`;
CREATE DATABASE `hemango` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hemango`;

-- Users table (single table for all users)
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

-- Bookings table
CREATE TABLE `bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `company_name` varchar(255) NOT NULL,
    `booking_time` datetime NOT NULL,
    `quantity_kg` int(11) NOT NULL,
    `status` enum('Pending','Confirmed','Cancelled','Rejected') DEFAULT 'Pending',
    `mango_variety` varchar(100) DEFAULT NULL,
    `harvest_date` date DEFAULT NULL,
    `ripeness_level` enum('unripe','partially_ripe','fully_ripe') DEFAULT NULL,
    `colour` enum('greenish','yellow','golden','mixed') DEFAULT NULL,
    `size` enum('small','medium','large') DEFAULT NULL,
    `bruising_level` enum('none','light','moderate','heavy') DEFAULT NULL,
    `pest_presence` enum('yes','no') DEFAULT 'no',
    `comments` text DEFAULT NULL,
    `photo_1` varchar(500) DEFAULT NULL,
    `photo_2` varchar(500) DEFAULT NULL,
    `photo_3` varchar(500) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    KEY `idx_user_id` (`user_id`),
    KEY `idx_status` (`status`),
    KEY `idx_booking_time` (`booking_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Market prices table
CREATE TABLE `market_prices` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `price` decimal(10,2) NOT NULL,
    `date` date NOT NULL DEFAULT (CURDATE()),
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Market activity table
CREATE TABLE `market_activity` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` date NOT NULL DEFAULT (CURDATE()),
    `average_buyers` int(11) DEFAULT 0,
    `active_buyers` int(11) DEFAULT 0,
    `trend` varchar(50) DEFAULT 'Stable',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Slots table
CREATE TABLE `slots` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `warehouse_name` varchar(255) NOT NULL,
    `warehouse_email` varchar(255) NOT NULL,
    `warehouse_mobile` varchar(15) NOT NULL,
    `slot_timing` time NOT NULL,
    `slot_price` decimal(10,2) NOT NULL,
    `is_available` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_timing` (`slot_timing`),
    KEY `idx_available` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data
INSERT INTO `users` (`full_name`, `email`, `mobile`, `password`, `role`) VALUES
('Admin User', 'admin@hemango.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin'),
('Test Farmer', 'farmer@hemango.com', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Farmer');

INSERT INTO `market_prices` (`price`, `date`) VALUES
(150.00, CURDATE()),
(155.00, DATE_ADD(CURDATE(), INTERVAL 1 DAY)),
(148.00, DATE_ADD(CURDATE(), INTERVAL 2 DAY));

INSERT INTO `market_activity` (`date`, `average_buyers`, `active_buyers`, `trend`) VALUES
(CURDATE(), 25, 18, 'Stable'),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), 30, 22, 'Rising'),
(DATE_ADD(CURDATE(), INTERVAL 2 DAY), 20, 15, 'Falling');

INSERT INTO `slots` (`warehouse_name`, `warehouse_email`, `warehouse_mobile`, `slot_timing`, `slot_price`) VALUES
('Fresh Farms Warehouse', 'warehouse1@hemango.com', '9876543201', '09:00:00', 150.00),
('Fresh Farms Warehouse', 'warehouse1@hemango.com', '9876543201', '10:00:00', 150.00),
('Fresh Farms Warehouse', 'warehouse1@hemango.com', '9876543201', '11:00:00', 150.00),
('Green Valley Storage', 'warehouse2@hemango.com', '9876543202', '09:30:00', 160.00),
('Green Valley Storage', 'warehouse2@hemango.com', '9876543202', '10:30:00', 160.00),
('Green Valley Storage', 'warehouse2@hemango.com', '9876543202', '11:30:00', 160.00);
