<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user") {
    header("location:login.php");
    exit;
}

$username_session = htmlspecialchars($_SESSION['username']); 
$user_id_session = $_SESSION['user_id'];   

$query_conditions = " WHERE p.id_user = '$user_id_session'"; 
$search_date_value = ""; 

if (isset($_POST['date_filter_search'])) {
    $search_date = mysqli_real_escape_string($db, $_POST['date_filter_input']);
    $search_date_value = $search_date; 
    if (!empty($search_date)) {
        $query_conditions .= " AND p.Tanggal = '$search_date'";
    }
}

$query_history = "SELECT p.ID, p.Nama, p.Tanggal, p.Jam, p.status, 
                         l.Nama AS NamaLapanganDariJoin, l.foto AS GambarLapanganDisplay 
                  FROM pesanan p
                  LEFT JOIN lapangan l ON p.ID_lapangan = l.ID 
                  $query_conditions
                  ORDER BY p.Tanggal DESC, p.Jam DESC, p.ID DESC"; 

$result_history = mysqli_query($db, $query_history);

if (!$result_history) {
    die("Error pada query riwayat: " . mysqli_error($db)); 
}

function format_rupiah_history($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

function get_status_text_history($status_code) {
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
    <title>Riwayat Pesanan Anda - Penyewaan Olahraga</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px; 
        }
        .navbar-brand-custom {
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .navbar-brand-custom svg {
            margin-right: 8px;
        }
        .page-header-history {
            background-color: #6f42c1; 
            color: white;
            padding: 2rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            border-radius: .3rem;
        }
        .page-header-history h1 {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        .filter-container {
            background-color: #fff;
            padding: 1rem 1.5rem;
            border-radius: .3rem;
            margin-bottom: 2rem;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        }
        .history-card {
            background-color: #fff;
            border: 1px solid #e9ecef; 
            border-radius: .5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease-in-out;
            display: flex; 
            flex-direction: column;
            height: 100%; 
        }
        .history-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.4rem 1rem rgba(0,0,0,0.12);
        }
        .history-card .card-img-top-container {
            height: 180px; 
            overflow: hidden;
            border-top-left-radius: .5rem;
            border-top-right-radius: .5rem;
        }
        .history-card .card-img-top {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .history-card .card-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1; 
        }
        .history-card .card-title {
            font-weight: bold;
            font-size: 1.15rem; 
            margin-bottom: 0.5rem;
            color: #007bff; 
        }
        .history-card .booking-info {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.75rem;
        }
        .history-card .booking-info strong {
            color: #495057;
        }
        .history-card .booking-status .badge {
            font-size: 0.85rem;
            padding: .4em .75em; 
        }
        .history-card .card-footer {
            background-color: transparent;
            border-top: 1px solid #e9ecef;
            padding: 0.75rem 1.25rem;
            margin-top: auto; 
        }
        .history-card .btn-detail {
            font-weight: 500; 
        }
        .no-history-container {
            text-align: center;
            padding: 3rem 1.5rem;
            background-color: #fff;
            border-radius: .5rem;
            color: #6c757d;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="dribbble-icon-brand-history" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
    <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
  </symbol>
</svg>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="user_home.php">
        <svg width="24" height="24" fill="currentColor"><use xlink:href="#dribbble-icon-brand-history"/></svg>
        Penyewaan Olahraga
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="user_home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="field_catalog.php">Booking</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="history.php">Riwayat <span class="sr-only">(current)</span></a>
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
          <a class="dropdown-item" href="profil_pengguna.php">Profil Saya</a>
          <a class="dropdown-item" href="history.php">Riwayat Booking</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
        </div>
      </div>
    </div>
  </div>
</nav>

<main class="container mt-4">
    <div class="page-header-history">
        <h1>Riwayat Pesanan Anda</h1>
        <p class="lead">Semua detail transaksi penyewaan lapangan Anda ada di sini.</p>
    </div>

    <div class="filter-container">
        <form action="history.php" method="post" class="form-inline justify-content-center justify-content-md-start">
            <div class="form-group mb-2 mr-sm-2">
                <label for="date_filter_input" class="sr-only">Filter Tanggal</label>
                <input type="date" name="date_filter_input" id="date_filter_input" class="form-control form-control-sm" value="<?php echo htmlspecialchars($search_date_value); ?>">
            </div>
            <button type="submit" name="date_filter_search" class="btn btn-primary btn-sm mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search mr-1" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg>
                Cari
            </button>
            <?php if (!empty($search_date_value)): ?>
                <a href="history.php" class="btn btn-outline-secondary btn-sm mb-2 ml-sm-2">Reset Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($result_history) > 0) : ?>
            <?php while ($row = mysqli_fetch_assoc($result_history)) : 
                $status_item = get_status_text_history($row['status']);
                $nama_lapangan_hist = !empty($row['NamaLapanganDariJoin']) ? $row['NamaLapanganDariJoin'] : (!empty($row['Nama']) ? $row['Nama'] : 'Booking Lapangan');
                $gambar_lapangan_hist = !empty($row['GambarLapanganDisplay']) ? './img/' . htmlspecialchars($row['GambarLapanganDisplay']) : './img/Garasi-Futsal.jpg';
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card history-card">
                    <div class="card-img-top-container">
                        <img class="card-img-top" src="<?php echo $gambar_lapangan_hist; ?>" alt="Gambar <?php echo htmlspecialchars($nama_lapangan_hist); ?>">
                    </div>
                    <div class="card-body">
                        <div>
                            <h5 class="card-title"><?php echo htmlspecialchars($nama_lapangan_hist);?></h5>
                            <div class="booking-info">
                                <p class="mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-event mr-1" viewBox="0 0 16 16"><path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                                    <strong>Tanggal:</strong> <?php echo htmlspecialchars(date('d M Y', strtotime($row['Tanggal'])));?>
                                </p>
                                <p class="mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-fill mr-1" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/></svg>
                                    <strong>Waktu:</strong> <?php echo htmlspecialchars(date('H:i', strtotime($row['Jam'] ?? ($row['Waktu'] ?? '00:00')))); ?> WIB
                                </p>
                                <p class="mb-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill mr-1" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>
                                    <strong>ID Booking:</strong> #<?php echo htmlspecialchars($row['ID']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="booking-status"><span class="badge badge-<?php echo $status_item['badge']; ?>"><?php echo htmlspecialchars($status_item['text']); ?></span></span>
                        <a href="book_detail.php?ID_book=<?php echo $row['ID']; ?>" class="btn btn-success btn-sm btn-detail">
                            Lihat Detail
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-short ml-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="no-history-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-journal-richtext mb-3" viewBox="0 0 16 16">
                        <path d="M7.5 3.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm-.861 1.542 1.33.886 1.854-1.855a.25.25 0 0 1 .289-.047l1.888.974V7.5a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V7.02s.275-.222.686-.42L5.8 6.323a.25.25 0 0 1-.047-.289l.974-1.888a.25.25 0 0 1 .39-.086L7.5 4.685V3.75z"/>
                        <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z"/>
                        <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z"/>
                    </svg>
                    <h4>Belum Ada Riwayat Pesanan</h4>
                    <p>Anda belum melakukan pesanan apapun<?php echo !empty($search_date_value) ? ' untuk tanggal yang dipilih' : ''; ?>.</p>
                    <a href="booking.php" class="btn btn-primary mt-2">Cari Lapangan Sekarang</a>
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