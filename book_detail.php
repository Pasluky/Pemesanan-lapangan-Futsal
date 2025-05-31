<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user") {
    header("location:login.php");
    exit;
}

$username_session = $_SESSION['username'];
$user_id_session = $_SESSION['user_id'];

if (!isset($_GET['ID_book']) || !is_numeric($_GET['ID_book'])) {
    $_SESSION['error_message'] = "ID Booking tidak valid.";
    header("location: history.php");
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
    $_SESSION['error_message'] = "Detail booking dengan ID #".$id_book_safe." tidak ditemukan atau Anda tidak memiliki akses.";
    header("location: history.php");
    exit;
}
$booking = mysqli_fetch_assoc($result_booking_detail);

$daftar_item_tambahan_dipesan = [];
$query_detail_tambahan = "SELECT 
                            pdt.jumlah_item, 
                            pdt.harga_satuan_saat_pesan, 
                            pdt.subtotal_item,
                            t.Nama AS nama_item_tambahan
                          FROM 
                            pesanan_detail_tambahan pdt
                          JOIN 
                            tambahan t ON pdt.id_tambahan = t.ID
                          WHERE 
                            pdt.id_pesanan = '$id_book_safe'";
$result_detail_tambahan = mysqli_query($db, $query_detail_tambahan);
if ($result_detail_tambahan) {
    while($row_tambahan = mysqli_fetch_assoc($result_detail_tambahan)){
        $daftar_item_tambahan_dipesan[] = $row_tambahan;
    }
}


function format_rupiah_book_detail($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

function get_status_pesanan_text_book_detail($status_code) {
    if ($status_code == 1) return ['text' => 'Berlangsung', 'badge' => 'primary'];
    if ($status_code == 0) return ['text' => 'Selesai', 'badge' => 'success'];
    if ($status_code == 2) return ['text' => 'Dibatalkan', 'badge' => 'danger'];
    return ['text' => 'Tidak Diketahui', 'badge' => 'secondary'];
}
$status_info_pesanan = get_status_pesanan_text_book_detail($booking['status'] ?? null); 

$nama_lapangan_display = !empty($booking['NamaLapangan']) ? $booking['NamaLapangan'] : (!empty($booking['Nama']) ? $booking['Nama'] : 'Lapangan Tidak Diketahui');
$gambar_lapangan_display = 'Garasi-Futsal.jpg'; 

$telepon_pemesan = $booking['Telepon'] ?? 'Tidak ada data';

$subtotal_lapangan_calc = ($booking['HargaLapanganPerSesiDariDB'] ?? 0) * ($booking['durasi'] ?? 0);
$status_pembayaran_display = $booking['Status_Pembayaran'] ?? 'Belum Lunas';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo htmlspecialchars($booking['ID']); ?> - Penyewaan Olahraga</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { font-weight: bold; }
        .detail-card { background-color: #fff; border-radius: .75rem; box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.08); margin-top: 2rem; overflow: hidden; }
        .detail-card-header { background-color: #007bff; color: white; font-size: 1.3rem; font-weight: bold; padding: 1rem 1.5rem; border-top-left-radius: .75rem; border-top-right-radius: .75rem; }
        .detail-card-body { padding: 2rem; }
        .field-image-detail-container { text-align: center; margin-bottom:1.5rem; }
        .field-image-detail { width: 100%; max-height: 300px; object-fit: cover; border-radius: .5rem; border: 1px solid #dee2e6; margin-bottom: 1rem; }
        .field-name-detail { font-size: 1.5rem; font-weight: bold; color: #343a40; margin-top:0.5rem; }
        .info-group { margin-bottom: 1.5rem; }
        .info-group h5 { font-weight: bold; color: #495057; margin-bottom: .75rem; padding-bottom: .5rem; border-bottom: 2px solid #007bff; display: inline-block; }
        .info-item { display: flex; justify-content: space-between; padding: .6rem 0; font-size: 1rem; border-bottom: 1px solid #f1f1f1; }
        .info-item:last-child { border-bottom: none; }
        .info-item dt { font-weight: 600; color: #495057; flex-basis: 45%; margin-right: 10px; word-break: break-word;}
        .info-item dd { margin-bottom: 0; color: #212529; text-align: right; flex-basis: 55%; word-break: break-word;}
        .payment-summary .info-item dt, .payment-summary .info-item dd { font-size: 1.05rem; }
        .payment-summary .total-amount strong { font-size: 1.25rem; color: #28a745; }
        .status-badge { font-size: .9rem; padding: .4em .7em; }
        .footer-actions { padding: 1.5rem; text-align: right; background-color: #f8f9fa; border-top: 1px solid #dee2e6;}
        .alert-container { margin-bottom: 1.5rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="user_home.php">Penyewaan Olahraga</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item"><a class="nav-link" href="user_home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="booking.php">Booking</a></li>
        <li class="nav-item active"><a class="nav-link" href="history.php">Riwayat <span class="sr-only">(current)</span></a></li>
      </ul>
      <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo htmlspecialchars($username_session); ?>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="profil_pengguna.php">Profil</a>
          <a class="dropdown-item" href="history.php">Riwayat</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
        </div>
      </div>
    </div>
  </div>
</nav>

<main role="main" class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9"> 
            <div class="detail-card">
                <div class="detail-card-header">
                    Rincian Pesanan #<?php echo htmlspecialchars($booking['ID']); ?>
                </div>
                <div class="detail-card-body">
                    <div class="alert-container">
                        <?php
                        if (isset($_SESSION['success_message'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
                                 htmlspecialchars($_SESSION['success_message']) .
                                 '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                            unset($_SESSION['success_message']);
                        }
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-md-5 mb-4 mb-md-0">
                            <div class="field-image-detail-container">
                                <img src="./img/<?php echo htmlspecialchars($gambar_lapangan_display); ?>" alt="Gambar <?php echo htmlspecialchars($nama_lapangan_display); ?>" class="field-image-detail">
                                <h4 class="field-name-detail mt-2"><?php echo htmlspecialchars($nama_lapangan_display); ?></h4>
                                <p class="text-muted small">
                                    Harga Lapangan: <?php echo format_rupiah_book_detail($booking['HargaLapanganPerSesiDariDB'] ?? 0); ?> / Sesi (2 Jam)
                                </p>
                                <p class="text-muted small mt-2">
                                    <?php echo !empty($booking['DeskripsiLapanganDB']) ? nl2br(htmlspecialchars($booking['DeskripsiLapanganDB'])) : '<i>Deskripsi lapangan tidak tersedia.</i>'; ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="info-group">
                                <h5>Data Pemesan</h5>
                                <dl>
                                    <div class="info-item">
                                        <dt>Nama</dt>
                                        <dd><?php echo htmlspecialchars($username_session); ?></dd>
                                    </div>
                                    <div class="info-item">
                                        <dt>No. Telepon</dt>
                                        <dd><?php echo htmlspecialchars($telepon_pemesan); ?></dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="info-group">
                                <h5>Detail Jadwal Sewa</h5>
                                <dl>
                                    <div class="info-item">
                                        <dt>Tanggal Main</dt>
                                        <dd><?php echo htmlspecialchars(date('l, d F Y', strtotime($booking['Tanggal']))); ?></dd>
                                    </div>
                                    <div class="info-item">
                                        <dt>Sesi Waktu</dt>
                                        <dd>
                                            <?php 
                                                $jam_mulai_dt_user = new DateTime($booking['Jam'] ?? '00:00:00');
                                                $jam_selesai_dt_user = clone $jam_mulai_dt_user;
                                                if(isset($booking['durasi']) && is_numeric($booking['durasi'])){ $jam_selesai_dt_user->add(new DateInterval('PT'.$booking['durasi'].'H'));}
                                                echo htmlspecialchars($jam_mulai_dt_user->format('H:i')). " - " . htmlspecialchars($jam_selesai_dt_user->format('H:i')) . " WIB";
                                            ?>
                                        </dd>
                                    </div>
                                    <div class="info-item">
                                        <dt>Durasi</dt>
                                        <dd><?php echo htmlspecialchars($booking['durasi'] ?? '0'); ?> Jam</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <?php if (!empty($daftar_item_tambahan_dipesan)) : ?>
                    <div class="info-group">
                        <h5 class="section-title-admin">Item Tambahan Dipesan</h5>
                        <dl>
                            <?php foreach($daftar_item_tambahan_dipesan as $item_dipesan): ?>
                            <div class="info-item">
                                <dt><?php echo htmlspecialchars($item_dipesan['nama_item_tambahan']); ?> (x<?php echo htmlspecialchars($item_dipesan['jumlah_item']); ?>)</dt>
                                <dd><?php echo format_rupiah_book_detail($item_dipesan['subtotal_item']); ?></dd>
                            </div>
                            <?php endforeach; ?>
                        </dl>
                    </div>
                    <hr>
                    <?php endif; ?>

                    <div class="info-group payment-summary">
                        <h5 class="section-title-admin">Rincian Pembayaran</h5>
                        <dl>
                             <div class="info-item">
                                <dt>Subtotal Lapangan</dt>
                                <dd><?php echo format_rupiah_book_detail($subtotal_lapangan_calc); ?></dd>
                            </div>
                            <?php foreach($daftar_item_tambahan_dipesan as $item_dipesan): ?>
                            <div class="info-item">
                                <dt>Subtotal <?php echo htmlspecialchars($item_dipesan['nama_item_tambahan']); ?></dt>
                                <dd><?php echo format_rupiah_book_detail($item_dipesan['subtotal_item']); ?></dd>
                            </div>
                            <?php endforeach; ?>
                            <hr style="border-top: 1px dashed #ccc; margin: 0.5rem 0; width:100%;">
                            <div class="info-item total-amount">
                                <dt>Total Pembayaran</dt>
                                <dd><strong><?php echo format_rupiah_book_detail($booking['total_harga'] ?? 0); ?></strong></dd>
                            </div>
                            <div class="info-item">
                                <dt>Jumlah Dibayar</dt>
                                <dd><?php echo format_rupiah_book_detail($booking['bayar'] ?? 0); ?></dd>
                            </div>
                            <div class="info-item">
                                <dt>Kembalian</dt>
                                <dd><?php echo format_rupiah_book_detail($booking['kembali'] ?? 0); ?></dd>
                            </div>
                        </dl>
                    </div>
                    <hr>
                     <div class="info-group">
                        <h5 class="section-title-admin">Status</h5>
                         <dl>
                            <div class="info-item">
                                <dt>Status Pesanan:</dt>
                                <dd><span class="badge badge-<?php echo $status_info_pesanan['badge']; ?> status-badge"><?php echo htmlspecialchars($status_info_pesanan['text']); ?></span></dd>
                            </div>
                            <div class="info-item">
                                <dt>Status Pembayaran:</dt>
                                <dd><span class="badge badge-<?php echo (strtolower($status_pembayaran_display) == 'lunas') ? 'success' : 'warning'; ?> status-badge"><?php echo htmlspecialchars($status_pembayaran_display); ?></span></dd>
                            </div>
                            <?php if (isset($booking['Tanggal_Pemesanan_Dibuat']) && !empty($booking['Tanggal_Pemesanan_Dibuat']) && $booking['Tanggal_Pemesanan_Dibuat'] != '0000-00-00 00:00:00'): ?>
                            <div class="info-item mt-2">
                                <dt>Dipesan pada:</dt>
                                <dd class="text-muted"><small><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['Tanggal_Pemesanan_Dibuat']))); ?> WIB</small></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
                <div class="card-footer footer-actions">
                    <a href="history.php" class="btn btn-outline-secondary mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill mr-1" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/></svg>
                        Kembali ke Riwayat
                    </a>
                    <button class="btn btn-primary" onclick="window.print();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill mr-1" viewBox="0 0 16 16"><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zm4 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/><path d="M11 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM1.5 7a.5.5 0 0 0-.5.5V11a.5.5 0 0 0 .5.5H2v-4H1.5zM14.5 7a.5.5 0 0 0-.5.5V11a.5.5 0 0 0 .5.5H15V7h-.5z"/></svg>
                        Cetak Bukti Pesanan
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="text-center py-4 bg-light border-top mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Layanan Penyewaan Olahraga. Semua Hak Dilindungi.</p>
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