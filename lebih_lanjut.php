<?php
session_start();
if($_SESSION['Level'] != "user"){
    header("location:login.php");
}

// echo "Selamat datang User, " . $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyewaan Futsal - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.2.1.slim.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js" ></script>
<style type="text/css">
    .jumbotron{
      background-image: linear-gradient(rgba(0, 0, 0, 0.527),rgba(0, 0, 0, 0.5)), url("./img/Garasi-Futsal.jpg");
      background-size: cover;
      background-position: center; /* Tambahkan ini agar gambar jumbotron lebih baik */
      color: white;
    }
    .action-section {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f8f9fa; /* Warna latar yang lembut untuk section */
    }
    .action-card {
        border: none; /* Hilangkan border default card */
        transition: transform .2s ease-in-out, box-shadow .2s ease-in-out; /* Animasi halus */
        height: 100%; /* Membuat semua card sama tinggi dalam satu baris */
        display: flex; /* Untuk flexbox alignment */
        flex-direction: column; /* Konten card secara vertikal */
    }
    .action-card:hover {
        transform: translateY(-5px); /* Efek mengangkat sedikit saat hover */
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .action-card .card-body {
        flex-grow: 1; /* Membuat card-body mengisi ruang yang tersedia */
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Menyebar konten di card-body */
    }
    .action-card .icon {
        font-size: 3rem; /* Ukuran ikon */
        margin-bottom: 1rem;
        color: #007bff; /* Warna ikon utama, bisa disesuaikan per card */
    }
    .action-card .btn {
        margin-top: auto; /* Mendorong tombol ke bawah card */
    }
    /* Warna ikon spesifik per kartu */
    .icon-futsal { color: #28a745; } /* Hijau untuk futsal */
    .icon-status { color: #ffc107; } /* Kuning untuk status */
    .icon-equipment { color: #17a2b8; } /* Biru muda untuk peralatan */

    .how-to-section h1 {
        margin-bottom: 30px;
    }
  </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm"> <div class="container"> <a class="navbar-brand font-weight-bold" href="user_home.php">
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
          <a class="nav-link" href="booking.php">Booking</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="contact_us.php">Hubungi Kami</a> </li>
      </ul>
      <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo htmlspecialchars($username); ?>
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
<main role="main">
<div class="jumbotron" style="padding-top: 8rem; padding-bottom: 4rem;"> <div class="container text-center"> <h1 class="display-3 font-weight-bold">Sewa Lapangan Lebih Mudah!</h1>
    <p class="lead col-md-8 mx-auto">Platform kami mudahkan Anda sewa lapangan olahraga pilihan. Cari jadwal tersedia, pesan online praktis, lalu dapatkan konfirmasi booking instan. Mulai sekarang, nikmati beragam pilihan lapangan berkualitas terbaik kami!</p>
  </div>
</div>

<div class="container action-section" id="pilihan-lanjutan">
    <div class="row text-center mb-4">
        <div class="col-12">
            <h2 class="font-weight-bold">Apa yang Ingin Anda Lakukan?</h2>
            <p class="lead text-muted">Pilih salah satu opsi di bawah ini untuk melanjutkan.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card action-card shadow-sm">
                <div class="card-body text-center">
                    <div>
                        <div class="icon icon-futsal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-dribbble" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
                                <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
                            </svg>
                        </div>
                        <h4 class="card-title font-weight-bold">Sewa Lapangan Futsal</h4>
                        <p class="card-text text-muted">Temukan dan pesan lapangan futsal favorit Anda dengan mudah dan cepat.</p>
                    </div>
                    <a class="btn btn-success btn-block" href="booking.php" role="button">Pesan Lapangan &raquo;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card action-card shadow-sm">
                <div class="card-body text-center">
                    <div>
                        <div class="icon icon-status">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-calendar-check" viewBox="0 0 16 16">
                                <path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                        </div>
                        <h4 class="card-title font-weight-bold">Cek Status Booking</h4>
                        <p class="card-text text-muted">Pantau status reservasi lapangan Anda kapan saja, di mana saja.</p>
                    </div>
                    <a class="btn btn-warning btn-block text-dark" href="history.php" role="button">Lihat Status &raquo;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card action-card shadow-sm">
                <div class="card-body text-center">
                    <div>
                        <div class="icon icon-equipment">
                             <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-smartwatch" viewBox="0 0 16 16">
                                <path d="M9 5a.5.5 0 0 0-1 0v3H6a.5.5 0 0 0 0 1h2.5a.5.5 0 0 0 .5-.5V5z"/>
                                <path d="M4 1.667v.383A2.5 2.5 0 0 0 2 4.5v7a2.5 2.5 0 0 0 2 2.45v.383C4 15.253 4.746 16 5.667 16h4.666c.92 0 1.667-.746 1.667-1.667v-.383a2.5 2.5 0 0 0 2-2.45V4.5A2.5 2.5 0 0 0 12 1.667v-.383C12 .747 11.254 0 10.333 0H5.667C4.746 0 4 .746 4 1.667zM4.5 3h7A1.5 1.5 0 0 1 13 4.5v7a1.5 1.5 0 0 1-1.5 1.5h-7A1.5 1.5 0 0 1 3 11.5V4.5A1.5 1.5 0 0 1 4.5 3z"/>
                            </svg>
                        </div>
                        <h4 class="card-title font-weight-bold">Sewa Peralatan</h4>
                        <p class="card-text text-muted">Lengkapi permainan Anda dengan menyewa peralatan berkualitas dari kami.</p>
                    </div>
                    <a class="btn btn-info btn-block" href="peralatan_user.php" role="button">Sewa Peralatan &raquo;</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container how-to-section mt-4"> <hr class="mb-5">
  <div class="row">
    <div class="col-12">
        <h1 class="text-center font-weight-bold">Bagaimana Cara Menyewa dari Kami?</h1>
        <p class="text-center text-muted lead mb-5">Berikut adalah langkah-langkah mudah untuk memulai.</p>
    </div>
    <div class="col-md-4 text-center mb-4">
      <div class="mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-plus-fill text-primary" viewBox="0 0 16 16">
          <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
          <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H12V5.5a.5.5 0 0 1 .5-.5z"/>
        </svg>
      </div>
      <h3 class="font-weight-bold mb-3">DAFTAR / MASUK</h3>
      <p class="text-muted">Buat akun baru atau masuk jika sudah memiliki akun untuk memulai proses penyewaan lapangan futsal.</p>
      </div>
    <div class="col-md-4 text-center mb-4">
       <div class="mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-calendar2-check-fill text-success" viewBox="0 0 16 16">
          <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zm9.5 8.5H9.75a.75.75 0 0 0 0 1.5h2.5a.75.75 0 0 0 0-1.5h-1.5a.25.25 0 0 1-.25-.25V7.5a.75.75 0 0 0-1.5 0v1a.25.25 0 0 1-.25.25h-1.5a.75.75 0 0 0 0 1.5h1.25a.75.75 0 0 0 .75-.75V9.5a.25.25 0 0 1 .25-.25H13a.25.25 0 0 1 .25.25v1a.75.75 0 0 0 1.5 0v-1.25a.75.75 0 0 0-.75-.75zM6.354 11.354a.5.5 0 0 0-.708-.708L4.793 9.793l-.646.647a.5.5 0 1 0 .708.708l1-1a.5.5 0 0 0 0-.708l-1-1a.5.5 0 0 0-.708.708l.646.646L4.793 8.207l.647-.647a.5.5 0 0 0-.708-.708l-1 1a.5.5 0 0 0 0 .708l1 1z"/>
        </svg>
      </div>
      <h3 class="font-weight-bold mb-3">PILIH LAPANGAN & JADWAL</h3>
      <p class="text-muted">Jelajahi berbagai pilihan lapangan, lihat detail fasilitas, dan pilih jadwal yang sesuai dengan keinginan Anda.</p>
      <p><a class="btn btn-success" href="booking.php" role="button">Lihat Lapangan &raquo;</a></p>
    </div>
    <div class="col-md-4 text-center mb-4">
       <div class="mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-credit-card-2-front-fill text-warning" viewBox="0 0 16 16">
          <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-2zm0 3a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1z"/>
        </svg>
      </div>
      <h3 class="font-weight-bold mb-3">KONFIRMASI & BAYAR</h3>
      <p class="text-muted">Selesaikan pemesanan Anda dengan melakukan konfirmasi dan pembayaran secara aman dan mudah.</p>
      </div>
  </div>
  <hr class="mt-5">
</div> </main>

<footer class="text-center py-4 bg-light"> <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> ArenaKita. Semua Hak Dilindungi.</p>
        <p class="mb-0"><a href="#">Kebijakan Privasi</a> | <a href="#">Syarat & Ketentuan</a></p>
    </div>
</footer>

<script>
    // Script untuk smooth scroll jika "Lebih lanjut" diklik
    document.querySelector('a[href="#pilihan-lanjutan"]').addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
</script>
</body>
</html>