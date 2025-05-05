<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ubah / Batal Reservasi</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header>
    <h1>Ubah atau Batal Reservasi</h1>
    <nav>
      <a href="index.html">Beranda</a>
      <a href="kamar.html">Kamar</a>
      <a href="pesan.html">Reservasi</a>
      <a href="ubah-batal.html">Ubah/Batal Reservasi</a>
    </nav>
  </header>

  <main>
    <section class="modify-reservation">
      <h2>Cari Reservasi</h2>
      <form action="php/search-reservation.php" method="POST">
        <label for="reservation_id">ID Reservasi:</label>
        <input type="text" id="reservation_id" name="reservation_id" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />

        <button type="submit">Cari Reservasi</button>
      </form>
    </section>

    <section class="reservation-details">
      <h2>Detail Reservasi</h2>
      <div id="reservation-info">
        <!-- Data reservasi yang ditemukan akan muncul di sini -->
        <!-- Form untuk mengubah tanggal dan membatalkan reservasi hanya muncul setelah reservasi ditemukan -->
      </div>

      <!-- Form untuk mengubah reservasi -->
      <form action="php/update-reservation.php" method="POST">
        <h3>Ubah Tanggal Reservasi</h3>
        <label for="new_checkin">Tanggal Check-in:</label>
        <input type="date" id="new_checkin" name="new_checkin" required />

        <label for="new_checkout">Tanggal Check-out:</label>
        <input type="date" id="new_checkout" name="new_checkout" required />

        <button type="submit">Ubah Reservasi</button>
      </form>

      <!-- Form untuk membatalkan reservasi -->
      <form action="php/cancel-reservation.php" method="POST">
        <h3>Batalkan Reservasi</h3>
        <button type="submit">Batalkan Reservasi</button>
      </form>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Hotel Nyaman</p>
  </footer>
</body>
</html>
