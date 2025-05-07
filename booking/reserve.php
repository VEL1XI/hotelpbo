<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $guest_name = $_POST["guest_name"];
  $email = $_POST["email"];
  $phone = $_POST["phone"];
  $room_type = $_POST["room_type"];
  $checkin = $_POST["checkin"];
  $checkout = $_POST["checkout"];
  $payment = $_POST["payment"];

  $sql = "INSERT INTO reservations 
          (guest_name, email, phone, room_type, checkin_date, checkout_date, payment_method) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssss", $guest_name, $email, $phone, $room_type, $checkin, $checkout, $payment);

  if ($stmt->execute()) {
    echo "<script>
      alert('Reservasi berhasil! Anda akan menerima konfirmasi melalui email.');
      window.location.href = '../index.html';
    </script>";
  } else {
    echo "Terjadi kesalahan: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
} else {
  echo "Akses tidak valid.";
}
?>
