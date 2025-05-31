<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user") {
    header("location:login.php");
    exit;
}

$username_session = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna';
$user_id_session = $_SESSION['user_id'];

$id_field = '';
if (isset($_GET['ID_field']) && is_numeric($_GET['ID_field'])) {
    $id_field = mysqli_real_escape_string($db, $_GET['ID_field']);
} else {
    $_SESSION['error_message_booking_form'] = "ID Lapangan tidak valid.";
    header("location: booking.php");
    exit;
}

$query_lapangan = "SELECT * FROM lapangan WHERE ID='$id_field' AND status = 1";
$result_lapangan = mysqli_query($db, $query_lapangan);

if ($result_lapangan && mysqli_num_rows($result_lapangan) > 0) {
    $lapangan = mysqli_fetch_assoc($result_lapangan);
} else {
    $_SESSION['error_message_booking_form'] = "Data lapangan tidak ditemukan atau tidak aktif.";
    header("location: booking.php");
    exit;
}

$query_tambahan = "SELECT ID, Nama, Harga FROM tambahan ORDER BY Nama ASC";
$result_tambahan = mysqli_query($db, $query_tambahan);
$daftar_item_tambahan = [];
if ($result_tambahan) {
    while ($item = mysqli_fetch_assoc($result_tambahan)) {
        $daftar_item_tambahan[] = $item;
    }
}

$jam_buka = 7;
$jam_tutup_operasi = 21;
$durasi_sesi = 2;
$daftar_sesi = [];
for ($jam = $jam_buka; $jam < $jam_tutup_operasi; $jam += $durasi_sesi) {
    $jam_mulai_str = str_pad($jam, 2, "0", STR_PAD_LEFT) . ":00";
    $jam_selesai_str = str_pad($jam + $durasi_sesi, 2, "0", STR_PAD_LEFT) . ":00";
    $daftar_sesi[$jam_mulai_str] = $jam_mulai_str . " - " . $jam_selesai_str;
}

$booked_sessions_today = [];
$selected_date = '';

if (isset($_GET['tanggal_main'])) {
    $selected_date = mysqli_real_escape_string($db, $_GET['tanggal_main']);
    if (!empty($selected_date) && strtotime($selected_date) >= strtotime(date('Y-m-d'))) {
        $query_booked = "SELECT Jam FROM pesanan WHERE ID_Lapangan = ? AND Tanggal = ? AND status != 2";
        $stmt_booked = mysqli_prepare($db, $query_booked);
        mysqli_stmt_bind_param($stmt_booked, "is", $id_field, $selected_date);
        mysqli_stmt_execute($stmt_booked);
        $result_booked_db = mysqli_stmt_get_result($stmt_booked);
        if ($result_booked_db) {
            while ($booked_row = mysqli_fetch_assoc($result_booked_db)) {
                $booked_sessions_today[] = date("H:i", strtotime($booked_row['Jam']));
            }
        } else {
            error_log("Error fetching booked sessions: " . mysqli_error($db));
        }
        mysqli_stmt_close($stmt_booked);
    } elseif (!empty($selected_date)) {
        $_SESSION['error_message_booking_form'] = "Tanggal yang dipilih tidak valid.";
        $selected_date = ''; 
    }
}

function format_rupiah_detail_form($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

$gambar_lapangan_display = !empty($lapangan['foto']) ? $lapangan['foto'] : 'Garasi-Futsal.jpg';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Lapangan: <?php echo htmlspecialchars($lapangan['Nama']); ?> - Penyewaan Olahraga</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { 
            font-weight: bold; 
            display: flex;
            align-items: center;
        }
        .navbar-brand-custom svg {
            margin-right: 8px;
        }
        .field-presentation-card { background-color: #fff; border-radius: .5rem; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); }
        .field-image-detail { width: 100%; max-height: 400px; object-fit: cover; border-radius: .5rem .5rem 0 0; }
        .field-info { padding: 1.5rem; }
        .field-info h2 { font-weight: bold; color: #343a40; margin-bottom: .5rem; }
        .field-info .price { font-size: 1.5rem; font-weight: bold; color: #28a745; margin-bottom: 1rem; }
        .booking-form-card { background-color: #fff; border-radius: .5rem; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); margin-top: 1.5rem; }
        .booking-form-card .card-header { background-color: #007bff; color: white; font-weight: bold; font-size: 1.2rem; }
        .form-section-title { font-size: 1.1rem; font-weight: bold; margin-top: 1.5rem; margin-bottom: 1rem; color: #495057; border-bottom: 1px solid #e9ecef; padding-bottom: 0.5rem; }
        .session-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; margin-bottom: 1.5rem; }
        .session-btn { padding: 0.6rem; text-align: center; border: 1px solid #007bff; color: #007bff; background-color: white; border-radius: .25rem; cursor: pointer; transition: all 0.2s ease; font-size:0.9rem; }
        .session-btn:hover:not(.disabled):not(.active) { background-color: #e6f2ff; }
        .session-btn.active { background-color: #007bff; color: white; font-weight: bold; }
        .session-btn.disabled { background-color: #e9ecef; color: #6c757d; border-color: #ced4da; cursor: not-allowed; opacity: 0.7; }
        input[type=number]::-webkit-outer-spin-button, input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
        .alert-container-form { margin-bottom: 1rem; }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="dribbble-icon-brand-fdetail" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
    <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
  </symbol>
</svg>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="user_home.php">
        <svg width="24" height="24" fill="currentColor"><use xlink:href="#dribbble-icon-brand-fdetail"/></svg>
        Penyewaan Olahraga
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item"><a class="nav-link" href="user_home.php">Home</a></li>
        <li class="nav-item active"><a class="nav-link" href="booking.php">Booking <span class="sr-only">(current)</span></a></li>
        <li class="nav-item"><a class="nav-link" href="history.php">Riwayat</a></li>
        <li class="nav-item"><a class="nav-link" href="peralatan_user.php">Sewa Peralatan</a></li>
        <li class="nav-item"><a class="nav-link" href="about_us.php">Tentang Kami</a></li>
      </ul>
      <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo htmlspecialchars($username_session); ?>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="profil_pengguna.php">Profil Saya</a>
          <a class="dropdown-item" href="history.php">Riwayat Booking</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
        </div>
      </div>
    </div>
  </div>
</nav>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="field-presentation-card">
                <img src="./img/<?php echo htmlspecialchars($gambar_lapangan_display); ?>" class="field-image-detail" alt="Gambar <?php echo htmlspecialchars($lapangan['Nama']); ?>">
                <div class="field-info">
                    <h2><?php echo htmlspecialchars($lapangan['Nama']); ?></h2>
                    <p class="price"><?php echo format_rupiah_detail_form($lapangan['Harga']); ?> / Sesi (2 Jam)</p>
                    <p class="text-muted">
                        <?php echo !empty($lapangan['Deskripsi']) ? nl2br(htmlspecialchars($lapangan['Deskripsi'])) : '<i>Deskripsi untuk lapangan ini tidak tersedia.</i>'; ?>
                    </p>
                    <p class="text-muted small">Tipe: <?php echo htmlspecialchars($lapangan['Tipe']); ?> | Jenis: <?php echo htmlspecialchars($lapangan['Jenis']); ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card booking-form-card">
                <div class="card-header text-center">Formulir Pemesanan</div>
                <div class="card-body p-4">
                    <div class="alert-container-form">
                        <?php
                        if (isset($_SESSION['error_message_booking_form'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['error_message_booking_form']) . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                            unset($_SESSION['error_message_booking_form']);
                        }
                        ?>
                    </div>
                    <form method="POST" action="proses_booking_sesi.php" id="bookingFormWithSession">
                        <h5 class="form-section-title">Data Pemesan</h5>
                        <div class="form-group">
                            <label for="name_display">Nama Pemesan</label>
                            <input type="text" id="name_display" class="form-control" value="<?php echo htmlspecialchars($username_session); ?>" readonly>
                            <input type="hidden" name="nama_pemesan" value="<?php echo htmlspecialchars($username_session); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">No. Telepon Aktif</label>
                            <input type="number" id="phone" class="form-control" placeholder="Contoh: 08123456789" required name="telepon">
                        </div>
                        
                        <h5 class="form-section-title">Pilih Tanggal & Sesi</h5>
                        <div class="form-group">
                            <label for="tanggal_main">Pilih Tanggal Main</label>
                            <input type="date" id="tanggal_main" class="form-control" required name="tanggal_main" value="<?php echo htmlspecialchars($selected_date); ?>" min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div id="sesi-container">
                            <label>Pilih Sesi (Durasi 2 Jam)</label>
                            <?php if (!empty($selected_date)): ?>
                                <div class="session-grid">
                                    <?php if (empty($daftar_sesi)): ?>
                                        <p class="text-muted">Tidak ada definisi sesi yang valid.</p>
                                    <?php else: ?>
                                        <?php foreach ($daftar_sesi as $jam_mulai_key => $sesi_text) : 
                                            $is_booked = in_array($jam_mulai_key, $booked_sessions_today);
                                        ?>
                                        <button type="button" 
                                                class="btn session-btn <?php echo $is_booked ? 'disabled' : ''; ?>" 
                                                data-sesi-val="<?php echo $jam_mulai_key; ?>"
                                                <?php echo $is_booked ? 'disabled title="Sesi sudah dipesan"' : ''; ?>>
                                            <?php echo $sesi_text; ?>
                                        </button>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" name="sesi_waktu_mulai" id="sesi_waktu_mulai" value="">
                                <div id="error-sesi" class="text-danger small mt-1" style="display:none;">Harap pilih satu sesi.</div>
                            <?php else: ?>
                                <p class="text-muted"><i>Pilih tanggal terlebih dahulu untuk melihat sesi yang tersedia.</i></p>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="form-section-title">Tambahan (Opsional)</h5>
                        <div class="row">
                            <?php if (!empty($daftar_item_tambahan)): ?>
                                <?php foreach ($daftar_item_tambahan as $item): ?>
                                    <div class="col-md-6 form-group">
                                        <label for="tambahan_<?php echo $item['ID']; ?>">
                                            <?php echo htmlspecialchars($item['Nama']); ?> (<?php echo format_rupiah_detail_form($item['Harga']); ?>/item)
                                        </label>
                                        <input type="number" 
                                               id="tambahan_<?php echo $item['ID']; ?>" 
                                               class="form-control form-control-sm jumlah-tambahan-input" 
                                               placeholder="Jumlah" 
                                               name="jumlah_tambahan[<?php echo $item['ID']; ?>]" 
                                               min="0" 
                                               value="0"
                                               data-harga_item="<?php echo $item['Harga']; ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <p class="text-muted"><i>Tidak ada item tambahan yang tersedia.</i></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <hr class="my-4">
                        <h5 class="form-section-title">Rincian Pembayaran</h5>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="total">Total Biaya</label>
                                <input type="text" id="total" class="form-control font-weight-bold" value="Rp 0" readonly name="total_biaya_display">
                                <input type="hidden" name="total_biaya" id="total_biaya_hidden">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="pay">Jumlah Bayar</label>
                                <input type="number" id="pay" class="form-control" placeholder="Masukkan nominal" required name="jumlah_bayar" min="0">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="kembali">Kembalian</label>
                                <input type="text" id="kembali" class="form-control" value="Rp 0" readonly name="kembalian_display">
                            </div>
                        </div>
                        
                        <input type="hidden" id="field_price_per_session" value="<?php echo $lapangan['Harga']; ?>" name="harga_lapangan_sesi">
                        <input type="hidden" value="<?php echo $lapangan['ID']; ?>" name="id_lapangan">
                        
                        <button class="btn btn-lg btn-primary btn-block mt-4" type="submit" name="book_session" id="submitBookingButton">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar-check-fill mr-2" viewBox="0 0 16 16"><path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zm-5.146-5.146-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708.708z"/></svg>
                            Pesan Sesi Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 bg-light border-top mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Layanan Penyewaan Olahraga.</p>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hargaLapanganPerSesi = parseFloat(document.getElementById('field_price_per_session').value) || 0;
    
    const tanggalMainInput = document.getElementById('tanggal_main');
    const sesiContainer = document.getElementById('sesi-container');
    const hiddenSesiInput = document.getElementById('sesi_waktu_mulai');
    const itemTambahanInputs = document.querySelectorAll('.jumlah-tambahan-input');
    const payInput = document.getElementById('pay');
    const totalOutput = document.getElementById('total');
    const totalHiddenOutput = document.getElementById('total_biaya_hidden');
    const kembaliOutput = document.getElementById('kembali');
    const errorSesi = document.getElementById('error-sesi');
    const bookingForm = document.getElementById('bookingFormWithSession');
    const submitBookingButton = document.getElementById('submitBookingButton');

    let sesiTerpilih = null; 
    let hargaSesiAktif = 0;
    let numerikTotalKeseluruhan = 0;


    function formatRupiahJS(angka) {
        if (isNaN(angka) || angka === null) return "Rp 0";
        return "Rp " + parseFloat(angka).toLocaleString('id-ID');
    }

    function updateBiayaTotal() {
        numerikTotalKeseluruhan = 0;
        if (sesiTerpilih) {
            numerikTotalKeseluruhan += hargaLapanganPerSesi; 
        }
        
        itemTambahanInputs.forEach(function(input) {
            const jumlah = parseInt(input.value) || 0;
            const hargaSatuan = parseFloat(input.getAttribute('data-harga_item')) || 0;
            numerikTotalKeseluruhan += jumlah * hargaSatuan;
        });
        
        totalOutput.value = formatRupiahJS(numerikTotalKeseluruhan);
        if(totalHiddenOutput) totalHiddenOutput.value = numerikTotalKeseluruhan;
        updateKembalian();
    }

    function updateKembalian() {
        const jumlahBayar = parseFloat(payInput.value) || 0;
        if (!isNaN(jumlahBayar) && jumlahBayar > 0) {
            if (jumlahBayar >= numerikTotalKeseluruhan) {
                kembaliOutput.value = formatRupiahJS(jumlahBayar - numerikTotalKeseluruhan);
            } else {
                kembaliOutput.value = "Bayar kurang";
            }
        } else {
            kembaliOutput.value = "Rp 0";
        }
    }

    if (tanggalMainInput) {
        tanggalMainInput.addEventListener('change', function() {
            const selectedDateValue = this.value;
            if (selectedDateValue) {
                window.location.href = `field_detail.php?ID_field=<?php echo $id_field; ?>&tanggal_main=${selectedDateValue}`;
            } else {
                if(sesiContainer) sesiContainer.innerHTML = '<p class="text-muted"><i>Pilih tanggal terlebih dahulu untuk melihat sesi yang tersedia.</i></p>';
                if(hiddenSesiInput) hiddenSesiInput.value = '';
                sesiTerpilih = null;
                updateBiayaTotal();
            }
        });
    }

    const sessionButtons = sesiContainer.querySelectorAll('.session-btn:not(.disabled)');
    sessionButtons.forEach(button => {
        button.addEventListener('click', function() {
            sessionButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            sesiTerpilih = this.getAttribute('data-sesi-val');
            if(hiddenSesiInput) hiddenSesiInput.value = sesiTerpilih;
            if(errorSesi) errorSesi.style.display = 'none';
            updateBiayaTotal();
        });
    });
    
    itemTambahanInputs.forEach(function(input) {
        input.addEventListener('input', updateBiayaTotal);
    });

    if(payInput) payInput.addEventListener('input', updateKembalian);

    const activeSessionButton = sesiContainer.querySelector('.session-btn.active');
    if (activeSessionButton) {
        sesiTerpilih = activeSessionButton.getAttribute('data-sesi-val');
        if(hiddenSesiInput) hiddenSesiInput.value = sesiTerpilih;
    }
    updateBiayaTotal(); 

    if (bookingForm) {
        bookingForm.addEventListener('submit', function(event) {
            if (<?php echo json_encode(!empty($selected_date)); ?> && !hiddenSesiInput.value) {
                if(errorSesi) errorSesi.style.display = 'block';
                event.preventDefault(); 
                alert('Harap pilih salah satu sesi waktu terlebih dahulu.');
            }
            if (parseFloat(payInput.value) < numerikTotalKeseluruhan) {
                event.preventDefault();
                alert('Jumlah bayar kurang dari total biaya. Mohon periksa kembali.');
            }
        });
    }
    
    window.setTimeout(function() {
        let alerts = document.querySelectorAll('.alert-container-form .alert');
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