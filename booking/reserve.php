<?php
// Start session to store reservation data
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

// Initialize variables for form data
$guest_name = $email = $phone = $room_type = $checkin = $checkout = $payment = "";
$error_message = "";
$success = false;

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    if (empty($_POST["guest_name"])) {
        $error_message = "Nama lengkap harus diisi.";
    } else {
        $guest_name = sanitize_input($_POST["guest_name"]);
        // Check if name contains only letters and spaces
        if (!preg_match("/^[a-zA-Z\s]*$/", $guest_name)) {
            $error_message = "Nama hanya boleh berisi huruf dan spasi.";
        }
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

    if (empty($_POST["phone"])) {
        $error_message = "Nomor telepon harus diisi.";
    } else {
        $phone = sanitize_input($_POST["phone"]);
        // Check if phone number is valid (numeric and minimum length)
        if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
            $error_message = "Nomor telepon hanya boleh berisi angka (10-15 digit).";
        }
    }

    if (empty($_POST["room_type"])) {
        $error_message = "Jenis kamar harus dipilih.";
    } else {
        $room_type = sanitize_input($_POST["room_type"]);
    }

    if (empty($_POST["checkin"])) {
        $error_message = "Tanggal check-in harus diisi.";
    } else {
        $checkin = sanitize_input($_POST["checkin"]);
        if (!validateDate($checkin)) {
            $error_message = "Format tanggal check-in tidak valid.";
        }
    }

    if (empty($_POST["checkout"])) {
        $error_message = "Tanggal check-out harus diisi.";
    } else {
        $checkout = sanitize_input($_POST["checkout"]);
        if (!validateDate($checkout)) {
            $error_message = "Format tanggal check-out tidak valid.";
        }
    }

    // Check if checkout date is after checkin date
    if (!empty($checkin) && !empty($checkout)) {
        $checkin_date = new DateTime($checkin);
        $checkout_date = new DateTime($checkout);
        
        if ($checkout_date <= $checkin_date) {
            $error_message = "Tanggal check-out harus setelah tanggal check-in.";
        }
    }

    if (empty($_POST["payment"])) {
        $error_message = "Metode pembayaran harus dipilih.";
    } else {
        $payment = sanitize_input($_POST["payment"]);
    }

    // If no errors, proceed with saving the reservation
    if (empty($error_message)) {
        // Generate reservation code
        $reservation_code = "HH" . date("YmdHis") . rand(100, 999);
        
        try {
            // Connect to database using PDO
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Get room rate from room_type
            $room_rates = [
                "1" => 450000, // Rate for Deluxe room
                "2" => 300000, // Rate for Standard room
                "3" => 600000  // Rate for Family room
            ];
            
            $room_rate = $room_rates[$room_type] ?? 0;
            
            // Calculate stay duration in days
            $stay_duration = $checkin_date->diff($checkout_date)->days;
            
            // Calculate total price
            $total_price = $room_rate * $stay_duration;
            
            // Prepare SQL statement for inserting reservation
            $stmt = $conn->prepare("INSERT INTO reservations (reservation_code, guest_name, email, phone, room_type, checkin_date, checkout_date, payment_method, total_price, reservation_date) 
                                   VALUES (:code, :name, :email, :phone, :room_type, :checkin, :checkout, :payment, :price, NOW())");
            
            // Bind parameters
            $stmt->bindParam(':code', $reservation_code);
            $stmt->bindParam(':name', $guest_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':room_type', $room_type);
            $stmt->bindParam(':checkin', $checkin);
            $stmt->bindParam(':checkout', $checkout);
            $stmt->bindParam(':payment', $payment);
            $stmt->bindParam(':price', $total_price);
            
            // Execute the statement
            $stmt->execute();
            
            // Store reservation data in session
            $_SESSION['reservation'] = [
                'code' => $reservation_code,
                'name' => $guest_name,
                'email' => $email,
                'phone' => $phone,
                'room_type' => $room_type,
                'room_type_name' => getRoomTypeName($room_type),
                'checkin' => $checkin,
                'checkout' => $checkout,
                'payment' => $payment,
                'duration' => $stay_duration,
                'price' => $room_rate,
                'total' => $total_price
            ];
            
            $success = true;
            
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? "Konfirmasi Reservasi" : "Pemrosesan Reservasi"; ?> - Hotel Hebat</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .reservation-success {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #28a745;
        }
        
        .reservation-details {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        <?php if ($success): ?>
            <div class="reservation-success">
                <h2>Reservasi Berhasil!</h2>
                <p>Terima kasih telah memilih Hotel Hebat. Detail reservasi Anda tercatat dalam sistem kami.</p>
                <p><strong>Kode Reservasi: <?php echo $_SESSION['reservation']['code']; ?></strong></p>
                <p>Harap simpan kode reservasi ini untuk keperluan check-in atau perubahan reservasi.</p>
            </div>
            
            <div class="reservation-details">
                <h3>Detail Reservasi</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Nama Tamu:</div>
                    <div class="detail-value"><?php echo $_SESSION['reservation']['name']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo $_SESSION['reservation']['email']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Nomor Telepon:</div>
                    <div class="detail-value"><?php echo $_SESSION['reservation']['phone']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Jenis Kamar:</div>
                    <div class="detail-value"><?php echo $_SESSION['reservation']['room_type_name']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Tanggal Check-in:</div>
                    <div class="detail-value"><?php echo date("d-m-Y", strtotime($_SESSION['reservation']['checkin'])); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Tanggal Check-out:</div>
                    <div class="detail-value"><?php echo date("d-m-Y", strtotime($_SESSION['reservation']['checkout'])); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Lama Menginap:</div>
                    <div class="detail-value"><?php echo $_SESSION['reservation']['duration']; ?> malam</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Harga per Malam:</div>
                    <div class="detail-value">Rp <?php echo number_format($_SESSION['reservation']['price'], 0, ',', '.'); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Total Bayar:</div>
                    <div class="detail-value">Rp <?php echo number_format($_SESSION['reservation']['total'], 0, ',', '.'); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Metode Pembayaran:</div>
                    <div class="detail-value"><?php echo getPaymentMethodName($_SESSION['reservation']['payment']); ?></div>
                </div>
            </div>
            
            <div class="actions">
                <a href="../index.html" class="button">Kembali ke Beranda</a>
                <a href="javascript:window.print()" class="button">Cetak Konfirmasi</a>
            </div>
            
        <?php else: ?>
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <p><?php echo $error_message; ?></p>
                </div>
                
                <div class="actions">
                    <a href="../pesan.html" class="button">Kembali ke Form Reservasi</a>
                </div>
            <?php else: ?>
                <div class="error-message">
                    <p>Terjadi kesalahan saat memproses reservasi. Silakan coba lagi.</p>
                </div>
                
                <div class="actions">
                    <a href="../pesan.html" class="button">Kembali ke Form Reservasi</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2025 Hotel Hebat - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>