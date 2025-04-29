<?php
include 'database.php';

$checkin = $_GET['checkin'];
$checkout = $_GET['checkout'];
$guests = intval($_GET['guests']);

// Query cari kamar yang belum dipesan di tanggal tersebut dan cukup kapasitas
$sql = "SELECT * FROM rooms 
        WHERE max_guests >= ? 
        AND id NOT IN (
            SELECT room_id FROM reservations 
            WHERE (? < checkout_date AND ? > checkin_date)
        )";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $guests, $checkout, $checkin);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Hasil Pencarian Kamar Tersedia</h2>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='room'>";
        echo "<h3>" . htmlspecialchars($row['room_name']) . "</h3>";
        echo "<img src='../images/" . htmlspecialchars($row['photo']) . "' alt='Foto Kamar' width='200'>";
        echo "<p>Ukuran Tempat Tidur: " . $row['bed_size'] . "</p>";
        echo "<p>Fasilitas: " . $row['facilities'] . "</p>";
        echo "<p>Harga per Malam: Rp" . number_format($row['price_per_night']) . "</p>";
        echo "<p>Kapasitas: " . $row['max_guests'] . " tamu</p>";
        echo "<a href='../pesan.html'>Pesan Sekarang</a>";
        echo "</div><hr>";
    }
} else {
    echo "<p>Tidak ada kamar tersedia untuk tanggal dan jumlah tamu yang dipilih.</p>";
}

$stmt->close();
$conn->close();
?>
