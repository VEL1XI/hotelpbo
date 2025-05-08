<?php
// Mulai session untuk manajemen login
session_start();

// Cek apakah user sudah login
$logged_in = isset($_SESSION['user_id']);
$user_name = $logged_in ? $_SESSION['user_name'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Hebat</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      line-height: 1.6;
    }
    header {
      background: #000;
      color: #fff;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 999;
      box-sizing: border-box;
    }
    header nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
    }
    header nav a:hover {
      text-decoration: underline;
    }
    .hero {
      background: url('images/hero.jpg') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: white;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.7);
      margin-top: 70px;
    }
    .hero h1 {
      font-size: 4rem;
      margin-bottom: 2rem;
    }
    .search-box {
      background: rgba(255, 255, 255, 0.85);
      padding: 2rem;
      border-radius: 10px;
      width: 80%;
      max-width: 800px;
      margin: 0 auto;
    }
    .search-box h2 {
      color: #333;
      margin-top: 0;
      text-shadow: none;
      text-align: center;
    }
    .search-box form {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: center;
    }
    .search-box form > div {
      flex: 1;
      min-width: 200px;
    }
    .search-box label {
      display: block;
      color: #333;
      margin-bottom: 0.5rem;
      font-weight: bold;
      text-shadow: none;
    }
    .search-box input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
    }
    .search-box button {
      background: #000;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
      margin-top: 1.5rem;
    }
    .search-box button:hover {
      background: #333;
    }
    section {
      padding: 60px 20px;
      text-align: center;
    }
    .types, .facilities {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 2rem;
    }
    .type, .facility {
      background: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 10px;
      width: 250px;
      padding: 20px;
    }
    .type img, .facility img {
      max-width: 100%;
      border-radius: 8px;
    }
    .about {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 2rem;
      align-items: center;
    }
    .about-text {
      flex: 1;
      min-width: 300px;
      text-align: left;
    }
    .about img {
      max-width: 400px;
      border-radius: 8px;
    }
    footer {
      background: #0b0c2a;
      color: white;
      padding: 40px 20px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
    }
    footer div {
      flex: 1;
      min-width: 200px;
      margin-bottom: 20px;
    }
    
    /* Auth Form Styles */
    .auth-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 80px 20px 20px;
      background: #f7f7f7;
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
    
    @media (max-width: 768px) {
      .search-box {
        width: 90%;
        padding: 1.5rem;
      }
      .search-box form {
        flex-direction: column;
      }
      .hero h1 {
        font-size: 3rem;
      }
      header {
        flex-direction: column;
        padding: 1rem;
      }
      header nav {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
      }
      header nav a {
        margin: 5px 10px;
      }
    }
  </style>
</head>
<body>
  <header>
    <div><strong>HOTEL HEBAT</strong></div>
    <nav>
      <a href="index.php">Home</a>
      <a href="booking/kamar.php">Kamar</a>
      <a href="#facilities">Facilities</a>
      <a href="#about">About Us</a>
      <?php if($logged_in): ?>
        <a href="user/user_dashboard.php">Dashboard</a>
        <a href="auth/logout.php">Logout (<?php echo htmlspecialchars($user_name); ?>)</a>
      <?php else: ?>
        <a href="auth/login.php">Login</a>
        <a href="auth/register.html">Register</a>
      <?php endif; ?>
    </nav>
  </header>

  <div class="hero">
    <h1>Hotel Hebat</h1>
    <div class="search-box">
      <h2>Cek Ketersediaan Kamar</h2>
      <form action="booking/search.php" method="GET">
        <div>
          <label for="checkin">Check-in:</label>
          <input type="date" name="checkin" id="checkin" required>
        </div>
        
        <div>
          <label for="checkout">Check-out:</label>
          <input type="date" name="checkout" id="checkout" required>
        </div>
        
        <div>
          <label for="guests">Jumlah Tamu:</label>
          <input type="number" name="guests" id="guests" min="1" max="10" required>
        </div>
        
        <div>
          <button type="submit">Cari Kamar</button>
        </div>
      </form>
    </div>
  </div>

  <section id="rooms">
    <h2>Hotel Types</h2>
    <div class="types">
      <div class="type">
        <img src="images/standard.jpg" alt="Standard Room">
        <h3>Standard Room</h3>
        <p>Rp 300.000 / malam</p>
      </div>
      <div class="type">
        <img src="images/premium.jpg" alt="premium Room">
        <h3>Premium Room</h3>
        <p>Rp 450.000 / malam</p>
      </div>
      <div class="type">
        <img src="images/VIP.jpg" alt="VIP Room">
        <h3>VIP Room</h3>
        <p>Rp 600.000 / malam</p>
      </div>
    </div>
  </section>

  <section id="facilities">
    <h2>Hotel Facilities</h2>
    <div class="facilities">
      <div class="facility">
        <img src="images/spa.jpg" alt="Spa">
        <p>Spa & Massage</p>
      </div>
      <div class="facility">
        <img src="images/gym.jpg" alt="Gym">
        <p>Gym</p>
      </div>
      <div class="facility">
        <img src="images/pool.jpg" alt="Pool">
        <p>Swimming Pool</p>
      </div>
      <div class="facility">
        <img src="images/restaurant.jpg" alt="Restaurant">
        <p>Restaurant</p>
      </div>
    </div>
  </section>

  <section id="about">
    <h2>About Us</h2>
    <div class="about">
      <div class="about-text">
        <p>Hotel Hebat adalah hotel modern yang terletak strategis di pusat kota. Kami menyediakan kamar nyaman, fasilitas lengkap, dan layanan terbaik untuk membuat pengalaman menginap Anda menyenangkan.</p>
      </div>
      <img src="images/about.jpg" alt="About Hotel">
    </div>
  </section>

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
</html>