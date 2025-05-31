<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "admin") {
    header("location:login.php");
    exit;
}

$query_tambahan = "SELECT ID, Nama, Harga FROM tambahan ORDER BY ID ASC";
$result_tambahan = mysqli_query($db, $query_tambahan);

if (!$result_tambahan) {
    die("Error mengambil data item tambahan: " . mysqli_error($db));
}

function format_rupiah_tambahan($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pengelolaan Item Tambahan</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px; 
        }
        .page-header {
            background-color: #fd7e14; 
            color: #fff;
            padding: 25px 20px;
            border-radius: .25rem;
            margin-bottom: 30px;
            text-align: center;
        }
        .page-header h1 { margin-bottom: 0.5rem; }
        .page-header p { font-size: 1.1rem; color: #f8f9fa; }
        .navbar-brand-custom { font-weight: bold; }
        .table th { background-color: #e9ecef; font-weight: bold; }
        .action-buttons .btn { margin-right: 5px; margin-bottom: 5px;}
        .alert-container { position: fixed; top: 80px; right: 20px; z-index: 1050; width: auto; max-width: 400px;}
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

<main role="main" class="container mt-4 mb-5">
    <div class="page-header">
        <h1>Pengelolaan Peralatan</h1>
        <p class="lead">Kelola daftar peralatan yang dapat disewa beserta harganya.</p>
    </div>

    <div class="alert-container">
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
                 htmlspecialchars($_SESSION['success_message']) .
                 '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' .
                 htmlspecialchars($_SESSION['error_message']) .
                 '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            unset($_SESSION['error_message']);
        }
        ?>
    </div>

    <div class="mb-3 text-right">
        <a href="tambahan_tambah.php" class="btn btn-success btn-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle-fill mr-2" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
            </svg>
            Tambah Item Baru
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h4 class="mb-0">Daftar Item Tambahan</h4>
        </div>
        <div class="card-body p-0">
            <?php if (mysqli_num_rows($result_tambahan) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" style="width: 10%;">ID</th>
                            <th scope="col" style="width: 40%;">Nama Item</th>
                            <th scope="col" style="width: 25%;" class="text-right">Harga</th>
                            <th scope="col" style="width: 25%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = mysqli_fetch_assoc($result_tambahan)) : ?>
                        <tr>
                            <th scope="row"><?php echo htmlspecialchars($item['ID']); ?></th>
                            <td><?php echo htmlspecialchars($item['Nama']); ?></td>
                            <td class="text-right"><?php echo format_rupiah_tambahan($item['Harga']); ?></td>
                            <td class="text-center action-buttons">
                                <a href="tambahan_edit.php?id=<?php echo $item['ID']; ?>" class="btn btn-warning btn-sm" title="Edit Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16"><path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/></svg> Edit
                                </a>
                                <a href="tambahan_proses.php?aksi=hapus&id=<?php echo $item['ID']; ?>" class="btn btn-danger btn-sm" title="Hapus Item" onclick="return confirm('Apakah Anda yakin ingin menghapus item \'<?php echo htmlspecialchars(addslashes($item['Nama'])); ?>\'?');">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16"><path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/></svg> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-info text-center m-3">
                    Belum ada data item tambahan yang terdaftar. Silakan <a href="tambahan_tambah.php">tambahkan item baru</a>.
                </div>
            <?php endif; ?>
        </div>
        <?php if (mysqli_num_rows($result_tambahan) > 0) : ?>
        <div class="card-footer bg-light text-muted">
             Menampilkan <?php echo mysqli_num_rows($result_tambahan); ?> data item tambahan.
        </div>
        <?php endif; ?>
    </div>
</main>

<footer class="text-center mt-5 mb-4">
    <p>&copy; <?php echo date("Y"); ?> Admin Panel Penyewaan Olahraga. Semua Hak Dilindungi.</p>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 4000);
</script>
</body>
</html>