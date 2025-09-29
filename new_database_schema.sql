-- Hemango App - New Database Schema
-- Optimized for performance and security

CREATE DATABASE IF NOT EXISTS hemango_new;
USE hemango_new;

-- Users table with proper structure
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('farmer', 'admin') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);

-- Factories table
CREATE TABLE factories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(255),
    capacity_per_day INT DEFAULT 1000,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_location (latitude, longitude),
    INDEX idx_active (is_active)
);

-- Mango varieties table
CREATE TABLE mango_varieties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    season_start DATE,
    season_end DATE,
    base_price_per_kg DECIMAL(10, 2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    factory_id INT NOT NULL,
    mango_variety_id INT NOT NULL,
    quantity_kg INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    quality_data JSON,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (factory_id) REFERENCES factories(id),
    FOREIGN KEY (mango_variety_id) REFERENCES mango_varieties(id),
    
    INDEX idx_user (user_id),
    INDEX idx_factory (factory_id),
    INDEX idx_date (booking_date),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- Market prices table
CREATE TABLE market_prices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mango_variety_id INT NOT NULL,
    price_per_kg DECIMAL(10, 2) NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (mango_variety_id) REFERENCES mango_varieties(id),
    
    INDEX idx_variety_date (mango_variety_id, date),
    UNIQUE KEY unique_variety_date (mango_variety_id, date)
);

-- Time slots table
CREATE TABLE time_slots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    factory_id INT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    max_capacity_kg INT DEFAULT 100,
    price_per_kg DECIMAL(10, 2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (factory_id) REFERENCES factories(id),
    
    INDEX idx_factory (factory_id),
    INDEX idx_time (start_time, end_time)
);

-- User sessions table for JWT management
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user (user_id),
    INDEX idx_token (token_hash),
    INDEX idx_expires (expires_at)
);

-- Insert sample data
INSERT INTO mango_varieties (name, season_start, season_end, base_price_per_kg) VALUES
('Alphonso', '2024-03-01', '2024-06-30', 150.00),
('Kesar', '2024-04-01', '2024-07-31', 120.00),
('Dasheri', '2024-05-01', '2024-08-31', 100.00),
('Langra', '2024-06-01', '2024-09-30', 110.00),
('Himsagar', '2024-04-15', '2024-07-15', 130.00);

INSERT INTO factories (name, location, latitude, longitude, contact_phone, contact_email, capacity_per_day) VALUES
('Green Valley Processing', 'Ratnagiri, Maharashtra', 17.0000, 73.3000, '+91-9876543210', 'contact@greenvalley.com', 2000),
('Mango Paradise Industries', 'Devgad, Maharashtra', 16.4000, 73.4000, '+91-9876543211', 'info@mangoparadise.com', 1500),
('Royal Mango Works', 'Sindhudurg, Maharashtra', 16.2000, 73.7000, '+91-9876543212', 'royal@mangoworks.com', 1800);

INSERT INTO time_slots (factory_id, start_time, end_time, max_capacity_kg, price_per_kg) VALUES
(1, '09:00:00', '10:00:00', 200, 5.00),
(1, '10:00:00', '11:00:00', 200, 5.00),
(1, '11:00:00', '12:00:00', 200, 5.00),
(1, '14:00:00', '15:00:00', 200, 5.00),
(1, '15:00:00', '16:00:00', 200, 5.00),
(1, '16:00:00', '17:00:00', 200, 5.00),
(2, '09:00:00', '10:00:00', 150, 4.50),
(2, '10:00:00', '11:00:00', 150, 4.50),
(2, '11:00:00', '12:00:00', 150, 4.50),
(2, '14:00:00', '15:00:00', 150, 4.50),
(2, '15:00:00', '16:00:00', 150, 4.50),
(3, '09:00:00', '10:00:00', 180, 4.75),
(3, '10:00:00', '11:00:00', 180, 4.75),
(3, '11:00:00', '12:00:00', 180, 4.75),
(3, '14:00:00', '15:00:00', 180, 4.75),
(3, '15:00:00', '16:00:00', 180, 4.75);

-- Insert sample market prices
INSERT INTO market_prices (mango_variety_id, price_per_kg, date) VALUES
(1, 150.00, CURDATE()),
(2, 120.00, CURDATE()),
(3, 100.00, CURDATE()),
(4, 110.00, CURDATE()),
(5, 130.00, CURDATE());
