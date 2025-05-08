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
$reservation = null;

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    if (empty($_POST["reservation_code"])) {
        $error_message = "Kode reservasi harus diisi.";
    } else {
        $reservation_code = sanitize_input($_POST["reservation_code"]);
    }

    if (empty($_POST["email"])) {
        $error_message = "Email harus diisi.";
    } else {
        $email = sanitize_input($_POST["email"]);
        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Format email tidak valid.";
        }
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
            } else {
                $error_message = "Reservasi tidak ditemukan. Silakan periksa kembali kode reservasi dan email Anda.";
            }
            
        } catch(PDOException $e) {
            $error_message = "Terjadi kesalahan: " . $e->getMessage();
        }
        
        // Close connection
        $conn = null;
    }
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
    <title>Cek Reservasi - Hotel Hebat</title>
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
        <h2>Cek Status Reservasi</h2>
        
        <?php if (!$reservation): ?>
            <div class="reservation-form">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <?php if (!empty($error_message)): ?>
                        <div class="error-message">
                            <p><?php echo $error_message; ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="reservation_code">Kode Reservasi:</label>
                        <input type="text" id="reservation_code" name="reservation_code" value="<?php echo $reservation_code; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit">Cek Reservasi</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="success-message">
                <p>Reservasi ditemukan!</p>
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
                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo $reservation['email']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Nomor Telepon:</div>
                    <div class="detail-value"><?php echo $reservation['phone']; ?></div>
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
                
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">Terkonfirmasi</div>
                </div>
            </div>
            
            <div class="actions">
                <a href="../index.html" class="button">Kembali ke Beranda</a>
                <a href="modify_reservation.php?code=<?php echo $reservation['reservation_code']; ?>&email=<?php echo urlencode($reservation['email']); ?>" class="button">Ubah Reservasi</a>
                <a href="cancel_reservation.php?code=<?php echo $reservation['reservation_code']; ?>&email=<?php echo urlencode($reservation['email']); ?>" class="button" onclick="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?');">Batalkan Reservasi</a>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2025 Hotel Hebat - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>