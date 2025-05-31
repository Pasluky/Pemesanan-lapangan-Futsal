<?php
session_start();
require 'config.php'; 

$is_logged_in = isset($_SESSION['Level']) && isset($_SESSION['username']);
$username_session = ''; 
if ($is_logged_in) {
    if ($_SESSION['Level'] == 'user') {
        $username_session = htmlspecialchars($_SESSION['username']);
    } elseif ($_SESSION['Level'] == 'admin') {
        $username_session = htmlspecialchars($_SESSION['username']) . " (Admin)";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Penyewaan Olahraga</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style type="text/css">
        body {
            padding-top: 56px; 
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1;
        }
        .navbar-brand-custom {
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .navbar-brand-custom svg {
            margin-right: 8px;
        }
        .about-header {
            background-color: #007bff; 
            color: white;
            padding: 4rem 1.5rem;
            text-align: center;
            border-bottom: 5px solid #0056b3; 
        }
        .about-header h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .about-header .lead {
            font-size: 1.3rem;
            margin-bottom: 0;
            color: rgba(255,255,255,0.9);
        }
        .section-padding {
            padding: 3rem 0;
        }
        .section-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #343a40;
        }
        .section-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }
        .value-card, .benefit-card {
            background-color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .value-card:hover, .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.12);
        }
        .value-card .icon-wrapper, .benefit-card .icon-wrapper {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .value-card h5, .benefit-card h5 {
            font-weight: bold;
            margin-bottom: 0.75rem;
            color: #007bff;
        }
        .value-card p, .benefit-card p {
            color: #495057;
            font-size: 0.95rem;
        }
        .team-section {
            background-color: #e9ecef; 
        }
        .cta-section {
            background-color: #343a40;
            color: white;
        }
        .cta-section h3 {
            font-weight: bold;
        }
        .img-story {
            border-radius: .5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        }
        .footer {
            padding: 1.5rem 0;
            background-color: #343a40; 
            color: #adb5bd;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto; 
        }
    </style>
</head>
<body>

<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="dribbble-icon-brand-about" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
    <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
  </symbol>
</svg>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="<?php echo $is_logged_in && isset($_SESSION['Level']) && $_SESSION['Level'] == 'user' ? 'user_home.php' : 'index.php'; ?>">
        <svg width="24" height="24" fill="currentColor"><use xlink:href="#dribbble-icon-brand-about"/></svg>
        Penyewaan Olahraga
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $is_logged_in && isset($_SESSION['Level']) && $_SESSION['Level'] == 'user' ? 'user_home.php' : 'index.php'; ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="field_catalog.php">Booking</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="history.php">Riwayat</a>
        </li>
         <li class="nav-item active">
          <a class="nav-link" href="about_us.php">Tentang Kami <span class="sr-only">(current)</span></a>
        </li>
      </ul>
      <?php if ($is_logged_in && isset($_SESSION['Level']) && $_SESSION['Level'] == 'user'): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $username_session; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="profil_pengguna.php">Profil Saya</a>
            <a class="dropdown-item" href="history.php">Riwayat Booking</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
            </div>
        </div>
      <?php elseif ($is_logged_in && isset($_SESSION['Level']) && $_SESSION['Level'] == 'admin'): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $username_session; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="admin_home.php">Dashboard Admin</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
            </div>
        </div>
      <?php else: ?>
        <a class="btn btn-success my-2 my-sm-0" href="login.php" role="button">Masuk / Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="content-wrapper">
<main role="main">

    <header class="about-header">
        <div class="container">
            <h1>Tentang Kami</h1>
            <p class="lead">Menghubungkan Anda dengan lapangan olahraga terbaik untuk pengalaman bermain yang tak terlupakan.</p>
        </div>
    </header>

    <section class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://i0.wp.com/rsum.bandaacehkota.go.id/wp-content/uploads/2025/02/lari.webp?fit=1279%2C853&ssl=1" class="img-fluid img-story" alt="Ilustrasi Tim">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title text-left mb-3">Selamat Datang di Platform Kami</h2>
                    <p class="text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                        Platform ini lahir dari kecintaan kami terhadap olahraga dan semangat untuk memudahkan setiap individu menemukan serta memesan lapangan olahraga secara praktis dan cepat. Kami percaya bahwa akses mudah ke fasilitas olahraga berkualitas adalah kunci untuk gaya hidup sehat dan komunitas yang lebih aktif.
                    </p>
                    <p class="text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                        Misi kami adalah menyediakan platform penyewaan lapangan olahraga yang mudah diakses, handal, dan mendukung terbentuknya komunitas olahraga yang solid serta suportif bagi semua kalangan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title">Nilai-Nilai yang Kami Junjung</h2>
            <p class="section-subtitle">Prinsip yang memandu setiap langkah kami dalam melayani Anda.</p>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="value-card">
                        <div class="icon-wrapper text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                                <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.355-1.506.32-2.075z"/>
                            </svg>
                        </div>
                        <h5>Kemudahan Akses</h5>
                        <p>Platform intuitif untuk booking cepat di mana saja, kapan saja.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="value-card">
                         <div class="icon-wrapper text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-patch-check-fill" viewBox="0 0 16 16">
                                <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89.01-.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89-.01.622-.636zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708z"/>
                            </svg>
                        </div>
                        <h5>Kualitas Terjamin</h5>
                        <p>Menyediakan lapangan dan fasilitas pendukung dengan standar terbaik.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="value-card">
                        <div class="icon-wrapper" style="color: #ffc107;">
                           <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                            </svg>
                        </div>
                        <h5>Komunitas Aktif</h5>
                        <p>Membangun wadah bagi para pecinta olahraga untuk terhubung.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="value-card">
                        <div class="icon-wrapper" style="color: #dc3545;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 0c-.001 0-.001 0 0 0l-.042.01L1.354 3.093a.804.804 0 0 0-.303.304C.791 3.858.56 4.66.503 5.835.42 7.39.963 9.95 8 16c7.037-6.05 7.58-8.61.497-10.165A1.107 1.107 0 0 0 14.646 3.093L8.042.01C8.001 0 8.001 0 8 0zM8 5a.5.5 0 0 1 .5.5v1.5a.5.5 0 1 1-1 0V5.5A.5.5 0 0 1 8 5zm0 4a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            </svg>
                        </div>
                        <h5>Kepercayaan Pengguna</h5>
                        <p>Menjamin transaksi aman dan informasi yang transparan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Mengapa Memilih Layanan Kami?</h2>
            <p class="section-subtitle">Kami memberikan lebih dari sekadar penyewaan lapangan.</p>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="benefit-card">
                        <div class="icon-wrapper text-success">
                           <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-calendar2-week-fill" viewBox="0 0 16 16">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 7h12v2H2V7zm0 3h12v2H2v-2zm1.5-6h9V1H3.5v2z"/>
                            </svg>
                        </div>
                        <h5>Jadwal Real-time</h5>
                        <p>Lihat ketersediaan lapangan secara langsung dan update.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="benefit-card">
                         <div class="icon-wrapper text-info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-credit-card-2-front-fill" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-2zm0 3a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1z"/>
                            </svg>
                        </div>
                        <h5>Pembayaran Mudah & Aman</h5>
                        <p>Berbagai metode pembayaran online yang terjamin keamanannya.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="benefit-card">
                        <div class="icon-wrapper text-warning">
                           <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-headset" viewBox="0 0 16 16">
                                <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5z"/>
                            </svg>
                        </div>
                        <h5>Dukungan Pelanggan Responsif</h5>
                        <p>Tim kami siap membantu menjawab pertanyaan dan kendala Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding team-section">
        <div class="container">
            <h2 class="section-title">Tim di Balik Platform Ini</h2>
            <p class="section-subtitle">Kami adalah sekelompok individu yang bersemangat tentang olahraga dan teknologi.</p>
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <p class="text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                        Didukung oleh tim profesional yang berdedikasi, kami bekerja keras setiap hari untuk meningkatkan platform ini, memastikan Anda mendapatkan pengalaman terbaik dalam mencari dan menyewa lapangan olahraga. Keahlian kami beragam, mulai dari pengembangan teknologi, layanan pelanggan, hingga pemasaran, semuanya bersatu untuk satu tujuan: memajukan olahraga di Indonesia.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding cta-section text-center">
        <div class="container">
            <h3 class="mb-4">Siap untuk Mulai Berolahraga?</h3>
            <p class="lead mb-4">Jangan tunda lagi! Temukan lapangan impian Anda dan pesan sekarang juga melalui platform kami.</p>
            <a href="field_catalog.php" class="btn btn-primary btn-lg px-5">Cari Lapangan Sekarang</a>
            <a href="contact_us.php" class="btn btn-outline-light btn-lg px-5 ml-2">Hubungi Kami</a>
        </div>
    </section>

</main>
</div>

<footer class="footer">
    <div class="container">
        <span>&copy; <?php echo date("Y"); ?> Layanan Penyewaan Olahraga. Semua Hak Dilindungi.</span>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
</body>
</html>