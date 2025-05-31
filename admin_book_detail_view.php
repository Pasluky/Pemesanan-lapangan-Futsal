<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$adminFullName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator';

if (!isset($_GET['ID_book']) || !is_numeric($_GET['ID_book'])) {
    $_SESSION['error_message'] = "ID Booking tidak valid.";
    header("location: riwayat_booking.php"); 
    exit;
}
$id_book_safe = mysqli_real_escape_string($db, $_GET['ID_book']);

$query_booking_detail = "SELECT 
                            p.*, 
                            u.Nama AS NamaUserPemesanan, 
                            u.Email AS EmailUserPemesanan,
                            l.Nama AS NamaLapanganBooking, 
                            l.Tipe AS TipeLapangan, 
                            l.Jenis AS JenisLapangan,
                            l.Harga AS HargaLapanganPerSesiDariDB 
                         FROM 
                            pesanan p
                         LEFT JOIN 
                            users u ON p.id_user = u.ID
                         LEFT JOIN 
                            lapangan l ON p.ID_lapangan = l.ID 
                         WHERE 
                            p.ID = '$id_book_safe'";

$result_booking_detail = mysqli_query($db, $query_booking_detail);

if (!$result_booking_detail) {
    die("Error mengambil detail booking: " . mysqli_error($db));
}

if (mysqli_num_rows($result_booking_detail) == 0) {
    $_SESSION['error_message'] = "Detail booking dengan ID #".$id_book_safe." tidak ditemukan.";
    header("location: riwayat_booking.php");
    exit;
}
$booking = mysqli_fetch_assoc($result_booking_detail);

function format_rupiah_admin_detail($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

function get_status_pesanan_admin_detail_text($status_code) {
    if ($status_code == 1) return ['text' => 'Berlangsung', 'badge' => 'primary'];
    if ($status_code == 0) return ['text' => 'Selesai', 'badge' => 'success'];
    if ($status_code == 2) return ['text' => 'Dibatalkan', 'badge' => 'danger'];
    return ['text' => 'Tidak Diketahui', 'badge' => 'secondary'];
}
$status_info_pesanan = get_status_pesanan_admin_detail_text($booking['status'] ?? null);

$jumlah_sepatu = $booking['Tambahan_1'] ?? 0;
$jumlah_kostum = $booking['Tambahan_2'] ?? 0;

$harga_satuan_sepatu_db = 0;
$nama_item_sepatu = "Sepatu";
$stmt_sepatu = mysqli_prepare($db, "SELECT Harga, Nama FROM tambahan WHERE ID = 3 LIMIT 1"); 
if ($stmt_sepatu) {
    mysqli_stmt_execute($stmt_sepatu);
    $result_sepatu = mysqli_stmt_get_result($stmt_sepatu);
    if ($row_s = mysqli_fetch_assoc($result_sepatu)) {
        $harga_satuan_sepatu_db = (float)$row_s['Harga'];
        $nama_item_sepatu = $row_s['Nama'];
    }
    mysqli_stmt_close($stmt_sepatu);
}

$harga_satuan_kostum_db = 0;
$nama_item_kostum = "Kostum";
$stmt_kostum = mysqli_prepare($db, "SELECT Harga, Nama FROM tambahan WHERE ID = 2 LIMIT 1"); 
if ($stmt_kostum) {
    mysqli_stmt_execute($stmt_kostum);
    $result_kostum = mysqli_stmt_get_result($stmt_kostum);
    if ($row_k = mysqli_fetch_assoc($result_kostum)) {
        $harga_satuan_kostum_db = (float)$row_k['Harga'];
        $nama_item_kostum = $row_k['Nama'];
    }
    mysqli_stmt_close($stmt_kostum);
}

$biaya_sepatu_calc = $jumlah_sepatu * $harga_satuan_sepatu_db;
$biaya_kostum_calc = $jumlah_kostum * $harga_satuan_kostum_db;

$subtotal_lapangan_calc = ($booking['HargaLapanganPerSesiDariDB'] ?? 0) * ($booking['durasi'] ?? 0);
$status_pembayaran_display = $booking['Status_Pembayaran'] ?? 'Belum Lunas';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Detail Pesanan #<?php echo htmlspecialchars($booking['ID']); ?></title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { font-weight: bold; }
        .detail-view-card { background-color: #fff; border-radius: .75rem; box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.08); margin-top: 2rem; }
        .detail-view-card .card-header { background-color: #6f42c1; color: white; font-size: 1.3rem; font-weight: bold; padding: 1rem 1.5rem; border-top-left-radius: .75rem; border-top-right-radius: .75rem; }
        .detail-view-card .card-body { padding: 2rem; }
        .section-title-admin { font-weight: bold; color: #495057; margin-top: 1.5rem; margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 2px solid #6f42c1; display: inline-block; }
        
        .detail-info-item {
            display: flex;
            justify-content: space-between;
            padding: .6rem 0;
            font-size: 0.95rem; 
            border-bottom: 1px solid #f1f1f1;
        }
        .detail-info-item:last-child {
            border-bottom: none;
        }
        .detail-info-item dt {
            font-weight: 600;
            color: #5a6268;
            flex-basis: 40%; 
            margin-right: 10px;
            word-break: break-word; 
        }
        .detail-info-item dd {
            margin-bottom: 0;
            color: #212529;
            text-align: right;
            flex-basis: 60%; 
            word-break: break-word; 
        }

        .payment-summary-admin strong { font-size: 1.1em; }
        .payment-summary-admin .total-harga strong { color: #28a745; font-size: 1.3em; }
        .status-badge-admin { font-size: 1rem; padding: .5em .8em; }
        .actions-footer { padding: 1.5rem; text-align: right; background-color: #f8f9fa; border-top: 1px solid #dee2e6;}
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
      <span class="navbar-text mr-3 text-light">Halo, <?php echo $adminFullName; ?>!</span>
      <a class="btn btn-outline-danger my-2 my-sm-0" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
    </div>
  </div>
</nav>

<main role="main" class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="detail-view-card">
                <div class="card-header">
                    Detail Pesanan #<?php echo htmlspecialchars($booking['ID']); ?>
                </div>
                <div class="card-body">

                    <div class="alert-container">
                        <?php
                        if (isset($_SESSION['success_message_status'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['success_message_status']) . '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                            unset($_SESSION['success_message_status']);
                        }
                        if (isset($_SESSION['error_message_status'])) {
                             echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['error_message_status']) . '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                            unset($_SESSION['error_message_status']);
                        }
                        ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <h5 class="section-title-admin">Informasi Pemesan</h5>
                                <dl>
                                    <div class="detail-info-item"><dt>ID User:</dt><dd><?php echo htmlspecialchars($booking['id_user']); ?></dd></div>
                                    <div class="detail-info-item"><dt>Nama Pemesan:</dt><dd><?php echo htmlspecialchars($booking['NamaUserPemesanan'] ?? 'N/A'); ?></dd></div>
                                    <div class="detail-info-item"><dt>Email Pemesan:</dt><dd><?php echo htmlspecialchars($booking['EmailUserPemesanan'] ?? 'N/A'); ?></dd></div>
                                    <div class="detail-info-item"><dt>No. Telepon (diinput):</dt><dd><?php echo htmlspecialchars($booking['Telepon']); ?></dd></div>
                                </dl>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="info-group">
                                <h5 class="section-title-admin">Informasi Lapangan</h5>
                                <dl>
                                    <div class="detail-info-item"><dt>ID Lapangan:</dt><dd><?php echo htmlspecialchars($booking['ID_lapangan']); ?></dd></div>
                                    <div class="detail-info-item"><dt>Nama Lapangan:</dt><dd><?php echo htmlspecialchars($booking['NamaLapanganBooking'] ?? $booking['Nama']); ?></dd></div>
                                    <div class="detail-info-item"><dt>Tipe:</dt><dd><?php echo htmlspecialchars($booking['TipeLapangan'] ?? 'N/A'); ?></dd></div>
                                    <div class="detail-info-item"><dt>Jenis:</dt><dd><?php echo htmlspecialchars($booking['JenisLapangan'] ?? 'N/A'); ?></dd></div>
                                    <div class="detail-info-item"><dt>Harga Lapangan/Sesi:</dt><dd><?php echo format_rupiah_admin_detail($booking['HargaLapanganPerSesiDariDB'] ?? 0); ?></dd></div>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <hr>
                     <div class="info-group">
                        <h5 class="section-title-admin">Detail Jadwal & Tambahan</h5>
                        <dl>
                            <div class="detail-info-item"><dt>Tanggal Main:</dt><dd><?php echo htmlspecialchars(date('l, d F Y', strtotime($booking['Tanggal']))); ?></dd></div>
                            <div class="detail-info-item"><dt>Sesi Waktu:</dt><dd><?php $jam_mulai_dt_admin = new DateTime($booking['Jam'] ?? '00:00:00'); $jam_selesai_dt_admin = clone $jam_mulai_dt_admin; if(isset($booking['durasi']) && is_numeric($booking['durasi'])){ $jam_selesai_dt_admin->add(new DateInterval('PT'.$booking['durasi'].'H'));} echo htmlspecialchars($jam_mulai_dt_admin->format('H:i')). " - " . htmlspecialchars($jam_selesai_dt_admin->format('H:i')) . " WIB";?></dd></div>
                            <div class="detail-info-item"><dt>Durasi:</dt><dd><?php echo htmlspecialchars($booking['durasi'] ?? '0'); ?> Jam</dd></div>
                            <?php if ($jumlah_sepatu > 0): ?>
                            <div class="detail-info-item"><dt>Sewa <?php echo htmlspecialchars($nama_item_sepatu); ?>:</dt><dd><?php echo htmlspecialchars($jumlah_sepatu); ?> pasang (<?php echo format_rupiah_admin_detail($biaya_sepatu_calc); ?>)</dd></div>
                            <?php endif; ?>
                            <?php if ($jumlah_kostum > 0): ?>
                            <div class="detail-info-item"><dt>Sewa <?php echo htmlspecialchars($nama_item_kostum); ?>:</dt><dd><?php echo htmlspecialchars($jumlah_kostum); ?> stel (<?php echo format_rupiah_admin_detail($biaya_kostum_calc); ?>)</dd></div>
                            <?php endif; ?>
                        </dl>
                    </div>
                    <hr>
                    <div class="info-group payment-summary-admin">
                        <h5 class="section-title-admin">Rincian Pembayaran</h5>
                        <dl>
                            <div class="detail-info-item"><dt>Subtotal Lapangan:</dt><dd><?php echo format_rupiah_admin_detail($subtotal_lapangan_calc); ?></dd></div>
                             <?php if ($biaya_sepatu_calc > 0): ?>
                            <div class="detail-info-item"><dt>Subtotal <?php echo htmlspecialchars($nama_item_sepatu); ?>:</dt><dd><?php echo format_rupiah_admin_detail($biaya_sepatu_calc); ?></dd></div>
                             <?php endif; ?>
                             <?php if ($biaya_kostum_calc > 0): ?>
                            <div class="detail-info-item"><dt>Subtotal <?php echo htmlspecialchars($nama_item_kostum); ?>:</dt><dd><?php echo format_rupiah_admin_detail($biaya_kostum_calc); ?></dd></div>
                             <?php endif; ?>
                            <div class="detail-info-item total-harga"><dt>TOTAL PEMBAYARAN:</dt><dd><strong><?php echo format_rupiah_admin_detail($booking['total_harga'] ?? 0); ?></strong></dd></div>
                            <div class="detail-info-item"><dt>Jumlah Dibayar:</dt><dd><?php echo format_rupiah_admin_detail($booking['bayar'] ?? 0); ?></dd></div>
                            <div class="detail-info-item"><dt>Kembalian:</dt><dd><?php echo format_rupiah_admin_detail($booking['kembali'] ?? 0); ?></dd></div>
                        </dl>
                    </div>
                    <hr>
                    <div class="info-group">
                        <h5 class="section-title-admin">Status</h5>
                         <dl>
                            <div class="detail-info-item">
                                <dt>Status Pesanan:</dt>
                                <dd><span class="badge badge-<?php echo $status_info_pesanan['badge']; ?> status-badge-admin"><?php echo htmlspecialchars($status_info_pesanan['text']); ?></span></dd>
                            </div>
                            <div class="detail-info-item">
                                <dt>Status Pembayaran:</dt>
                                <dd><span class="badge badge-<?php echo (strtolower($status_pembayaran_display) == 'lunas') ? 'success' : 'warning'; ?> status-badge-admin"><?php echo htmlspecialchars($status_pembayaran_display); ?></span></dd>
                            </div>
                            <?php if (isset($booking['Tanggal_Pemesanan_Dibuat']) && !empty($booking['Tanggal_Pemesanan_Dibuat']) && $booking['Tanggal_Pemesanan_Dibuat'] != '0000-00-00 00:00:00'): ?>
                            <div class="detail-info-item mt-2">
                                <dt>Dipesan pada:</dt>
                                <dd class="text-muted"><small><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['Tanggal_Pemesanan_Dibuat']))); ?> WIB</small></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <h5 class="section-title-admin">Tindakan Admin</h5>
                        <form action="proses_update_status_booking.php" method="POST">
                            <input type="hidden" name="ID_book" value="<?php echo htmlspecialchars($booking['ID']); ?>">
                            <div class="form-row align-items-end">
                                <div class="col-md-5 form-group">
                                    <label for="ubah_status_pesanan">Ubah Status Pesanan:</label>
                                    <select name="status_pesanan_baru" id="ubah_status_pesanan" class="form-control form-control-sm">
                                        <option value="1" <?php if($booking['status'] == 1) echo 'selected';?>>Berlangsung</option>
                                        <option value="0" <?php if($booking['status'] == 0) echo 'selected';?>>Selesai</option>
                                        <option value="2" <?php if($booking['status'] == 2) echo 'selected';?>>Dibatalkan oleh Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-5 form-group">
                                    <label for="ubah_status_pembayaran">Ubah Status Pembayaran:</label>
                                    <select name="status_pembayaran_baru" id="ubah_status_pembayaran" class="form-control form-control-sm">
                                        <option value="Belum Lunas" <?php if(strtolower($status_pembayaran_display) == 'belum lunas') echo 'selected';?>>Belum Lunas</option>
                                        <option value="Lunas" <?php if(strtolower($status_pembayaran_display) == 'lunas') echo 'selected';?>>Lunas</option>
                                    </select>
                                </div>
                                <div class="col-md-2 form-group">
                                    <button type="submit" name="update_status_booking" class="btn btn-warning btn-sm btn-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-footer actions-footer">
                    <a href="riwayat_booking.php" class="btn btn-outline-secondary mr-2">
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill mr-1" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/></svg>
                        Kembali ke Riwayat
                    </a>
                    <button class="btn btn-info" onclick="window.print();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill mr-1" viewBox="0 0 16 16"><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zm4 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/><path d="M11 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM1.5 7a.5.5 0 0 0-.5.5V11a.5.5 0 0 0 .5.5H2v-4H1.5zM14.5 7a.5.5 0 0 0-.5.5V11a.5.5 0 0 0 .5.5H15V7h-.5z"/></svg>
                        Cetak Bukti
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="text-center py-4 bg-light border-top mt-5">
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