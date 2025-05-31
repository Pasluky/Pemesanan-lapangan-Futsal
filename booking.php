<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user") {
    header("location:login.php");
    exit;
}

$username_session = $_SESSION['username'];

$filter_tipe_lapangan = isset($_GET['tipe']) ? mysqli_real_escape_string($db, $_GET['tipe']) : 'semua';

$query_lapangan_sql = "SELECT ID, Nama, Tipe, Jenis, Harga, foto FROM lapangan WHERE status = 1";

if ($filter_tipe_lapangan != 'semua' && ($filter_tipe_lapangan == 'Indoor' || $filter_tipe_lapangan == 'Outdoor')) {
    $query_lapangan_sql .= " AND Tipe = '$filter_tipe_lapangan'";
}
$query_lapangan_sql .= " ORDER BY Nama ASC";

$result_lapangan = mysqli_query($db, $query_lapangan_sql);

if (!$result_lapangan) {
    die("Error mengambil data lapangan: " . mysqli_error($db));
}

function format_rupiah_booking($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Lapangan untuk Dipesan</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px; 
        }
        .navbar-brand-custom {
            font-weight: bold;
        }
        .page-header-booking {
            background: linear-gradient(to right, #007bff, #0056b3);
            color: white;
            padding: 2.5rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            border-radius: .3rem;
        }
        .page-header-booking h1 {
            font-weight: bold;
            font-size: 2.5rem;
        }
        .page-header-booking .lead {
            font-size: 1.15rem;
        }
        .filter-buttons .btn-filter-tipe { /* Class baru untuk tombol filter */
            margin-right: 5px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .field-card {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: .5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .field-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.4rem 1.2rem rgba(0,0,0,0.12);
        }
        .field-card .card-img-top {
            height: 200px; 
            object-fit: cover;
            border-top-left-radius: .45rem;
            border-top-right-radius: .45rem;
        }
        .field-card .card-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .field-card .card-title {
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 0.35rem;
            color: #343a40;
        }
        .field-card .field-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.75rem;
        }
        .field-card .field-price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #28a745;
            margin-top: auto; 
            margin-bottom: 0.75rem;
        }
        .field-card .btn-book {
            font-weight: bold;
        }
        .no-fields-alert {
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="dribbble-icon-brand-catalog" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
    <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
  </symbol>
</svg>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="<?php echo $is_logged_in && $_SESSION['Level'] == 'user' ? 'user_home.php' : 'index.php'; ?>">
        <svg width="24" height="24" fill="currentColor"><use xlink:href="#dribbble-icon-brand-catalog"/></svg>
        Penyewaan Olahraga
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $is_logged_in && $_SESSION['Level'] == 'user' ? 'user_home.php' : 'index.php'; ?>">Home</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="field_catalog.php">Sewa Lapangan <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="peralatan_user.php">Sewa Peralatan</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="about_us.php">Tentang Kami</a>
        </li>
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

<main role="main" class="container mt-4">
    <div class="page-header-booking">
        <h1>Pilih Lapangan Favorit Anda</h1>
        <p class="lead">Tentukan lapangan dan lihat sesi yang tersedia untuk memulai pemesanan.</p>
    </div>

    <div class="mb-4 filter-buttons text-center text-md-left">
        <p class="mb-2 d-block d-md-inline mr-md-2">Filter Tipe Lapangan:</p>
        <a href="booking.php?tipe=semua" class="btn btn-outline-primary btn-filter-tipe <?php if($filter_tipe_lapangan == 'semua') echo 'active'; ?>">Semua</a>
        <a href="booking.php?tipe=Indoor" class="btn btn-outline-primary btn-filter-tipe <?php if($filter_tipe_lapangan == 'Indoor') echo 'active'; ?>">Indoor</a>
        <a href="booking.php?tipe=Outdoor" class="btn btn-outline-primary btn-filter-tipe <?php if($filter_tipe_lapangan == 'Outdoor') echo 'active'; ?>">Outdoor</a>
    </div>

    <div class="row" id="field-list-container">
        <?php if (mysqli_num_rows($result_lapangan) > 0) : ?>
            <?php while ($row = mysqli_fetch_assoc($result_lapangan)) : ?>
            <div class="col-md-6 col-lg-4 mb-4 field-item">
                <div class="card field-card">
                    <img class="card-img-top" 
                         src="./img/<?php echo !empty($row['foto']) ? htmlspecialchars($row['foto']) : 'Garasi-Futsal.jpg'; ?>" 
                         alt="Gambar <?php echo htmlspecialchars($row['Nama']); ?>">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title"><?php echo htmlspecialchars($row['Nama']);?></h5>
                            <p class="field-meta">
                                <span class="badge badge-info mr-1"><?php echo htmlspecialchars($row['Tipe']);?></span>
                                <span class="badge badge-secondary"><?php echo htmlspecialchars($row['Jenis']);?></span>
                            </p>
                        </div>
                        <div>
                            <p class="field-price"><?php echo format_rupiah_booking($row['Harga']);?> / Sesi (2 Jam)</p>
                            <a href="field_detail.php?ID_field=<?php echo $row['ID']; ?>" class="btn btn-primary btn-block btn-book">
                                Pilih & Lihat Sesi
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle-fill ml-1" viewBox="0 0 16 16">
                                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning no-fields-alert">
                    <h4 class="alert-heading">Oops! Lapangan Tidak Ditemukan</h4>
                    <p>Saat ini belum ada lapangan yang terdaftar atau sesuai dengan filter Anda.</p>
                    <hr>
                    <p class="mb-0">Silakan cek kembali nanti atau coba filter yang berbeda.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <hr class="mt-4 mb-5">
</main>

<footer class="text-center py-4 bg-light border-top">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Layanan Penyewaan Olahraga. Semua Hak Dilindungi.</p>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js" ></script>
</body>
</html>