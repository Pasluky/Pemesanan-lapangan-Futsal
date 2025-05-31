<?php
session_start();
if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user") {
    header("location:login.php");
    exit;
}

$username_session = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna';
$user_id_session = $_SESSION['user_id'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang, <?php echo $username_session; ?>! - Penyewaan Olahraga</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 56px; 
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
        .jumbotron-user-home {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6),rgba(0, 0, 0, 0.6)), url("./img/Garasi-Futsal.jpg");
            background-size: cover;
            background-position: center;
            color: white;
            border-radius: 0;
            margin-bottom: 0;
            padding-top: 5rem; 
            padding-bottom: 5rem;
        }
        .jumbotron-user-home h1 {
            font-weight: 700; /* Lebih tebal */
            font-size: 3rem; /* Sedikit lebih besar */
        }
        .jumbotron-user-home .lead {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .jumbotron-user-home .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }
        .action-section {
            padding: 3rem 0;
            background-color: #ffffff; 
        }
        .action-section .section-title-action {
            font-weight: bold;
            margin-bottom: 1rem;
            color: #343a40;
        }
        .action-section .section-subtitle-action {
            color: #6c757d;
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }
        .action-card {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            transition: all 0.3s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .action-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            text-align: center;
            padding: 2rem;
        }
        .action-card .icon-wrapper {
            font-size: 3rem; 
            margin-bottom: 1rem;
        }
        .action-card .card-title {
            font-weight: bold;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }
        .action-card .card-text {
            color: #6c757d;
            font-size: 0.95rem;
            flex-grow: 1; 
            margin-bottom: 1.5rem;
        }
        .action-card .btn {
            padding: 0.5rem 1.5rem;
            font-weight: bold;
        }
        .icon-futsal { color: #28a745; } 
        .btn-futsal { background-color: #28a745; border-color: #28a745; color:white; }
        .btn-futsal:hover { background-color: #218838; border-color: #1e7e34; }

        .icon-status { color: #ffc107; } 
        .btn-status { background-color: #ffc107; border-color: #ffc107; color:#212529; }
        .btn-status:hover { background-color: #e0a800; border-color: #d39e00; }
        
        .icon-equipment { color: #17a2b8; } 
        .btn-equipment { background-color: #17a2b8; border-color: #17a2b8; color:white; }
        .btn-equipment:hover { background-color: #138496; border-color: #117a8b; }

        .how-to-section {
            padding: 3rem 0;
            background-color: #f8f9fa; 
        }
        .how-to-section .section-title {
            font-weight: bold;
            margin-bottom: 1rem;
        }
         .how-to-section .lead-text { 
            margin-bottom: 3rem;
            font-size: 1.1rem;
            color: #6c757d;
        }
        .how-to-section .step-item {
            text-align: center;
            padding: 1.5rem;
        }
        .how-to-section .step-icon-wrapper {
            width: 90px; 
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto; 
            font-size: 3rem; 
        }
        .step-icon-daftar { background-color: #e6f2ff; color: #007bff; } 
        .step-icon-pilih { background-color: #e9f7ef; color: #28a745; } 
        .step-icon-bayar { background-color: #fff8e1; color: #ffc107; } 

        .how-to-section .step-item h3 { 
            font-size: 1.25rem; 
            font-weight: bold;
            margin-bottom: 0.75rem;
        }
        .how-to-section .step-item p {
            font-size: 0.95rem; 
            color: #5a6268; 
            min-height: 70px; 
        }
         .how-to-section .step-item .btn {
            font-weight: bold;
        }
        .footer {
            padding: 1.5rem 0;
            background-color: #343a40; /* Footer gelap */
            color: #adb5bd;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto; 
        }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="dribbble-icon-brand" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
    <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
  </symbol>
</svg>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="user_home.php">
        <svg width="24" height="24" fill="currentColor"><use xlink:href="#dribbble-icon-brand"/></svg>
        Penyewaan Olahraga
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item active">
          <a class="nav-link" href="user_home.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="field_catalog.php">Booking</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="history.php">Riwayat</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="about_us.php">Tentang Kami</a>
        </li>
      </ul>
      <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo htmlspecialchars($username_session); ?>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="profil_pengguna.php">Profil Saya</a>
          <a class="dropdown-item" href="history.php">Riwayat Booking</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')">Keluar</a>
        </div>
      </div>
    </div>
  </div>
</nav>

<div class="content-wrapper">
<main role="main">
<div class="jumbotron jumbotron-user-home">
  <div class="container text-center">
    <h1 class="display-4">Selamat Datang, <?php echo htmlspecialchars($username_session); ?>!</h1>
    <p class="lead">Platform kami mudahkan Anda sewa lapangan olahraga pilihan. Cari jadwal tersedia, pesan online praktis, lalu dapatkan konfirmasi booking instan.</p>
    <p><a class="btn btn-primary btn-lg mt-2" href="#pilihan-lanjutan-user" role="button">Mulai Sekarang &raquo;</a></p>
  </div>
</div>

<div class="action-section" id="pilihan-lanjutan-user">
    <div class="container">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2 class="section-title-action">Apa yang Ingin Anda Lakukan?</h2>
                <p class="section-subtitle-action">Pilih salah satu layanan kami di bawah ini.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card action-card">
                    <div class="card-body">
                        <div class="icon-wrapper icon-futsal">
                            <svg width="64" height="64" fill="currentColor" class="bi bi-dribbble" viewBox="0 0 16 16"><use xlink:href="#dribbble-icon-brand"/></svg>
                        </div>
                        <h5 class="card-title">Sewa Lapangan</h5>
                        <p class="card-text">Temukan dan pesan lapangan futsal atau olahraga lainnya dengan mudah dan cepat.</p>
                        <a class="btn btn-futsal btn-block" href="booking.php" role="button">Pesan Lapangan &raquo;</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card action-card">
                    <div class="card-body">
                        <div class="icon-wrapper icon-status">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-calendar2-heart-fill" viewBox="0 0 16 16">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM8 6.093c.084-.402.516-1.093 1.343-1.093.828 0 1.343.691 1.343 1.093s-.515.691-1.343 1.093L8 7.681l-1.343-.5S6 6.493 6 6.093c0-.402.515-1.093 1.343-1.093.828 0 1.259.691 1.343 1.093z"/>
                                <path d="M8 7.93C7.577 7.57 7.001 7.22 6.5 7.014C5.404 6.548 4.5 6.963 4.5 8.093c0 .395.173.734.412 1.003L8 12.15l3.088-3.054A1.51 1.51 0 0 0 11.5 8.093c0-1.13-.904-1.545-2-1.079C9 7.22 8.423 7.57 8 7.93zm.325 3.166a.5.5 0 0 0-.65 0L4 14.341V14a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v.341l-3.675-3.244z"/>
                            </svg>
                        </div>
                        <h5 class="card-title">Riwayat & Status Booking</h5>
                        <p class="card-text">Lihat riwayat pemesanan Anda dan pantau status booking yang sedang aktif.</p>
                        <a class="btn btn-status btn-block" href="history.php" role="button">Lihat Riwayat &raquo;</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card action-card">
                    <div class="card-body">
                        <div class="icon-wrapper icon-equipment">
                             <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-collection-play-fill" viewBox="0 0 16 16">
                                <path d="M1.5 14.5A1.5 1.5 0 0 1 0 13V3a1.5 1.5 0 0 1 1.5-1.5h13A1.5 1.5 0 0 1 16 3v10a1.5 1.5 0 0 1-1.5 1.5h-13zm5.265-8.076A.5.5 0 0 0 6 6.868v2.264a.5.5 0 0 0 .765.432l2.045-1.132a.5.5 0 0 0 0-.864l-2.045-1.132z"/>
                                <path d="M2.5 0a.5.5 0 0 0 0 1h11a.5.5 0 0 0 0-1h-11zm0 15a.5.5 0 0 0 0 1h11a.5.5 0 0 0 0-1h-11z"/>
                            </svg>
                        </div>
                        <h5 class="card-title">Sewa Peralatan</h5>
                        <p class="card-text">Lihat katalog peralatan pendukung untuk melengkapi permainan Anda.</p>
                        <a class="btn btn-equipment btn-block" href="peralatan_user.php" role="button">Lihat Peralatan &raquo;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="how-to-section">
  <div class="container">
      <hr class="my-5">
      <div class="row">
        <div class="col-12 text-center">
            <h2 class="section-title">Bagaimana Cara Menyewa dari Kami?</h2>
            <p class="lead-text">Berikut adalah langkah-langkah mudah untuk memulai.</p>
        </div>
        <div class="col-md-4 mb-4">
            <div class="step-item">
              <div class="step-icon-wrapper step-icon-daftar">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
              </div>
              <h3 class="font-weight-bold mt-3 mb-3">1. AKUN ANDA</h3>
              <p class="text-muted px-3">Anda sudah masuk! Anda dapat langsung memilih lapangan atau melihat riwayat pesanan Anda.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
           <div class="step-item">
             <div class="step-icon-wrapper step-icon-pilih">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-calendar2-check-fill" viewBox="0 0 16 16">
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zm9.5 8.5H9.75a.75.75 0 0 0 0 1.5h2.5a.75.75 0 0 0 0-1.5h-1.5a.25.25 0 0 1-.25-.25V7.5a.75.75 0 0 0-1.5 0v1a.25.25 0 0 1-.25.25h-1.5a.75.75 0 0 0 0 1.5h1.25a.75.75 0 0 0 .75-.75V9.5a.25.25 0 0 1 .25-.25H13a.25.25 0 0 1 .25.25v1a.75.75 0 0 0 1.5 0v-1.25a.75.75 0 0 0-.75-.75zM6.354 11.354a.5.5 0 0 0-.708-.708L4.793 9.793l-.646.647a.5.5 0 1 0 .708.708l1-1a.5.5 0 0 0 0-.708l-1-1a.5.5 0 0 0-.708.708l.646.646L4.793 8.207l.647-.647a.5.5 0 0 0-.708-.708l-1 1a.5.5 0 0 0 0 .708l1 1z"/>
                </svg>
              </div>
              <h3 class="font-weight-bold mt-3 mb-3">2. PILIH LAPANGAN & SESI</h3>
              <p class="text-muted px-3">Jelajahi pilihan lapangan, lihat detail fasilitas, pilih tanggal dan sesi waktu yang Anda inginkan.</p>
              <p><a class="btn btn-success mt-2 font-weight-bold" href="booking.php" role="button">Pilih Lapangan &raquo;</a></p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
           <div class="step-item">
             <div class="step-icon-wrapper step-icon-bayar">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-credit-card-2-front-fill" viewBox="0 0 16 16">
                  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-2zm0 3a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1z"/>
                </svg>
              </div>
              <h3 class="font-weight-bold mt-3 mb-3">3. KONFIRMASI & BAYAR</h3>
              <p class="text-muted px-3">Selesaikan pemesanan Anda dengan melakukan konfirmasi dan pembayaran secara aman.</p>
            </div>
        </div>
      </div>
      <hr class="mt-4">
    </div>
</div> 
</main>
</div>

<footer class="footer">
    <div class="container">
        <span>&copy; <?php echo date("Y"); ?> Penyewaan Olahraga. Semua Hak Dilindungi.</span>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId.length > 1 && document.querySelector(targetId)) { 
                 document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
</body>
</html>