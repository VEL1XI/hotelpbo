<?php
// Start session
session_start();

// Database connection configuration
$db_host = "localhost";
$db_user = "username";  // Change to your database username
$db_pass = "password";  // Change to your database password
$db_name = "hotel_hebat";

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate date format
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Initialize variables
$reservation_code = $email = "";
$error_message = "";
$success = false;
$reservation = null;

// Check if code and email are provided in URL
if (isset($_GET['code']) && isset($_GET['email'])) {
    $reservation_code = sanitize_input($_GET['code']);
    $email = sanitize_input($_GET['email']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    }
    
    // If no errors, proceed with retrieving the reservation
    if (empty($error_message)) {
        try {
            // Connect to database using PDO
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Prepare SQL statement to get reservation details
            $stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_code = :code AND email = :email");
            
            // Bind parameters
            $stmt->bindParam(':code', $reservation_code);
            $stmt->bindParam(':email', $email);
            
            // Execute the statement
            $stmt->execute();
            
            // Check if reservation exists
            if ($stmt->rowCount() > 0) {
                // Fetch reservation data
                $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Check if already cancelled
                if (isset($reservation['status']) && $reservation['status'] === 'cancelled') {
                    $error_message = "Reservasi ini sudah dibatalkan dan tidak dapat diubah.";
                }
                
                // Process form submission for modification
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_reservation'])) {
                    // Validate checkin and checkout dates
                    $checkin = sanitize_input($_POST["checkin"]);
                    $checkout = sanitize_input($_POST["checkout"]);
                    
                    if (!validateDate($checkin)) {
                        $error_message = "Format tanggal check-in tidak valid.";
                    } else if (!validateDate($checkout)) {
                        $error_message = "Format tanggal check-out tidak valid.";
                    } else {
                        $checkin_date = new DateTime($checkin);
                        $checkout_date = new DateTime($checkout);
                        
                        if ($checkout_date <= $checkin_date) {
                            $error_message = "Tanggal check-out harus setelah tanggal check-in.";
                        } else {
                            // Get selected room type
                            $room_type = sanitize_input($_POST["room_type"]);
                            
                            // Get room rate from room_type
                            $room_rates = [
                                "1" => 450000, // Rate for Premium room
                                "2" => 300000, // Rate for Standard room
                                "3" => 600000  // Rate for VIP room
                            ];
                            
                            $room_rate = $room_rates[$room_type] ?? 0;
                            
                            // Calculate stay duration in days
                            $stay_duration = $checkin_date->diff($checkout_date)->days;
                            
                            // Calculate total price
                            $total_price = $room_rate * $stay_duration;
                            
                            // Update reservation in database
                            $update_stmt = $conn->prepare("UPDATE reservations SET 
                                room_type = :room_type,
                                checkin_date = :checkin,
                                checkout_date = :checkout,
                                total_price = :price,
                                last_updated = NOW()
                                WHERE reservation_code = :code");
                            
                            $update_stmt->bindParam(':room_type', $room_type);
                            $update_stmt->bindParam(':checkin', $checkin);
                            $update_stmt->bindParam(':checkout', $checkout);
                            $update_stmt->bindParam(':price', $total_price);
                            $update_stmt->bindParam(':code', $reservation_code);
                            
                            $update_stmt->execute();
                            
                            // Refresh reservation data
                            $stmt->execute();
                            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $success = true;
                        }
                    }
                }
            } else {
                $error_message = "Reservasi tidak ditemukan. Silakan periksa kembali kode reservasi dan email Anda.";
            }
            
        } catch(PDOException $e) {
            $error_message = "Terjadi kesalahan: " . $e->getMessage();
        }
        
        // Close connection
        $conn = null;
    }
} else {
    $error_message = "Parameter tidak lengkap.";
}

// Function to get room type name from ID
function getRoomTypeName($room_id) {
    $room_names = [
        "1" => "Kamar Premium",
        "2" => "Kamar Standard",
        "3" => "Kamar VIP"
    ];
    
    return $room_names[$room_id] ?? "Unknown";
}

// Function to get payment method name
function getPaymentMethodName($method) {
    $methods = [
        "transfer" => "Transfer Bank",
        "card" => "Kartu Kredit/Debit",
        "cash" => "Bayar di Tempat"
    ];
    
    return $methods[$method] ?? "Unknown";
}

// Calculate stay duration
function calculateStayDuration($checkin, $checkout) {
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);
    return $checkin_date->diff($checkout_date)->days;
}

// Get current date in Y-m-d format
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Reservasi - Hotel Hebat</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .reservation-details {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .reservation-form {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        button {
            background-color: #f7b731;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }
        
        .detail-row {
            display: flex;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        
        .detail-label {
            width: 40%;
            font-weight: bold;
        }
        
        .detail-value {
            width: 60%;
        }
        
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f7b731;
            color: #000;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 0 10px;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 5px solid #dc3545;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 5px solid #28a745;
        }
        
        .note-message {
            background-color: #e2f0fb;
            color: #0c5460;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 5px solid #17a2b8;
        }
        
        .original-data {
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
            font-style: italic;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <header>
        <h1>HOTEL HEBAT</h1>
        <nav>
            <a href="../index.html">Home</a>
            <a href="../kamar.html">Kamar</a>
            <a href="../pesan.html">Reservasi</a>
            <a href="../ubah-batal.html">Ubah/Batal Reservasi</a>
        </nav>
    </header>
    
    <main class="container">
        <h2>Ubah Reservasi</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <p><?php echo $error_message; ?></p>
            </div>
            
            <div class="actions">
                <a href="../ubah-batal.html" class="button">Kembali</a>
            </div>
        <?php elseif ($success): ?>
            <div class="success-message">
                <p>Reservasi berhasil diperbarui!</p>
                <p>Kode Reservasi: <?php echo $reservation_code; ?></p>
            </div>
            
            <div class="reservation-details">
                <h3>Detail Reservasi Terbaru</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Kode Reservasi:</div>
                    <div class="detail-value"><?php echo $reservation['reservation_code']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Nama Tamu:</div>
                    <div class="detail-value"><?php echo $reservation['guest_name']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo $reservation['email']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Jenis Kamar:</div>
                    <div class="detail-value"><?php echo getRoomTypeName($reservation['room_type']); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Tanggal Check-in:</div>
                    <div class="detail-value"><?php echo date("d-m-Y", strtotime($reservation['checkin_date'])); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Tanggal Check-out:</div>
                    <div class="detail-value"><?php echo date("d-m-Y", strtotime($reservation['checkout_date'])); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Lama Menginap:</div>
                    <div class="detail-value"><?php echo calculateStayDuration($reservation['checkin_date'], $reservation['checkout_date']); ?> malam</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Total Bayar:</div>
                    <div class="detail-value">Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Metode Pembayaran:</div>
                    <div class="detail-value"><?php echo getPaymentMethodName($reservation['payment_method']); ?></div>
                </div>
            </div>
            
            <div class="actions">
                <a href="../index.html" class="button">Kembali ke Beranda</a>
                <a href="check_reservation.php" class="button">Cek Reservasi Lain</a>
            </div>
        <?php elseif ($reservation): ?>
            <div class="note-message">
                <p>Anda dapat mengubah jenis kamar, tanggal check-in, dan tanggal check-out untuk reservasi Anda.</p>
                <p>Nama tamu dan metode pembayaran tidak dapat diubah.</p>
            </div>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?code=" . urlencode($reservation_code) . "&email=" . urlencode($email); ?>" class="reservation-form">
                <div class="form-group">
                    <label for="guest_name">Nama Tamu:</label>
                    <input type="text" id="guest_name" value="<?php echo $reservation['guest_name']; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" value="<?php echo $reservation['email']; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="room_type">Jenis Kamar:</label>
                    <select id="room_type" name="room_type" required>
                        <option value="1" <?php echo ($reservation['room_type'] == '1') ? 'selected' : ''; ?>>Kamar Premium - Rp 450.000/malam</option>
                        <option value="2" <?php echo ($reservation['room_type'] == '2') ? 'selected' : ''; ?>>Kamar Standard - Rp 300.000/malam</option>
                        <option value="3" <?php echo ($reservation['room_type'] == '3') ? 'selected' : ''; ?>>Kamar VIP - Rp 600.000/malam</option>
                    </select>
                    <div class="original-data">Jenis kamar saat ini: <?php echo getRoomTypeName($reservation['room_type']); ?></div>
                </div>
                
                <div class="form-group">
                    <label for="checkin">Tanggal Check-in:</label>
                    <input type="date" id="checkin" name="checkin" value="<?php echo $reservation['checkin_date']; ?>" min="<?php echo $today; ?>" required>
                    <div class="original-data">Tanggal check-in saat ini: <?php echo date("d-m-Y", strtotime($reservation['checkin_date'])); ?></div>
                </div>
                
                <div class="form-group">
                    <label for="checkout">Tanggal Check-out:</label>
                    <input type="date" id="checkout" name="checkout" value="<?php echo $reservation['checkout_date']; ?>" min="<?php echo $today; ?>" required>
                    <div class="original-data">Tanggal check-out saat ini: <?php echo date("d-m-Y", strtotime($reservation['checkout_date'])); ?></div>
                </div>
                
                <div class="form-group">
                    <label for="payment">Metode Pembayaran:</label>
                    <input type="text" id="payment" value="<?php echo getPaymentMethodName($reservation['payment_method']); ?>" disabled>
                </div>
                
                <div class="actions">
                    <input type="hidden" name="update_reservation" value="1">
                    <a href="check_reservation.php" class="button">Batal</a>
                    <button type="submit" class="button">Simpan Perubahan</button>
                </div>
            </form>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2025 Hotel Hebat - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>