-- Create the database
CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

-- Create rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    bed_size VARCHAR(50) NOT NULL,
    facilities TEXT NOT NULL,
    price_per_night DECIMAL(10, 2) NOT NULL,
    max_guests INT NOT NULL,
    photo VARCHAR(255) NOT NULL,
    status ENUM('available', 'maintenance') DEFAULT 'available'
);

-- Create reservations table
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    guest_email VARCHAR(100) NOT NULL,
    guest_phone VARCHAR(20) NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    num_guests INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Create users table (for admin and user logins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample room data
INSERT INTO rooms (room_name, room_type, bed_size, facilities, price_per_night, max_guests, photo, status)
VALUES 
('Standard Room 101', 'Standard', 'Queen', 'TV, AC, Free Wi-Fi, Minibar', 300000, 2, 'standard.jpg', 'available'),
('Standard Room 102', 'Standard', 'Twin', 'TV, AC, Free Wi-Fi, Minibar', 300000, 2, 'standard.jpg', 'available'),
('Superior Room 201', 'Superior', 'King', 'TV, AC, Free Wi-Fi, Minibar, Bathtub', 450000, 2, 'superior.jpg', 'available'),
('Superior Room 202', 'Superior', 'Queen', 'TV, AC, Free Wi-Fi, Minibar, Bathtub', 450000, 2, 'superior.jpg', 'available'),
('Deluxe Room 301', 'Deluxe', 'King', 'TV, AC, Free Wi-Fi, Minibar, Bathtub, City View, Living Area', 600000, 4, 'deluxe.jpg', 'available'),
('Deluxe Room 302', 'Deluxe', 'King', 'TV, AC, Free Wi-Fi, Minibar, Bathtub, City View, Living Area', 600000, 4, 'deluxe.jpg', 'available');

-- Insert admin user (password is 'admin123' hashed)
INSERT INTO users (username, password, full_name, email, role)
VALUES ('admin', '$2y$10$8zT4.7CA.aYnQkQTH/r.Ou.THIlBvTvN2VQ0dVjL2CJ/5MbFE1m6O', 'Admin User', 'admin@hotelhebat.com', 'admin');