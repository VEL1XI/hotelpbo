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
                    $error_message = "Reservasi ini sudah dibatalkan sebelumnya.";
                }
                
                // Process cancellation if form is submitted
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_cancel'])) {
                    // Update reservation status to cancelled
                    $update_stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled', cancelled_at = NOW() WHERE reservation_code = :code");
                    $update_stmt->bindParam(':code', $reservation_code);
                    $update_stmt->execute();
                    
                    $success = true;
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batalkan Reservasi - Hotel Hebat</title>
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
        input[type="email"] {
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
        
        .cancel-button {
            background-color: #dc3545;
            color: white;
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
        
        .cancel-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 5px solid #ffc107;
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
        <h2>Batalkan Reservasi</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <p><?php echo $error_message; ?></p>
            </div>
            
            <div class="actions">
                <a href="../ubah-batal.html" class="button">Kembali</a>
            </div>
        <?php elseif ($success): ?>
            <div class="success-message">
                <p>Reservasi berhasil dibatalkan!</p>
                <p>Kode Reservasi: <?php echo $reservation_code; ?></p>
            </div>
            
            <div class="actions">
                <a href="../index.html" class="button">Kembali ke Beranda</a>
            </div>
        <?php elseif ($reservation): ?>
            <?php if ($_SERVER["REQUEST_METHOD"] != "POST"): ?>
                <div class="cancel-warning">
                    <h3>Perhatian!</h3>
                    <p>Anda akan membatalkan reservasi dengan kode <?php echo $reservation_code; ?>.</p>
                    <p>Tindakan ini tidak dapat dibatalkan. Pastikan Anda yakin ingin membatalkan reservasi ini.</p>
                </div>
                
                <div class="reservation-details">
                    <h3>Detail Reservasi</h3>
                    
                    <div class="detail-row">
                        <div class="detail-label">Kode Reservasi:</div>
                        <div class="detail-value"><?php echo $reservation['reservation_code']; ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Nama Tamu:</div>
                        <div class="detail-value"><?php echo $reservation['guest_name']; ?></div>
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
                        <div class="detail-label">Total Bayar:</div>
                        <div class="detail-value">Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></div>
                    </div>
                </div>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?code=" . urlencode($reservation_code) . "&email=" . urlencode($email); ?>">
                    <div class="actions">
                        <input type="hidden" name="confirm_cancel" value="1">
                        <a href="check_reservation.php" class="button">Kembali</a>
                        <button type="submit" class="button cancel-button">Konfirmasi Pembatalan</button>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2025 Hotel Hebat - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>