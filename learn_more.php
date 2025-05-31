<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyewaan Lapangan Futsal</title>
    
    <link rel="stylesheet" href="./css/bootstrap.min.css">

  <style type="text/css">
    body {
        padding-top: 56px; 
        background-color: #f8f9fa; 
    }
    .jumbotron{
      background-image: linear-gradient(rgba(0, 0, 0, 0.527),rgba(0, 0, 0, 0.5)), url("./img/Garasi-Futsal.jpg");
      background-size: cover;
      background-position: center;
      color: white;
      border-radius: 0; 
      margin-bottom: 0; 
      padding-top: 5rem; 
      padding-bottom: 5rem; 
    }
    /* Gaya untuk Pilihan Lanjutan - Mirip Screenshot (838).png */
    .pilihan-lanjutan-section {
        padding: 3rem 0;
        background-color: #f8f9fa; 
    }
    .pilihan-lanjutan-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
        transition: all 0.3s ease-in-out;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .pilihan-lanjutan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .pilihan-lanjutan-card .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        text-align: center;
        padding: 2rem;
    }
    .pilihan-lanjutan-card .icon-wrapper {
        font-size: 3rem; 
        margin-bottom: 1rem;
    }
    .pilihan-lanjutan-card .card-title {
        font-weight: bold;
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
    }
    .pilihan-lanjutan-card .card-text {
        color: #6c757d;
        font-size: 0.95rem;
        flex-grow: 1; 
        margin-bottom: 1.5rem;
    }
    .pilihan-lanjutan-card .btn {
        padding: 0.5rem 1.5rem;
        font-weight: bold;
    }
    .icon-sewa-lapangan { color: #28a745; } 
    .btn-sewa-lapangan { background-color: #28a745; border-color: #28a745; color:white; }
    .btn-sewa-lapangan:hover { background-color: #218838; border-color: #1e7e34; }

    .icon-cek-status { color: #ffc107; } 
    .btn-cek-status { background-color: #ffc107; border-color: #ffc107; color:#212529; }
    .btn-cek-status:hover { background-color: #e0a800; border-color: #d39e00; }
    
    .icon-sewa-peralatan { color: #17a2b8; } 
    .btn-sewa-peralatan { background-color: #17a2b8; border-color: #17a2b8; color:white; }
    .btn-sewa-peralatan:hover { background-color: #138496; border-color: #117a8b; }

    /* Gaya untuk Bagaimana Cara Menyewa - Mirip Screenshot (838).png */
    .cara-menyewa-section {
        padding: 3rem 0;
        background-color: #ffffff; 
    }
    .cara-menyewa-section .section-title {
        font-weight: bold;
        margin-bottom: 1rem;
    }
     .cara-menyewa-section .lead-text { 
        margin-bottom: 3rem;
        font-size: 1.2rem;
        color: #6c757d;
    }
    .cara-menyewa-section .step-card {
        text-align: center;
        padding: 1.5rem;
    }
    .cara-menyewa-section .step-icon-wrapper {
        width: 90px; /* Perbesar sedikit lingkaran ikon */
        height: 90px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem auto; /* Margin bawah ditambah */
        font-size: 3rem; /* Ukuran ikon di dalam lingkaran */
    }
    .step-icon-daftar { background-color: #e6f2ff; color: #007bff; } 
    .step-icon-pilih { background-color: #e9f7ef; color: #28a745; } 
    .step-icon-bayar { background-color: #fff8e1; color: #ffc107; } 

    .cara-menyewa-section .step-card h3 { /* Diubah ke h3 dan ditebalkan */
        font-size: 1.25rem; /* Ukuran font judul langkah */
        font-weight: bold;
        margin-bottom: 0.75rem; /* Margin bawah judul langkah */
    }
    .cara-menyewa-section .step-card p {
        font-size: 0.95rem; /* Sedikit perbesar font deskripsi */
        color: #6c757d;
        min-height: 60px; /* Beri tinggi minimum agar sejajar jika teks berbeda panjang */
    }
    .cara-menyewa-section .step-card .btn { /* Styling untuk tombol di langkah menyewa */
        font-weight: bold;
    }
    .navbar-brand img {
        max-height: 24px; 
        margin-right: 5px;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item active">
          <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="field_catalog.php">Sewa Lapangan</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="about_us.php">Tentang Kami</a>
        </li>
      </ul>
      <a class="btn btn-success my-2 my-sm-0" href="login.php" role="button">Masuk / Daftar</a>
    </div>
  </div>
</nav>
<main role="main">

<div class="jumbotron">
  <div class="container text-center">
    <h1 class="display-4 font-weight-bold">Sewa Lapangan Lebih Mudah!</h1>
    <p class="lead col-lg-8 mx-auto">Platform kami mudahkan Anda sewa lapangan olahraga pilihan. Cari jadwal tersedia, pesan online praktis, lalu dapatkan konfirmasi booking instan. Mulai sekarang, nikmati beragam pilihan lapangan berkualitas terbaik kami!</p>
  </div>
</div>

<div class="pilihan-lanjutan-section" id="pilihan-lanjutan">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="font-weight-bold">Mulai Petualangan Olahraga Anda</h2>
                <p class="lead text-muted">Temukan semua yang Anda butuhkan di sini.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pilihan-lanjutan-card">
                    <div class="card-body">
                        <div class="icon-wrapper icon-sewa-lapangan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-dribbble" viewBox="0 0 16 16">
                                 <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
                                 <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
                            </svg>
                        </div>
                        <h5 class="card-title">Lihat Pilihan Lapangan</h5>
                        <p class="card-text">Jelajahi berbagai lapangan futsal dan olahraga lainnya yang tersedia di platform kami.</p>
                        <a class="btn btn-sewa-lapangan btn-block" href="field_catalog.php" role="button">Telusuri Lapangan &raquo;</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pilihan-lanjutan-card">
                    <div class="card-body">
                        <div class="icon-wrapper icon-cek-status">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
                                <path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                        </div>
                        <h5 class="card-title">Cek Status Booking</h5>
                        <p class="card-text">Sudah melakukan booking? Masuk untuk melihat status dan detail reservasi Anda.</p>
                        <a class="btn btn-cek-status btn-block" href="login.php?redirect=history.php" role="button">Masuk untuk Cek &raquo;</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pilihan-lanjutan-card">
                    <div class="card-body">
                        <div class="icon-wrapper icon-sewa-peralatan">
                             <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-smartwatch" viewBox="0 0 16 16">
                                <path d="M9 5a.5.5 0 0 0-1 0v3H6a.5.5 0 0 0 0 1h2.5a.5.5 0 0 0 .5-.5V5z"/>
                                <path d="M4 1.667v.383A2.5 2.5 0 0 0 2 4.5v7a2.5 2.5 0 0 0 2 2.45v.383C4 15.253 4.746 16 5.667 16h4.666c.92 0 1.667-.746 1.667-1.667v-.383a2.5 2.5 0 0 0 2-2.45V4.5A2.5 2.5 0 0 0 12 1.667v-.383C12 .747 11.254 0 10.333 0H5.667C4.746 0 4 .746 4 1.667zM4.5 3h7A1.5 1.5 0 0 1 13 4.5v7a1.5 1.5 0 0 1-1.5 1.5h-7A1.5 1.5 0 0 1 3 11.5V4.5A1.5 1.5 0 0 1 4.5 3z"/>
                            </svg>
                        </div>
                        <h5 class="card-title">Pilihan Peralatan Olahraga</h5>
                        <p class="card-text">Lihat berbagai peralatan pendukung berkualitas yang dapat Anda sewa.</p>
                        <a class="btn btn-sewa-peralatan btn-block" href="peralatan_user.php" role="button">Lihat Peralatan &raquo;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="cara-menyewa-section">
    <div class="container">
      <hr class="mb-5">
      <div class="row">
        <div class="col-12 text-center">
            <h1 class="section-title">Bagaimana Cara Menyewa dari Kami?</h1>
            <p class="lead-text">Berikut adalah langkah-langkah mudah untuk memulai.</p>
        </div>
        <div class="col-md-4 mb-4">
            <div class="step-card">
              <div class="step-icon-wrapper step-icon-daftar">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-plus-fill" viewBox="0 0 16 16"> <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                  <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H12V5.5a.5.5 0 0 1 .5-.5z"/>
                </svg>
              </div>
              <h3 class="font-weight-bold mb-3">1. MASUK / DAFTAR</h3> <p>Masuk jika sudah punya akun, atau daftar baru untuk mengakses semua fitur penyewaan kami.</p>
              <p><a class="btn btn-success mt-3 font-weight-bold" href="login.php" role="button">Masuk / Daftar &raquo;</a></p> </div>
        </div>
        <div class="col-md-4 mb-4">
           <div class="step-card">
             <div class="step-icon-wrapper step-icon-pilih">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-calendar2-check-fill" viewBox="0 0 16 16"> <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zm9.5 8.5H9.75a.75.75 0 0 0 0 1.5h2.5a.75.75 0 0 0 0-1.5h-1.5a.25.25 0 0 1-.25-.25V7.5a.75.75 0 0 0-1.5 0v1a.25.25 0 0 1-.25.25h-1.5a.75.75 0 0 0 0 1.5h1.25a.75.75 0 0 0 .75-.75V9.5a.25.25 0 0 1 .25-.25H13a.25.25 0 0 1 .25.25v1a.75.75 0 0 0 1.5 0v-1.25a.75.75 0 0 0-.75-.75zM6.354 11.354a.5.5 0 0 0-.708-.708L4.793 9.793l-.646.647a.5.5 0 1 0 .708.708l1-1a.5.5 0 0 0 0-.708l-1-1a.5.5 0 0 0-.708.708l.646.646L4.793 8.207l.647-.647a.5.5 0 0 0-.708-.708l-1 1a.5.5 0 0 0 0 .708l1 1z"/>
                </svg>
              </div>
              <h3 class="font-weight-bold mb-3">2. PILIH LAPANGAN & JADWAL</h3> <p>Jelajahi pilihan lapangan, lihat fasilitas, dan pilih jadwal yang paling sesuai untuk Anda.</p>
              </div>
        </div>
        <div class="col-md-4 mb-4">
           <div class="step-card">
             <div class="step-icon-wrapper step-icon-bayar">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-credit-card-2-front-fill" viewBox="0 0 16 16"> <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-2zm0 3a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm3 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1z"/>
                </svg>
              </div>
              <h3 class="font-weight-bold mb-3">3. KONFIRMASI & BAYAR</h3> <p>Setelah memilih, lakukan konfirmasi booking dan lanjutkan ke proses pembayaran yang aman.</p>
              </div>
        </div>
      </div>
      <hr class="mt-4">
    </div>
</div>

</main>

<footer class="text-center py-4 bg-white border-top">
    <div class="container">
        <p class="mb-1">&copy; <?php echo date("Y"); ?> ArenaKita. Platform Penyewaan Lapangan Olahraga Terpercaya.</p>
        <p class="mb-0">
            <a href="privacy_policy.php" class="text-muted">Kebijakan Privasi</a> | 
            <a href="terms_conditions.php" class="text-muted">Syarat & Ketentuan</a> |
            <a href="faq.php" class="text-muted">FAQ</a>
        </p>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js" ></script>
<script>
    // Script untuk smooth scroll jika "Lebih lanjut" diklik
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