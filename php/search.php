<?php
// Include database connection
require_once 'database.php';

// Check if the required parameters are set
if (!isset($_GET['checkin']) || !isset($_GET['checkout']) || !isset($_GET['guests'])) {
    echo "<p>Error: Missing required parameters.</p>";
    exit;
}

// Get and sanitize input parameters
$checkin = $conn->real_escape_string($_GET['checkin']);
$checkout = $conn->real_escape_string($_GET['checkout']);
$guests = intval($_GET['guests']);

// Validate dates
$today = date('Y-m-d');
if ($checkin < $today) {
    echo "<p>Error: Check-in date cannot be in the past.</p>";
    exit;
}
if ($checkout <= $checkin) {
    echo "<p>Error: Check-out date must be after check-in date.</p>";
    exit;
}

// Query to find available rooms
$sql = "SELECT * FROM rooms 
        WHERE max_guests >= ? 
        AND id NOT IN (
            SELECT room_id FROM reservations 
            WHERE (? < checkout_date AND ? > checkin_date)
        )";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<p>Error preparing query: " . $conn->error . "</p>";
    exit;
}

$stmt->bind_param("iss", $guests, $checkout, $checkin);
$stmt->execute();
$result = $stmt->get_result();

// HTML header
echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Hasil Pencarian - Hotel Hebat</title>
    <link rel='stylesheet' href='../css/style.css'>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 100px auto 40px;
            padding: 20px;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .room {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
        }
        .room-image {
            flex: 0 0 250px;
            margin-right: 20px;
        }
        .room-image img {
            width: 100%;
            border-radius: 8px;
            height: auto;
        }
        .room-details {
            flex: 1;
            min-width: 300px;
        }
        .room h3 {
            margin-top: 0;
            color: #333;
        }
        .book-button {
            display: inline-block;
            background: #000;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
        }
        .book-button:hover {
            background: #333;
        }
        .no-rooms {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        hr {
            border: 0;
            height: 1px;
            background: #eee;
            margin: 20px 0;
        }
        .search-details {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .back-button {
            display: block;
            text-align: center;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <header>
        <div><strong>HOTEL HEBAT</strong></div>
        <nav>
            <a href='../index.html'>Home</a>
            <a href='../index.html#rooms'>Kamar</a>
            <a href='../index.html#facilities'>Facilities</a>
            <a href='../index.html#about'>About Us</a>
            <a href='#'>Login/Register</a>
        </nav>
    </header>
    
    <div class='container'>
        <div class='search-details'>
            <p><strong>Pencarian untuk:</strong> Check-in: " . date('d/m/Y', strtotime($checkin)) . 
            " - Check-out: " . date('d/m/Y', strtotime($checkout)) . 
            " - Jumlah Tamu: " . $guests . "</p>
        </div>
        
        <h2>Hasil Pencarian Kamar Tersedia</h2>";

// Display search results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='room'>
                <div class='room-image'>
                    <img src='../images/" . htmlspecialchars($row['photo']) . "' alt='Foto Kamar'>
                </div>
                <div class='room-details'>
                    <h3>" . htmlspecialchars($row['room_name']) . "</h3>
                    <p>Ukuran Tempat Tidur: " . htmlspecialchars($row['bed_size']) . "</p>
                    <p>Fasilitas: " . htmlspecialchars($row['facilities']) . "</p>
                    <p>Harga per Malam: Rp" . number_format($row['price_per_night']) . "</p>
                    <p>Kapasitas: " . $row['max_guests'] . " tamu</p>
                    <a href='../pesan.html?room_id=" . $row['id'] . "&checkin=" . $checkin . "&checkout=" . $checkout . "&guests=" . $guests . "' class='book-button'>Pesan Sekarang</a>
                </div>
              </div>";
    }
} else {
    echo "<div class='no-rooms'>
            <p>Tidak ada kamar tersedia untuk tanggal dan jumlah tamu yang dipilih.</p>
            <p>Silakan coba dengan tanggal atau jumlah tamu yang berbeda.</p>
          </div>";
}

// Add a back button
echo "<a href='../index.html' class='book-button back-button'>Kembali ke Beranda</a>
    </div> <!-- end container -->
    
    <footer>
        <div>
            <h4>ABOUT AGENCY</h4>
            <p>Hotel Hebat adalah pilihan terbaik untuk penginapan modern dan nyaman di Indonesia.</p>
        </div>
        <div>
            <h4>NEWSLETTER</h4>
            <p>Daftar untuk menerima penawaran spesial dan berita terbaru.</p>
        </div>
        <div>
            <h4>RESERVASI</h4>
            <p>Hubungi kami untuk pemesanan cepat dan mudah.</p>
            <p>&copy; 2025 Hotel Hebat</p>
        </div>
    </footer>
</body>
</html>";

// Close statement and connection
$stmt->close();
$conn->close();
?>