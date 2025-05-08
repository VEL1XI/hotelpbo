<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Kamar - Hotel Hebat</title>
  <style>
    :root {
      --primary-color: #0b0c2a;
      --secondary-color: #f5b917;
      --text-color: #333;
      --light-gray: #f7f7f7;
      --border-radius: 8px;
      --box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      color: var(--text-color);
      background-color: var(--light-gray);
    }
    
    /* Header */
    header {
      background-color: var(--primary-color);
      color: white;
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .header-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      text-decoration: none;
      color: white;
    }
    
    nav {
      display: flex;
      gap: 20px;
    }
    
    nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      padding: 5px 10px;
      border-radius: var(--border-radius);
    }
    
    nav a:hover, nav a.active {
      background-color: rgba(255, 255, 255, 0.2);
    }
    
    /* Main Content */
    main {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 20px;
    }
    
    .page-title {
      text-align: center;
      margin-bottom: 2rem;
      color: var(--primary-color);
      position: relative;
      padding-bottom: 15px;
    }
    
    .page-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background-color: var(--secondary-color);
    }
    
    /* Room List */
    .room-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 2rem;
    }
    
    .room-card {
      background-color: white;
      border-radius: var(--border-radius);
      overflow: hidden;
      box-shadow: var(--box-shadow);
      transition: transform 0.3s ease;
    }
    
    .room-card:hover {
      transform: translateY(-5px);
    }
    
    .room-image {
      height: 250px;
      overflow: hidden;
      position: relative;
    }
    
    .room-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .room-card:hover .room-image img {
      transform: scale(1.05);
    }
    
    .room-details {
      padding: 1.5rem;
    }
    
    .room-title {
      color: var(--primary-color);
      margin-bottom: 1rem;
      font-size: 1.4rem;
    }
    
    .facility-list {
      list-style: none;
      margin-bottom: 1rem;
    }
    
    .facility-list li {
      margin-bottom: 8px;
      padding-left: 25px;
      position: relative;
    }
    
    .facility-list li::before {
      content: '✓';
      position: absolute;
      left: 0;
      color: var(--secondary-color);
      font-weight: bold;
    }
    
    .room-price {
      font-size: 1.2rem;
      font-weight: bold;
      color: var(--primary-color);
      margin: 1rem 0;
    }
    
    .room-capacity {
      color: #666;
      margin-bottom: 1.5rem;
    }
    
    .btn {
      display: inline-block;
      background-color: var(--secondary-color);
      color: var(--primary-color);
      text-decoration: none;
      padding: 10px 20px;
      border-radius: var(--border-radius);
      font-weight: bold;
      transition: all 0.3s ease;
      text-align: center;
      width: 100%;
    }
    
    .btn:hover {
      background-color: var(--primary-color);
      color: white;
    }
    
    /* Footer */
    footer {
      background-color: var(--primary-color);
      color: white;
      text-align: center;
      padding: 2rem 0;
      margin-top: 3rem;
    }
    
    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      text-align: left;
      padding: 0 20px;
    }
    
    .footer-section h3 {
      margin-bottom: 1rem;
      position: relative;
      padding-bottom: 10px;
    }
    
    .footer-section h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 2px;
      background-color: var(--secondary-color);
    }
    
    .footer-section p {
      margin-bottom: 0.5rem;
    }
    
    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 1rem;
    }
    
    .social-links a {
      color: white;
      text-decoration: none;
    }
    
    .copyright {
      margin-top: 2rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding-top: 1rem;
      font-size: 0.9rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        padding: 1rem;
      }
      
      .logo {
        margin-bottom: 1rem;
      }
      
      nav {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
      }
      
      nav a {
        padding: 8px 12px;
        margin: 5px;
      }
      
      .room-list {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="header-container">
      <a href="\hotelpbo-main\index.php" class="logo">HOTEL HEBAT</a>
      <nav>
        <a href="\hotelpbo-main\index.php">Home</a>
        <a href="booking/kamar.php" class="active">Kamar</a>
        <a href="#facilities">Fasilitas</a>
        <a href="#about">Tentang Kami</a>
        <a href="/auth/login.php">Login</a>
      </nav>
    </div>
  </header>

  <main>
    <h1 class="page-title">Pilihan Kamar Terbaik</h1>
    
    <div class="room-list">
      <div class="room-card">
        <div class="room-image">
          <img src="/images/standard.jpg" alt="Standard Room">
        </div>
        <div class="room-details">
          <h2 class="room-title">Standard Room</h2>
          <ul class="facility-list">
            <li>Tempat Tidur Queen Size</li>
            <li>AC dan TV Layar Datar</li>
            <li>Wi-Fi Gratis</li>
            <li>Kamar Mandi Dalam</li>
          </ul>
          <p class="room-price">Rp 300.000 <span>/ malam</span></p>
          <p class="room-capacity">Maksimal 2 Tamu</p>
          <a href="/booking/reserve.php?type=standard" class="btn">Pesan Sekarang</a>
        </div>
      </div>
      
      <div class="room-card">
        <div class="room-image">
          <img src="/images/premium.jpg" alt="Premium Room">
        </div>
        <div class="room-details">
          <h2 class="room-title">Premium Room</h2>
          <ul class="facility-list">
            <li>Tempat Tidur King Size</li>
            <li>AC dan Smart TV</li>
            <li>Wi-Fi Kecepatan Tinggi</li>
            <li>Balkon Pribadi</li>
            <li>Bathtub</li>
          </ul>
          <p class="room-price">Rp 450.000 <span>/ malam</span></p>
          <p class="room-capacity">Maksimal 2 Tamu</p>
          <a href="/booking/reserve.php?type=premium" class="btn">Pesan Sekarang</a>
        </div>
      </div>
      
      <div class="room-card">
        <div class="room-image">
          <img src="/images/VIP.jpg" alt="VIP Room">
        </div>
        <div class="room-details">
          <h2 class="room-title">VIP Room</h2>
          <ul class="facility-list">
            <li>Tempat Tidur Super King Size</li>
            <li>Ruang Tamu Terpisah</li>
            <li>Smart TV & Sound System</li>
            <li>Mini Bar & Dapur Kecil</li>
            <li>Jacuzzi</li>
            <li>Sarapan Gratis</li>
          </ul>
          <p class="room-price">Rp 600.000 <span>/ malam</span></p>
          <p class="room-capacity">Maksimal 3 Tamu</p>
          <a href="\booking\reserve.php?type=vip" class="btn">Pesan Sekarang</a>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <div class="footer-content">
      <div class="footer-section">
        <h3>TENTANG KAMI</h3>
        <p>Hotel Hebat adalah hotel modern yang terletak strategis di pusat kota. Kami menyediakan kamar nyaman, fasilitas lengkap, dan layanan terbaik untuk membuat pengalaman menginap Anda menyenangkan.</p>
      </div>
      
      <div class="footer-section">
        <h3>KONTAK</h3>
        <p>Jl. Merdeka No. 123, Jakarta</p>
        <p>Telepon: (021) 1234-5678</p>
        <p>Email: info@hotelhebat.com</p>
      </div>
      
      <div class="footer-section">
        <h3>FOLLOW US</h3>
        <p>Ikuti kami di media sosial untuk mendapatkan penawaran spesial dan berita terbaru.</p>
        <div class="social-links">
          <a href="#">Facebook</a>
          <a href="#">Instagram</a>
          <a href="#">Twitter</a>
        </div>
      </div>
    </div>
    
    <div class="copyright">
      &copy; 2025 Hotel Hebat. All Rights Reserved.
    </div>
  </footer>
</body>
</html>