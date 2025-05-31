<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$adminFullName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator';

$query_conditions = "WHERE 1";
$filter_values = [
    'tanggal_mulai' => '',
    'tanggal_akhir' => '',
    'lapangan' => '',
    'status_booking' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_filter'])) {
    if (!empty($_POST['tanggal_mulai'])) {
        $filter_values['tanggal_mulai'] = mysqli_real_escape_string($db, $_POST['tanggal_mulai']);
        $query_conditions .= " AND p.Tanggal >= '{$filter_values['tanggal_mulai']}'";
    }
    if (!empty($_POST['tanggal_akhir'])) {
        $filter_values['tanggal_akhir'] = mysqli_real_escape_string($db, $_POST['tanggal_akhir']);
        $query_conditions .= " AND p.Tanggal <= '{$filter_values['tanggal_akhir']}'";
    }
    if (!empty($_POST['filter_lapangan'])) {
        $filter_values['lapangan'] = mysqli_real_escape_string($db, $_POST['filter_lapangan']);
        $query_conditions .= " AND p.ID_Lapangan = '{$filter_values['lapangan']}'";
    }
    if (isset($_POST['filter_status']) && $_POST['filter_status'] !== "") { 
        $filter_values['status_booking'] = mysqli_real_escape_string($db, $_POST['filter_status']);
        $query_conditions .= " AND p.status = '{$filter_values['status_booking']}'";
    }
}

$query_lapangan_options = "SELECT ID, Nama FROM lapangan ORDER BY Nama ASC";
$result_lapangan_options = mysqli_query($db, $query_lapangan_options);

$query_history = "SELECT p.ID AS BookingID, 
                         p.Tanggal AS TanggalMain, 
                         p.Jam AS JamMain, 
                         p.durasi, 
                         p.total_harga, 
                         p.status AS StatusPesanan,
                         u.Nama AS NamaUser, 
                         l.Nama AS NamaLapangan
                  FROM pesanan p
                  LEFT JOIN users u ON p.id_user = u.ID
                  LEFT JOIN lapangan l ON p.ID_Lapangan = l.ID
                  $query_conditions
                  ORDER BY p.Tanggal DESC, p.Jam DESC, p.ID DESC";

$result_history = mysqli_query($db, $query_history);
if (!$result_history) {
    die("Error mengambil riwayat booking: " . mysqli_error($db));
}

$summary_query_selesai_condition = $query_conditions . " AND p.status = 0";
$query_total_selesai = "SELECT COUNT(p.ID) as total FROM pesanan p LEFT JOIN users u ON p.id_user = u.ID LEFT JOIN lapangan l ON p.ID_Lapangan = l.ID $summary_query_selesai_condition";
$result_total_selesai = mysqli_query($db, $query_total_selesai);
$data_total_selesai = mysqli_fetch_assoc($result_total_selesai);
$total_booking_selesai = $data_total_selesai['total'] ?? 0;

$summary_query_dibatalkan_condition = $query_conditions . " AND p.status = 2";
$query_total_dibatalkan = "SELECT COUNT(p.ID) as total FROM pesanan p LEFT JOIN users u ON p.id_user = u.ID LEFT JOIN lapangan l ON p.ID_Lapangan = l.ID $summary_query_dibatalkan_condition";
$result_total_dibatalkan = mysqli_query($db, $query_total_dibatalkan);
$data_total_dibatalkan = mysqli_fetch_assoc($result_total_dibatalkan);
$total_booking_dibatalkan = $data_total_dibatalkan['total'] ?? 0;

$query_total_pendapatan_selesai = "SELECT SUM(p.total_harga) as total FROM pesanan p LEFT JOIN users u ON p.id_user = u.ID LEFT JOIN lapangan l ON p.ID_Lapangan = l.ID $summary_query_selesai_condition";
$result_total_pendapatan_selesai = mysqli_query($db, $query_total_pendapatan_selesai);
$data_total_pendapatan_selesai = mysqli_fetch_assoc($result_total_pendapatan_selesai);
$total_pendapatan_dari_selesai = $data_total_pendapatan_selesai['total'] ?? 0;


function format_rupiah_admin_hist($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

function get_status_pesanan_admin_text($status_code) {
    if ($status_code == 1) return ['text' => 'Berlangsung', 'badge' => 'primary'];
    if ($status_code == 0) return ['text' => 'Selesai', 'badge' => 'success'];
    if ($status_code == 2) return ['text' => 'Dibatalkan', 'badge' => 'danger'];
    return ['text' => 'Tidak Diketahui', 'badge' => 'secondary'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Riwayat Booking</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { font-weight: bold; }
        .page-header { background-color: #6f42c1; color: #fff; padding: 25px 20px; border-radius: .25rem; margin-bottom: 30px; text-align: center; }
        .page-header h1 { margin-bottom: 0.5rem; }
        .page-header p { font-size: 1.1rem; color: #e9ecef; }
        .summary-card { text-align: center; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); margin-bottom: 1.5rem; }
        .summary-card .card-body { padding: 1.5rem; }
        .summary-card .card-title { font-size: 1rem; color: #6c757d; margin-bottom: 0.5rem; }
        .summary-card .display-5 { font-size: 2rem; font-weight: bold; color: #6f42c1; }
        .filter-card { background-color: #fff; padding: 1.5rem; border-radius: .3rem; margin-bottom: 2rem; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); }
        .table th { background-color: #e9ecef; font-weight:bold; }
        .table td, .table th { vertical-align: middle; }
        .action-buttons .btn { margin-right: 5px; margin-bottom: 5px; }
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
        <li class="nav-item"><a class="nav-link" href="peralatan.php">Peralatan</a></li>
        <li class="nav-item active"><a class="nav-link" href="riwayat_booking.php">Riwayat Booking <span class="sr-only">(current)</span></a></li>
      </ul>
      <a class="btn btn-outline-danger my-2 my-sm-0" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
    </div>
  </div>
</nav>

<main role="main" class="container mt-4 mb-5">
    <div class="page-header">
        <h1>Riwayat Semua Booking</h1>
        <p class="lead">Tinjau dan analisis seluruh transaksi booking yang telah terjadi.</p>
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
                    <h6 class="card-title">Total Booking Selesai</h6>
                    <p class="display-5"><?php echo $total_booking_selesai; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="card-title">Total Booking Dibatalkan</h6>
                    <p class="display-5"><?php echo $total_booking_dibatalkan; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
             <div class="card summary-card">
                <div class="card-body">
                    <h6 class="card-title">Total Pendapatan (dari Selesai)</h6>
                    <p class="display-5"><?php echo format_rupiah_admin_hist($total_pendapatan_dari_selesai); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4 filter-card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filter Riwayat Booking</h5>
        </div>
        <div class="card-body">
            <form action="riwayat_booking.php" method="POST" id="filter-riwayat-form">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="tanggal-mulai">Tanggal Main Mulai</label>
                        <input type="date" class="form-control form-control-sm" id="tanggal-mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($filter_values['tanggal_mulai']); ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="tanggal-akhir">Tanggal Main Akhir</label>
                        <input type="date" class="form-control form-control-sm" id="tanggal-akhir" name="tanggal_akhir" value="<?php echo htmlspecialchars($filter_values['tanggal_akhir']); ?>">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="filter-lapangan">Lapangan</label>
                        <select class="form-control form-control-sm" id="filter-lapangan" name="filter_lapangan">
                            <option value="">Semua</option>
                            <?php 
                            if($result_lapangan_options && mysqli_num_rows($result_lapangan_options) > 0){
                                mysqli_data_seek($result_lapangan_options, 0); 
                                while($lap_opt = mysqli_fetch_assoc($result_lapangan_options)){
                                    $selected = ($filter_values['lapangan'] == $lap_opt['ID']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($lap_opt['ID'])."' $selected>".htmlspecialchars($lap_opt['Nama'])."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                     <div class="col-md-2 form-group">
                        <label for="filter-status">Status Booking</label>
                        <select class="form-control form-control-sm" id="filter-status" name="filter_status">
                            <option value="">Semua</option>
                            <option value="0" <?php echo ($filter_values['status_booking'] === "0") ? 'selected' : ''; ?>>Selesai</option>
                            <option value="1" <?php echo ($filter_values['status_booking'] === "1") ? 'selected' : ''; ?>>Berlangsung</option>
                            <option value="2" <?php echo ($filter_values['status_booking'] === "2") ? 'selected' : ''; ?>>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group d-flex align-items-end">
                        <button type="submit" name="apply_filter" class="btn btn-primary btn-block btn-sm">Terapkan</button>
                    </div>
                </div>
                 <?php if (!empty($filter_values['tanggal_mulai']) || !empty($filter_values['tanggal_akhir']) || !empty($filter_values['lapangan']) || (isset($filter_values['status_booking']) && $filter_values['status_booking'] !== "")): ?>
                    <div class="row mt-2">
                        <div class="col-12 text-md-right">
                            <a href="riwayat_booking.php" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Semua Booking (<?php echo mysqli_num_rows($result_history); ?> ditemukan)</h5>
            <button class="btn btn-outline-secondary btn-sm" id="export-riwayat" onclick="alert('Fitur export belum diimplementasikan.');">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill mr-1" viewBox="0 0 16 16">
                  <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zm4 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                  <path d="M11 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM1.5 7a.5.5 0 0 0-.5.5V11a.5.5 0 0 0 .5.5H2v-4H1.5zM14.5 7a.5.5 0 0 0-.5.5V11a.5.5 0 0 0 .5.5H15V7h-.5z"/>
                </svg>
                Cetak / Export
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">ID Book</th>
                            <th scope="col">Tgl. Main</th>
                            <th scope="col">Jam</th>
                            <th scope="col">Penyewa</th>
                            <th scope="col">Lapangan</th>
                            <th scope="col" class="text-center">Durasi</th>
                            <th scope="col" class="text-right">Total</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-riwayat-body">
                        <?php if (mysqli_num_rows($result_history) > 0) : ?>
                            <?php while ($booking_row = mysqli_fetch_assoc($result_history)) : 
                                $status_item_admin = get_status_pesanan_admin_text($booking_row['StatusPesanan']);
                            ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($booking_row['BookingID']); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y', strtotime($booking_row['TanggalMain']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking_row['JamMain']))); ?> WIB</td>
                                <td><?php echo htmlspecialchars($booking_row['NamaUser'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($booking_row['NamaLapangan'] ?? ($booking_row['Nama'] ?? 'N/A')); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($booking_row['durasi']); ?> Jam</td>
                                <td class="text-right"><?php echo format_rupiah_admin_hist($booking_row['total_harga']); ?></td>
                                <td class="text-center"><span class="badge badge-<?php echo $status_item_admin['badge']; ?> status-badge"><?php echo htmlspecialchars($status_item_admin['text']); ?></span></td>
                                <td class="text-center action-buttons">
                                    <a href="admin_book_detail_view.php?ID_book=<?php echo $booking_row['BookingID']; ?>" class="btn btn-sm btn-info" title="Lihat Detail & Kelola">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center text-muted p-4">Tidak ada data riwayat booking yang ditemukan<?php echo (!empty($filter_values['tanggal_mulai']) || !empty($filter_values['tanggal_akhir']) || !empty($filter_values['lapangan']) || (isset($filter_values['status_booking']) && $filter_values['status_booking'] !== "")) ? ' untuk filter yang diterapkan' : ''; ?>.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (mysqli_num_rows($result_history) > 0): ?>
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