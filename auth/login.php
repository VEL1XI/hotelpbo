<?php
session_start();

// Cek jika user sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Pesan error default
$error = "";

// Proses login jika form di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Koneksi ke database
    require_once "../config/database.php";
    
    // Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi";
    } else {
        // Query untuk mencari user
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Password benar, buat session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect berdasarkan role
                if ($user['role'] == 'admin') {
                    header("Location: ../admin/modify.php");
                } else {
                    header("Location: ../index.php");
                }
                exit;
            } else {
                $error = "Password salah";
            }
        } else {
            $error = "Username tidak ditemukan";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hotel Hebat</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            line-height: 1.6;
            background: #f7f7f7;
        }
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .auth-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        .auth-form h2 {
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .auth-form button {
            background: #000;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            margin-top: 10px;
        }
        .auth-form button:hover {
            background: #333;
        }
        .auth-switch {
            text-align: center;
            margin-top: 20px;
        }
        .auth-switch a {
            color: #0066cc;
            text-decoration: none;
        }
        .auth-switch a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #d9534f;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f9f2f2;
            border: 1px solid #ebccd1;
            display: <?php echo !empty($error) ? 'block' : 'none'; ?>;
        }
        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #0066cc;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <a href="../index.php" class="back-link">← Kembali ke Home</a>
    
    <div class="auth-container">
        <div class="auth-form">
            <h2>Login</h2>
            
            <div class="error-message">
                <?php echo $error; ?>
            </div>
            
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Login</button>
            </form>
            
            <div class="auth-switch">
                Belum punya akun? <a href="register.html">Register</a>
            </div>
        </div>
    </div>
</body>
</html>