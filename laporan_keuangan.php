<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "admin") {
    header("location:login.php");
    exit;
}
$adminFullName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator';

function format_rupiah_laporan($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Modifikasi fungsi ini untuk hanya menggunakan status pesanan
function get_payment_status_from_booking_status($status_pesanan_db) {
    if ($status_pesanan_db == 0) return ['text' => 'Lunas', 'badge' => 'success']; 
    if ($status_pesanan_db == 1) return ['text' => 'Belum Lunas/Proses', 'badge' => 'warning'];
    if ($status_pesanan_db == 2) return ['text' => 'Dibatalkan/Refund', 'badge' => 'danger'];
    return ['text' => 'N/A', 'badge' => 'secondary'];
}


$filter_conditions = "WHERE p.status = 0"; 
$filter_values = [
    'tanggal_mulai' => '',
    'tanggal_akhir' => '',
    'lapangan' => ''
    // Tidak ada filter status pembayaran lagi di sini karena kita infer dari status pesanan
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_filter_laporan'])) {
    $filter_conditions = "WHERE 1"; // Reset filter dasar jika ada input filter

    if (!empty($_POST['tanggal_mulai'])) {
        $filter_values['tanggal_mulai'] = mysqli_real_escape_string($db, $_POST['tanggal_mulai']);
        $filter_conditions .= " AND p.Tanggal >= '{$filter_values['tanggal_mulai']}'";
    }
    if (!empty($_POST['tanggal_akhir'])) {
        $filter_values['tanggal_akhir'] = mysqli_real_escape_string($db, $_POST['tanggal_akhir']);
        $filter_conditions .= " AND p.Tanggal <= '{$filter_values['tanggal_akhir']}'";
    }
    if (!empty($_POST['filter_lapangan'])) {
        $filter_values['lapangan'] = mysqli_real_escape_string($db, $_POST['filter_lapangan']);
        $filter_conditions .= " AND p.ID_Lapangan = '{$filter_values['lapangan']}'";
    }
    // Jika Anda ingin tetap memfilter berdasarkan status pesanan untuk laporan keuangan:
    if (isset($_POST['filter_status_pesanan']) && $_POST['filter_status_pesanan'] !== "") {
         $filter_values['status_pesanan'] = mysqli_real_escape_string($db, $_POST['filter_status_pesanan']);
         $filter_conditions .= " AND p.status = '{$filter_values['status_pesanan']}'";
    } else {
        // Default hanya tampilkan yang selesai jika tidak ada filter status spesifik
         $filter_conditions .= " AND p.status = 0";
    }
}


$query_lapangan_options = "SELECT ID, Nama FROM lapangan ORDER BY Nama ASC";
$result_lapangan_options = mysqli_query($db, $query_lapangan_options);


$bulan_ini_awal = date('Y-m-01');
$bulan_ini_akhir = date('Y-m-t');

$query_total_pendapatan_bulan = "SELECT SUM(total_harga) AS total FROM pesanan WHERE status = 0 AND Tanggal BETWEEN '$bulan_ini_awal' AND '$bulan_ini_akhir'";
$res_total_pendapatan_bulan = mysqli_query($db, $query_total_pendapatan_bulan);
$data_total_pendapatan_bulan = mysqli_fetch_assoc($res_total_pendapatan_bulan);
$total_pendapatan_bulan_ini = $data_total_pendapatan_bulan['total'] ?? 0;

$query_transaksi_bulan = "SELECT COUNT(ID) AS total FROM pesanan WHERE status = 0 AND Tanggal BETWEEN '$bulan_ini_awal' AND '$bulan_ini_akhir'";
$res_transaksi_bulan = mysqli_query($db, $query_transaksi_bulan);
$data_transaksi_bulan = mysqli_fetch_assoc($res_transaksi_bulan);
$jumlah_transaksi_bulan_ini = $data_transaksi_bulan['total'] ?? 0;

$query_total_pendapatan_all = "SELECT SUM(total_harga) AS total FROM pesanan WHERE status = 0";
$res_total_pendapatan_all = mysqli_query($db, $query_total_pendapatan_all);
$data_total_pendapatan_all = mysqli_fetch_assoc($res_total_pendapatan_all);
$total_pendapatan_keseluruhan = $data_total_pendapatan_all['total'] ?? 0;

// Menghapus p.Status_Pembayaran dari SELECT list
$query_transaksi = "SELECT p.ID AS IDTransaksi, p.Tanggal, p.Jam, u.Nama AS NamaPenyewa, 
                           l.Nama AS NamaLapangan, p.durasi, p.total_harga, p.status AS StatusPesananDB 
                    FROM pesanan p
                    LEFT JOIN users u ON p.id_user = u.ID
                    LEFT JOIN lapangan l ON p.ID_Lapangan = l.ID
                    $filter_conditions 
                    ORDER BY p.Tanggal DESC, p.Jam DESC";
$result_transaksi = mysqli_query($db, $query_transaksi);

if (!$result_transaksi) {
    die("Error mengambil data transaksi: " . mysqli_error($db));
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Laporan Keuangan</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { font-weight: bold; }
        .page-header { background-color: #28a745; color: #fff; padding: 25px 20px; border-radius: .25rem; margin-bottom: 30px; text-align: center; }
        .page-header h1 { margin-bottom: 0.5rem; }
        .page-header p { font-size: 1.1rem; color: #e9ecef; }
        .summary-card { text-align: center; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); margin-bottom:1.5rem; }
        .summary-card .card-body { padding: 1.5rem; }
        .summary-card .card-title { font-size: 1rem; color: #6c757d; margin-bottom: 0.5rem; }
        .summary-card .display-5 { font-size: 2rem; font-weight: bold; color: #28a745; }
        .filter-card { background-color: #fff; padding: 1.5rem; border-radius: .3rem; margin-bottom: 2rem; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); }
        .filter-card .form-group { margin-bottom: 1rem; } /* Penyesuaian margin */
        .table th { background-color: #e9ecef; font-weight:bold; }
        .table td, .table th { vertical-align: middle; }
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
        <li class="nav-item active"><a class="nav-link" href="laporan_keuangan.php">Laporan Keuangan<span class="sr-only">(current)</span></a></li>
        <li class="nav-item"><a class="nav-link" href="peralatan.php">Peralatan</a></li>
        <li class="nav-item"><a class="nav-link" href="riwayat_booking.php">Riwayat Booking</a></li>
      </ul>
      <a class="btn btn-outline-danger my-2 my-sm-0" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
    </div>
  </div>
</nav>

<main role="main" class="container mt-4 mb-5">
    <div class="page-header">
        <h1>Laporan Keuangan</h1>
        <p class="lead">Pantau semua transaksi & nilai total pendapatan dari penyewaan lapangan.</p>
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

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="card-title">Pendapatan Selesai (Bulan Ini)</h6>
                    <p class="display-5"><?php echo format_rupiah_laporan($total_pendapatan_bulan_ini); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="card-title">Booking Selesai (Bulan Ini)</h6>
                    <p class="display-5"><?php echo $jumlah_transaksi_bulan_ini; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
             <div class="card summary-card">
                <div class="card-body">
                    <h6 class="card-title">Total Pendapatan Selesai (Semua)</h6>
                    <p class="display-5"><?php echo format_rupiah_laporan($total_pendapatan_keseluruhan); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4 filter-card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filter Laporan Transaksi</h5>
        </div>
        <div class="card-body">
            <form action="laporan_keuangan.php" method="POST" id="filter-laporan-form">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="tanggal-mulai">Tgl Main Mulai</label>
                        <input type="date" class="form-control form-control-sm" id="tanggal-mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($filter_values['tanggal_mulai']); ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="tanggal-akhir">Tgl Main Akhir</label>
                        <input type="date" class="form-control form-control-sm" id="tanggal-akhir" name="tanggal_akhir" value="<?php echo htmlspecialchars($filter_values['tanggal_akhir']); ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="filter-lapangan">Lapangan</label>
                        <select class="form-control form-control-sm" id="filter-lapangan" name="filter_lapangan">
                            <option value="">Semua Lapangan</option>
                            <?php 
                            if($result_lapangan_options && mysqli_num_rows($result_lapangan_options) > 0){
                                mysqli_data_seek($result_lapangan_options, 0); // Reset pointer
                                while($lap_opt = mysqli_fetch_assoc($result_lapangan_options)){
                                    $selected = ($filter_values['lapangan'] == $lap_opt['ID']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($lap_opt['ID'])."' $selected>".htmlspecialchars($lap_opt['Nama'])."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                     <div class="col-md-3 form-group">
                        <label for="filter-status-pesanan">Status Pesanan</label>
                        <select class="form-control form-control-sm" id="filter-status-pesanan" name="filter_status_pesanan">
                            <option value="">Semua Status Pesanan</option>
                            <option value="0" <?php echo (isset($filter_values['status_pesanan']) && $filter_values['status_pesanan'] === "0") ? 'selected' : ''; ?>>Selesai</option>
                            <option value="1" <?php echo (isset($filter_values['status_pesanan']) && $filter_values['status_pesanan'] === "1") ? 'selected' : ''; ?>>Berlangsung</option>
                            <option value="2" <?php echo (isset($filter_values['status_pesanan']) && $filter_values['status_pesanan'] === "2") ? 'selected' : ''; ?>>Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right mt-2">
                        <button type="submit" name="apply_filter_laporan" class="btn btn-primary btn-sm">Terapkan Filter</button>
                        <?php if (!empty($filter_values['tanggal_mulai']) || !empty($filter_values['tanggal_akhir']) || !empty($filter_values['lapangan']) || (isset($filter_values['status_pesanan']) && $filter_values['status_pesanan'] !== '')): ?>
                            <a href="laporan_keuangan.php" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Transaksi Keuangan (<?php echo mysqli_num_rows($result_transaksi); ?> ditemukan)</h5>
            <button class="btn btn-outline-success btn-sm" id="export-laporan" onclick="alert('Fitur export laporan belum diimplementasikan.');">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download mr-1" viewBox="0 0 16 16">
                  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                  <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                Export Laporan
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">ID Pesanan</th>
                            <th scope="col">Tgl. Main</th>
                            <th scope="col">Jam Main</th>
                            <th scope="col">Penyewa</th>
                            <th scope="col">Lapangan</th>
                            <th scope="col" class="text-center">Durasi</th>
                            <th scope="col" class="text-right">Total Bayar</th>
                            <th scope="col" class="text-center">Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-transaksi-body">
                        <?php if (mysqli_num_rows($result_transaksi) > 0) : ?>
                            <?php while ($trx = mysqli_fetch_assoc($result_transaksi)) : 
                                $status_pembayaran_info = get_payment_status_from_booking_status($trx['StatusPesananDB']);
                            ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($trx['IDTransaksi']); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y', strtotime($trx['Tanggal']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($trx['Jam']))); ?> WIB</td>
                                <td><?php echo htmlspecialchars($trx['NamaPenyewa'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($trx['NamaLapangan'] ?? 'N/A'); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($trx['durasi']); ?> Jam</td>
                                <td class="text-right"><?php echo format_rupiah_laporan($trx['total_harga']); ?></td>
                                <td class="text-center"><span class="badge badge-<?php echo $status_pembayaran_info['badge']; ?>"><?php echo htmlspecialchars($status_pembayaran_info['text']); ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted p-4">Tidak ada data transaksi keuangan yang sesuai dengan filter.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (mysqli_num_rows($result_transaksi) > 0): ?>
        <div class="card-footer bg-light">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">Selanjutnya</a></li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</main>

<footer class="text-center py-4 bg-light border-top mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Admin Panel Penyewaan Olahraga. Semua Hak Dilindungi.</p>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 5000);
</script>
</body>
</html>