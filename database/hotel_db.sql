CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

-- Tabel kamar
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100),
    photo VARCHAR(255),
    bed_size VARCHAR(50),
    facilities TEXT,
    price_per_night INT,
    max_guests INT
);

-- Tabel reservasi
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    total_price INT,
    payment_method VARCHAR(50),
    status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);
