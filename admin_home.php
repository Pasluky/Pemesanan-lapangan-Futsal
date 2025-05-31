<?php
session_start();
if ($_SESSION['Level'] != "admin") {
  header("location:login.php");
}

// echo "Selamat datang Admin, " . $_SESSION['username'];
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../../../favicon.ico">

  <title>Blog Template for Bootstrap</title>

  <link rel="stylesheet" href="./css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/bootstrap.css">
  <link href="blog.css" rel="stylesheet">
</head>

<>

  <div class="container">
    <header class="blog-header py-3">
      <div class="row flex-nowrap justify-content-between align-items-center mb-3">
        <div class="col-4 ">
          <a class="btn btn-outline-success" href="profil_admin.php">Profil</a>
        </div>
        <div class="col-4 text-center">
          <a class="blog-header-logo text-dark" href="./admin_home.php"> <?php echo "Admin | " . $_SESSION['username']; ?></a>
        </div>
        <div class="col-4 d-flex justify-content-end align-items-center">
          <a class="btn btn-outline-danger" href="logout.php" onclick="return confirm('Apakah anda ingin keluar')">Keluar</a>
        </div>
      </div>
      <div class="nav-scroller py-2 mb-1 border-bottom border-top">
        <nav class="nav d-flex justify-content-between">
          <a class="p-2 text-dark" href="datalapangan.php">Data Lapangan</a>
          <a class="p-2 text-dark" href="laporan_keuangan.php">Laporan Keuangan</a>
          <a class="p-2 text-dark" href="peralatan.php">Peralatan</a>
          <a class="p-2 text-dark" href="riwayat_booking.php">Riwayat Booking</a>
        </nav>
      </div>
    </header>

    <div class="jumbotron p-3 p-md-5 text-white rounded bg-dark">
      <div class="col-md-6 px-0">
        <h1 class="display-4 font-italic">Halaman Admin</h1>
        <p class="lead my-3">Dasbor Admin Penyewaan Futsal: Pusat kendali untuk mengelola lapangan, keuangan, reservasi, peralatan, dan riwayat booking Anda.</p>
      </div>
    </div>

    <div class="row mb-2">
      <div class="col-md-6">
        <div class="card flex-md-row mb-4 box-shadow h-md-250">
          <div class="card-body d-flex flex-column align-items-start">
            <strong class="d-inline-block mb-2 text-primary">Data</strong>
            <h3 class="mb-0">
              <a class="text-dark" href="datalapangan.php">Data Lapangan</a>
            </h3>
            <p class="card-text mb-auto">Kelola detail, harga, dan juga status booking seperti konfirmasi atau batal pada tiap lapangan yang tersedia.</p>
            <div class="d-flex justify-content-between mt-2">
              <a class="p-2 mr-2 btn btn-sm btn-outline-success" href="datalapangan.php">Buka Panel</a>
            </div>
          </div>
          <img class="card-img-right flex-auto d-none d-md-block" src="./img/data_lapangan.jpg" style="width: 30vh;" alt="Card image cap">
        </div>
      </div>
      <div class="col-md-6">
        <div class="card flex-md-row mb-4 box-shadow h-md-250">
          <div class="card-body d-flex flex-column align-items-start">
            <strong class="d-inline-block mb-2 text-success">Keuangan</strong>
            <h3 class="mb-0">
              <a class="text-dark" href="laporan_keuangan.php">Laporan Keuangan</a>
            </h3>
            <p class="card-text mb-auto">Pantau semua transaksi & total pendapatan dari seluruh penyewaan lapangan futsal.</p>
            <div class="d-flex justify-content-between mt-2">
              <a class="p-2 mr-2 btn btn-sm btn-outline-success" href="laporan_keuangan.php">Buka Panel</a>
            </div>
          </div>
          <img class="card-img-right flex-auto d-none d-md-block" src="./img/laporan_keuangan.jpg" style="width: 30vh;" alt="Card image cap">
        </div>
      </div>
      <div class="col-md-6">
        <div class="card flex-md-row mb-4 box-shadow h-md-250">
          <div class="card-body d-flex flex-column align-items-start">
            <strong class="d-inline-block mb-2 text-info">Peralatan</strong>
            <h3 class="mb-0">
              <a class="text-dark" href="peralatan.php">Persediaan Peralatan</a>
            </h3>
            <p class="card-text mb-auto"> Kelola stok serta ketersediaan semua peralatan pendukung yang dapat Anda sewakan.</p>
            <div class="d-flex justify-content-between mt-2">
              <a class="p-2 mr-2 btn btn-sm btn-outline-success" href="peralatan.php">Buka Panel</a>
            </div>
          </div>
          <img class="card-img-right flex-auto d-none d-md-block" src="./img/peralatan.jpg" style="width: 30vh;" alt="Card image cap">
        </div>
      </div>
      <div class="col-md-6">
        <div class="card flex-md-row mb-4 box-shadow h-md-250">
          <div class="card-body d-flex flex-column align-items-start">
            <strong class="d-inline-block mb-2 text-warning">Riwayat</strong>
            <h3 class="mb-0">
              <a class="text-dark" href="riwayat_booking.php">Riwayat Booking</a>
            </h3>
            <p class="card-text mb-auto">Tinjau semua arsip lengkap & analisis seluruh transaksi booking yang sudah lalu.</p>
            <div class="d-flex justify-content-between mt-2">
              <a class="p-2 mr-2 btn btn-sm btn-outline-success" href="riwayat_booking.php">Tambah</a>
            </div>
          </div>
          <img class="card-img-right flex-auto d-none d-md-block" src="./img/riwayat_booking.jpg" style="width: 30vh;" alt="Card image cap">
        </div>
      </div>
    </div>
  </div>

 
  <footer class="text-center mt-5 mb-4">
      <p>&copy; <?php echo date("Y"); ?> Admin Panel Penyewaan Olahraga. Semua Hak Dilindungi.</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script>
    window.jQuery || document.write('<script src="../../../../assets/js/vendor/jquery-slim.min.js"><\/script>')
  </script>
  <script src="../../../../assets/js/vendor/popper.min.js"></script>
  <script src="../../../../dist/js/bootstrap.min.js"></script>
  <script src="../../../../assets/js/vendor/holder.min.js"></script>
  <script>
    Holder.addTheme('thumb', {
      bg: '#55595c',
      fg: '#eceeef',
      text: 'Thumbnail'
    });
  </script>

</html>