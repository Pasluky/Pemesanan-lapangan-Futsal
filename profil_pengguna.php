<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user") {
    header("location:login.php");
    exit;
}

$user_id_session = $_SESSION['user_id'];
$username_session = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna'; 

$user_data = null;
$latest_bookings = [];
$error_message_fetch = '';

$stmt_fetch_user = mysqli_prepare($db, "SELECT Nama, Email, FotoProfil FROM users WHERE ID = ?"); 
if ($stmt_fetch_user) {
    mysqli_stmt_bind_param($stmt_fetch_user, "i", $user_id_session);
    mysqli_stmt_execute($stmt_fetch_user);
    $result_user = mysqli_stmt_get_result($stmt_fetch_user);
    if ($result_user && mysqli_num_rows($result_user) == 1) {
        $user_data = mysqli_fetch_assoc($result_user);
    } else {
        $error_message_fetch = "Gagal mengambil data profil pengguna.";
    }
    mysqli_stmt_close($stmt_fetch_user);
} else {
    $error_message_fetch = "Gagal menyiapkan statement: " . mysqli_error($db);
}

$display_nama = $user_data['Nama'] ?? $username_session;
$display_email = $user_data['Email'] ?? 'Email tidak tersedia';

$userFotoDefault = 'https://i.pinimg.com/736x/28/ff/49/28ff492b09cd297ae2f16ff9371b17d4.jpg'; 
$userFoto = $userFotoDefault; // Set default
if (isset($user_data['FotoProfil']) && !empty($user_data['FotoProfil'])) {
    $path_foto_tersimpan = './img_profile/' . $user_data['FotoProfil'];
    if (file_exists($path_foto_tersimpan)) {
        $userFoto = htmlspecialchars($path_foto_tersimpan);
    }
}

$query_latest_bookings = "SELECT p.ID, p.Tanggal, p.Jam, p.status, l.Nama AS NamaLapangan 
                          FROM pesanan p
                          LEFT JOIN lapangan l ON p.ID_lapangan = l.ID
                          WHERE p.id_user = ?
                          ORDER BY p.Tanggal DESC, p.Jam DESC
                          LIMIT 3"; 
$stmt_latest_bookings = mysqli_prepare($db, $query_latest_bookings);
if($stmt_latest_bookings){
    mysqli_stmt_bind_param($stmt_latest_bookings, "i", $user_id_session);
    mysqli_stmt_execute($stmt_latest_bookings);
    $result_latest_bookings = mysqli_stmt_get_result($stmt_latest_bookings);
    while($row_booking = mysqli_fetch_assoc($result_latest_bookings)){
        $latest_bookings[] = $row_booking;
    }
    mysqli_stmt_close($stmt_latest_bookings);
}

function get_booking_status_text_profile($status_code) {
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
    <title>Profil Pengguna - <?php echo htmlspecialchars($display_nama); ?></title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style type="text/css">
        body {
            background-color: #f8f9fa;
            padding-top: 56px; 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1;
        }
        .navbar-brand-custom {
            font-weight: bold;
        }
        .profile-container {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .profile-header-card {
            background-color: #ffffff; 
            padding: 25px;
            border-radius: .5rem; 
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
            text-align: center;
        }
        .profile-img-display {
            width: 150px;
            height: 150px;
            object-fit: cover; 
            border-radius: 50%; 
            margin-bottom: 1rem;
            border: 4px solid #007bff;
            padding: 3px;
            background-color: white;
            display: block; 
            margin-left: auto;
            margin-right: auto; 
        }
        .profile-header-card .card-title {
            font-weight: bold;
            font-size: 1.75rem;
            color: #343a40;
        }
        .profile-header-card .text-muted {
            font-size: 1rem;
        }
        .info-card {
            background-color: #fff;
            border-radius: .5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        }
        .info-card .list-group-item {
            border-left: none;
            border-right: none;
            padding: 1rem 1.25rem;
            font-size: 0.95rem;
        }
        .info-card .list-group-item strong {
            color: #495057;
            min-width: 100px; 
            display: inline-block;
        }
        .footer {
            padding: 1.5rem 0;
            background-color: #e9ecef;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto; 
        }
         .alert-container {
            max-width: 800px; 
            margin: 1rem auto 0 auto;
        }
        .history-preview .list-group-item h5 {
            font-size: 1rem;
            font-weight: 500;
        }
         .history-preview .list-group-item p {
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }
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
        <li class="nav-item">
          <a class="nav-link" href="user_home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="booking.php">Booking</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="history.php">Riwayat</a>
        </li>
      </ul>
      <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo htmlspecialchars($username_session); ?> 
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item active" href="profil_pengguna.php">Profil Saya <span class="sr-only">(current)</span></a>
          <a class="dropdown-item" href="history.php">Riwayat Booking</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
        </div>
      </div>
    </div>
  </div>
</nav>

<div class="content-wrapper">
<main role="main" class="container profile-container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            <div class="alert-container">
                <?php
                if (!empty($error_message_fetch)) {
                     echo '<div class="alert alert-warning">' . $error_message_fetch . '</div>';
                }
                if (isset($_SESSION['success_message_profile'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
                         htmlspecialchars($_SESSION['success_message_profile']) .
                         '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    unset($_SESSION['success_message_profile']);
                }
                if (isset($_SESSION['error_message_profile'])) {
                     echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' .
                         htmlspecialchars($_SESSION['error_message_profile']) .
                         '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    unset($_SESSION['error_message_profile']);
                }
                ?>
            </div>

            <div class="card profile-header-card mb-4">
                <img src="<?php echo $userFoto; ?>" alt="Foto Profil <?php echo htmlspecialchars($display_nama); ?>" class="profile-img-display">
                <h2 class="card-title"><?php echo htmlspecialchars($display_nama); ?></h2>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($display_email); ?></p>
                <p class="card-text small text-secondary">Selamat datang di halaman profil Anda. Di sini Anda dapat melihat informasi akun dan mengelolanya.</p>
                <a href="profil_pengguna_edit.php" class="btn btn-primary btn-sm mt-3">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square mr-1" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
                    Edit Profil
                </a>
            </div>

            <div class="card info-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Informasi Akun Saya</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nama:</strong> <?php echo htmlspecialchars($display_nama); ?></li>
                        <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($display_email); ?></li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm history-preview">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Riwayat Sewa Terakhir</h4>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php if (!empty($latest_bookings)): ?>
                            <?php foreach($latest_bookings as $booking_item): 
                                $status_item_profile = get_booking_status_text_profile($booking_item['status']);
                                $nama_lapangan_profile = !empty($booking_item['NamaLapangan']) ? $booking_item['NamaLapangan'] : (!empty($booking_item['Nama']) ? $booking_item['Nama'] : 'Lapangan');
                            ?>
                            <a href="book_detail.php?ID_book=<?php echo $booking_item['ID']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($nama_lapangan_profile); ?></h5>
                                    <small class="text-muted"><?php echo htmlspecialchars(date('d M Y', strtotime($booking_item['Tanggal']))); ?></small>
                                </div>
                                <p class="mb-1">ID Booking: #<?php echo htmlspecialchars($booking_item['ID']); ?> - Pukul: <?php echo htmlspecialchars(date('H:i', strtotime($booking_item['Jam']))); ?> WIB</p>
                                <small class="badge badge-<?php echo $status_item_profile['badge']; ?>"><?php echo htmlspecialchars($status_item_profile['text']); ?></small>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="list-group-item text-muted">Belum ada riwayat sewa.</p>
                        <?php endif; ?>
                        <a href="history.php" class="list-group-item list-group-item-action text-primary text-center font-weight-bold">
                            Lihat Semua Riwayat Sewa
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
</div>

<footer class="footer">
    <div class="container">
        <span>&copy; <?php echo date("Y"); ?> Penyewaan Olahraga. Semua Hak Dilindungi.</span>
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