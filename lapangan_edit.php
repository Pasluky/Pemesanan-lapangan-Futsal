<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "admin") {
    header("location:login.php");
    exit;
}
$adminFullName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator';

$lapangan_id = null;
$lapangan_data = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $lapangan_id = mysqli_real_escape_string($db, $_GET['id']);
    $query_get_lapangan = "SELECT ID, Nama, Tipe, Jenis, Harga, status, foto FROM lapangan WHERE ID = '$lapangan_id'";
    $result_get_lapangan = mysqli_query($db, $query_get_lapangan);

    if ($result_get_lapangan && mysqli_num_rows($result_get_lapangan) == 1) {
        $lapangan_data = mysqli_fetch_assoc($result_get_lapangan);
    } else {
        $_SESSION['error_message'] = "Data lapangan tidak ditemukan.";
        header("location: datalapangan.php");
        exit;
    }
} else {
    $_SESSION['error_message'] = "ID lapangan tidak valid untuk diedit.";
    header("location: datalapangan.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Lapangan: <?php echo htmlspecialchars($lapangan_data['Nama']); ?></title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { font-weight: bold; }
        .form-container { 
            max-width: 750px; 
            margin: 2rem auto; 
            background-color: #fff; 
            padding: 2.5rem; 
            border-radius: .75rem; 
            box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.08);
        }
        .form-container h3 { 
            margin-bottom: 1.5rem; 
            text-align:center; 
            color:#343a40; 
            font-weight:bold; 
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.75rem;
            display: inline-block;
        }
        .current-foto-thumb {
            max-width: 200px;
            max-height: 150px;
            margin-top: 10px;
            border-radius: .25rem;
            border: 1px solid #ddd;
        }
        .custom-file-label::after { content: "Pilih berkas"; }
        .form-container .btn-block { padding: .75rem; font-size: 1.05rem;}
        .form-container .btn-outline-secondary { border-color: #6c757d; color: #6c757d;}
        .form-container .btn-outline-secondary:hover { background-color: #6c757d; color: #fff;}
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
        <li class="nav-item active"><a class="nav-link" href="datalapangan.php">Data Lapangan <span class="sr-only">(current)</span></a></li>
        <li class="nav-item"><a class="nav-link" href="admin_booking_list.php">Kelola Booking</a></li>
        <li class="nav-item"><a class="nav-link" href="laporan_keuangan.php">Laporan Keuangan</a></li>
        <li class="nav-item"><a class="nav-link" href="peralatan.php">Item Tambahan</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_user_list.php">Manajemen Pengguna</a></li>
      </ul>
      <span class="navbar-text mr-3 text-light">Halo, <?php echo $adminFullName; ?>!</span>
      <a class="btn btn-outline-danger my-2 my-sm-0" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="form-container">
        <div class="text-center">
             <h3>Edit Data Lapangan</h3>
        </div>

        <?php
        if (isset($_SESSION['form_error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' .
                 htmlspecialchars($_SESSION['form_error_message']) .
                 '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            unset($_SESSION['form_error_message']);
        }
        ?>

        <form action="lapangan_proses.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id_lapangan" value="<?php echo htmlspecialchars($lapangan_data['ID']); ?>">
            <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($lapangan_data['foto']); ?>">
            
            <div class="form-group">
                <label for="nama_lapangan">Nama Lapangan</label>
                <input type="text" class="form-control" id="nama_lapangan" name="nama_lapangan" value="<?php echo htmlspecialchars($lapangan_data['Nama']); ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="tipe_lapangan">Tipe Lapangan</label>
                    <select class="custom-select" id="tipe_lapangan" name="tipe_lapangan" required>
                        <option value="Indoor" <?php echo ($lapangan_data['Tipe'] == 'Indoor') ? 'selected' : ''; ?>>Indoor</option>
                        <option value="Outdoor" <?php echo ($lapangan_data['Tipe'] == 'Outdoor') ? 'selected' : ''; ?>>Outdoor</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="jenis_lapangan">Jenis Permukaan</label>
                    <select class="custom-select" id="jenis_lapangan" name="jenis_lapangan" required>
                        <option value="Reguler" <?php echo ($lapangan_data['Jenis'] == 'Reguler') ? 'selected' : ''; ?>>Reguler (Semen/Beton)</option>
                        <option value="Matras" <?php echo ($lapangan_data['Jenis'] == 'Matras') ? 'selected' : ''; ?>>Matras Interlock</option>
                        <option value="Rumput" <?php echo ($lapangan_data['Jenis'] == 'Rumput') ? 'selected' : ''; ?>>Rumput Sintetis</option>
                        <option value="Vinyl" <?php echo ($lapangan_data['Jenis'] == 'Vinyl') ? 'selected' : ''; ?>>Vinyl</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="harga_lapangan">Harga per Sesi (2 Jam) (Rp)</label>
                <input type="number" class="form-control" id="harga_lapangan" name="harga_lapangan" value="<?php echo htmlspecialchars($lapangan_data['Harga']); ?>" required min="0" step="1000">
            </div>

            <div class="form-group">
                <label for="status_lapangan">Status Lapangan</label>
                <select class="custom-select" id="status_lapangan" name="status_lapangan" required>
                    <option value="1" <?php echo ($lapangan_data['status'] == '1') ? 'selected' : ''; ?>>Aktif (Tersedia)</option>
                    <option value="0" <?php echo ($lapangan_data['status'] == '0') ? 'selected' : ''; ?>>Tidak Aktif / Perawatan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Foto Lapangan Saat Ini:</label><br>
                <?php if (!empty($lapangan_data['foto'])): ?>
                    <img src="./img/<?php echo htmlspecialchars($lapangan_data['foto']); ?>" alt="Foto Saat Ini" class="current-foto-thumb">
                    <p class="small text-muted mt-1">Nama file: <?php echo htmlspecialchars($lapangan_data['foto']); ?></p>
                <?php else: ?>
                    <p class="text-muted"><em>Tidak ada foto saat ini.</em></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="foto_lapangan">Ganti Foto Lapangan (Opsional)</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="foto_lapangan" name="foto_lapangan" accept="image/jpeg, image/png, image/gif">
                    <label class="custom-file-label" for="foto_lapangan" data-browse="Pilih">Pilih file gambar baru...</label>
                </div>
                <small class="form-text text-muted">Format: JPG, PNG, GIF. Ukuran maks: 2MB. Biarkan kosong jika tidak ingin mengganti foto.</small>
            </div>
            
            <hr class="my-4">
            <button type="submit" class="btn btn-primary btn-block">Update Data Lapangan</button>
            <a href="datalapangan.php" class="btn btn-outline-secondary btn-block mt-2">Batal & Kembali ke Daftar</a>
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
    document.addEventListener('DOMContentLoaded', function () {
        var customFileInput = document.getElementById('foto_lapangan');
        if (customFileInput) {
            customFileInput.addEventListener('change', function(e){
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file gambar baru...';
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        }

        window.setTimeout(function() {
            let alerts = document.querySelectorAll('.form-container .alert');
            alerts.forEach(function(alert) {
                $(alert).fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            });
        }, 5000);
    });
</script>
</body>
</html>