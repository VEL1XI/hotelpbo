<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "hotel_hebat";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Nama tidak boleh kosong";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);
    if ($result->num_rows > 0) {
        $errors[] = "Email sudah terdaftar";
    }
    
    // Validate phone
    if (empty($phone)) {
        $errors[] = "Nomor telepon tidak boleh kosong";
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }
    
    // Validate confirm password
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak sama";
    }
    
    // Check terms checkbox
    if (!isset($_POST['terms'])) {
        $errors[] = "Anda harus menyetujui syarat dan ketentuan";
    }
    
    // If there are errors, redirect back to register page
    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        header("Location: ../register.html");
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user into database
    $sql = "INSERT INTO users (name, email, phone, password, created_at) 
            VALUES ('$name', '$email', '$phone', '$hashed_password', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        // Registration successful
        $_SESSION['register_success'] = "Pendaftaran berhasil! Silakan login dengan akun baru Anda.";
        header("Location: ../login.html");
        exit;
    } else {
        // Error in registration
        $_SESSION['register_error'] = "Terjadi kesalahan: " . $conn->error;
        header("Location: ../register.html");
        exit;
    }
}

// Redirect to register page if not a POST request
header("Location: ../register.html");
exit;

$conn->close();
?>