<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "admin") {
    header("location:login.php");
    exit;
}
$adminFullName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tambah Item Tambahan</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { font-weight: bold; }
        .form-container { 
            max-width: 600px; 
            margin: 2rem auto; 
            background-color: #fff; 
            padding: 2.5rem; 
            border-radius: .75rem; 
            box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.08);
        }
        .form-container h3 { 
            margin-bottom: 1.5rem; 
            text-align:center; 
            color:#fd7e14; 
            font-weight:bold; 
            border-bottom: 2px solid #fd7e14;
            padding-bottom: 0.75rem;
            display: inline-block;
        }
         .form-container .btn-block { padding: .75rem; font-size: 1.05rem;}
         .form-container .btn-outline-secondary { border-color: #6c757d; color: #6c757d;}
         .form-container .btn-outline-secondary:hover { background-color: #6c757d; color: #fff;}
         .alert-container { max-width: 600px; margin: 1rem auto 0 auto; }

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand-custom" href="admin_home.php">Admin Panel Penyewaan</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item"><a class="nav-link" href="admin_home.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="datalapangan.php">Data Lapangan</a></li>
        <li class="nav-item"><a class="nav-link" href="laporan_keuangan.php">Laporan Keuangan</a></li>
        <li class="nav-item active"><a class="nav-link" href="peralatan.php">Peralatan <span class="sr-only">(current)</span></a></li>
        <li class="nav-item"><a class="nav-link" href="riwayat_booking.php">Riwayat Booking</a></li>
      </ul>
      <a class="btn btn-outline-danger my-2 my-sm-0" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="form-container">
        <div class="text-center">
             <h3>Tambah Item Tambahan Baru</h3>
        </div>

        <div class="alert-container">
            <?php
            if (isset($_SESSION['form_error_message_tambahan'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' .
                     htmlspecialchars($_SESSION['form_error_message_tambahan']) .
                     '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                unset($_SESSION['form_error_message_tambahan']);
            }
            ?>
        </div>

        <form action="tambahan_proses.php" method="POST">
            <input type="hidden" name="aksi" value="tambah">
            <div class="form-group">
                <label for="nama_item">Nama Item Tambahan</label>
                <input type="text" class="form-control" id="nama_item" name="nama_item" placeholder="Contoh: Sepatu Futsal Berkualitas" required>
            </div>
            <div class="form-group">
                <label for="harga_item">Harga Sewa (Rp)</label>
                <input type="number" class="form-control" id="harga_item" name="harga_item" placeholder="Contoh: 50000" required min="0" step="1000">
            </div>
            <hr class="my-4">
            <button type="submit" class="btn btn-primary btn-block">Simpan Item Tambahan</button>
            <a href="peralatan.php" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
        </form>
    </div>
</div>

<footer class="text-center py-4 bg-dark text-light mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Admin Panel Penyewaan Olahraga</p>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script>
    window.setTimeout(function() {
        let alerts = document.querySelectorAll('.alert-container .alert');
        alerts.forEach(function(alert) {
            $(alert).fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        });
    }, 5000);
</script>
</body>
</html>