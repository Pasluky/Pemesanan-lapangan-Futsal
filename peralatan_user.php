<?php
session_start();
require 'config.php'; 

$is_logged_in = isset($_SESSION['Level']) && isset($_SESSION['username']);
$username_session = ''; 
if ($is_logged_in) {
    if ($_SESSION['Level'] == 'user') {
        $username_session = htmlspecialchars($_SESSION['username']);
    } elseif ($_SESSION['Level'] == 'admin') {
        $username_session = htmlspecialchars($_SESSION['username']) . " (Admin)";
    }
}

$query_tambahan = "SELECT ID, Nama, Harga FROM tambahan ORDER BY Nama ASC";
$result_tambahan = mysqli_query($db, $query_tambahan);

if (!$result_tambahan) {
    die("Error mengambil data peralatan tambahan: " . mysqli_error($db));
}

function format_rupiah_peralatan($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

function get_icon_for_item($nama_item) {
    $nama_item_lower = strtolower($nama_item);
    if (strpos($nama_item_lower, 'sepatu') !== false) {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-subtract" viewBox="0 0 16 16">
                  <path d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v1.5a.5.5 0 0 1-.5.5H14a.5.5 0 0 1-.5-.5V6H3v1.5a.5.5 0 0 1-.5.5H1a.5.5 0 0 1-.5-.5V6a2 2 0 0 1 2-2h1V2zm2 4.5A1.5 1.5 0 0 1 3.5 5h5A1.5 1.5 0 0 1 10 6.5V8a.5.5 0 0 1-.42.49L7.994 8.804a.5.5 0 0 1-.39.044l-.732-.368a.5.5 0 0 1-.263-.639A1.5 1.5 0 0 0 5.5 7h-2A1.5 1.5 0 0 1 2 8.5v2A1.5 1.5 0 0 1 3.5 12H12v1.5a.5.5 0 0 1-1 0V12H3.5a2.5 2.5 0 0 1-2.5-2.5v-2A2.5 2.5 0 0 1 3.5 5H5V4H3.5a1.5 1.5 0 0 0-1.5 1.5v.5zM12 11.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                </svg>'; 
    } elseif (strpos($nama_item_lower, 'kostum') !== false || strpos($nama_item_lower, 'rompi') !== false) {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-badge" viewBox="0 0 16 16">
                  <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                  <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0h-7zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492c-.655-.46-.86-.78-.86-1.238V5.277a.5.5 0 0 0-.5-.5h-1.5a.5.5 0 0 0-.5.5v4.083c0 .394.208.724.776 1.083.261.164.47.325.64.493V14a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 3 14V2.5z"/>
                </svg>'; 
    } elseif (strpos($nama_item_lower, 'bola') !== false) {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-dribbble" viewBox="0 0 16 16"><use xlink:href="#dribbble-icon-path-nav-peralatan"/></svg>';
    } elseif (strpos($nama_item_lower, 'glove') !== false || strpos($nama_item_lower, 'sarung tangan') !== false) {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-hand-index-thumb" viewBox="0 0 16 16">
                  <path d="M6.75 1a.75.75 0 0 1 .75.75V8a.5.5 0 0 0 1 0V5.467l.086-.004c.317-.012.637-.008.916.024.264.03.503.09.706.193.27.138.57.319.948.569.414.277.842.569 1.219.909.342.309.614.64.772.933.218.4.319.868.319 1.376 0 .762-.209 1.432-.526 1.952C13.009 12.72 12.301 13 11.25 13H8.705L7.25 14.46c-.275.275-.72.275-1 0L4.93 13.074a.51.51 0 0 1-.014-.018l-.224-.243L1.6 9.483C1.032 8.732 1 7.808 1 6.842c0-1.801.823-2.811 2.409-3.35L5.07 2.93zm0 1.5L4.223 4H2.717C1.642 4.384 1 5.383 1 6.842c0 .758.203 1.412.595 1.975l2.725 3.271L6.75 13.995V2.5zm.923 1.016a.5.5 0 0 0-.846-.33L5.723 4.85C5.349 5.38 5.006 6.028 5.006 6.842a3.45 3.45 0 0 0 .18.996l.208.734.004.014A.504.504 0 0 0 6 9.202V13.5a.5.5 0 0 0 .5.5h1.727l1.59-1.743A1.51 1.51 0 0 0 10 11.507V10.25a.5.5 0 0 0-1 0v1.257a.51.51 0 0 1-.006.07L8.96 12.5H8.75a.5.5 0 0 0-.5.5V13a.5.5 0 0 0 .5.5h2.5c.84 0 1.486-.36 1.837-.918.351-.558.513-1.262.513-2.029 0-.582-.132-1.121-.388-1.629a3.163 3.163 0 0 0-.872-.964 4.204 4.204 0 0 0-1.185-.841 5.075 5.075 0 0 0-.847-.37Z"/>
                </svg>'; 
    }
    return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
              <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
            </svg>';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Peralatan Tambahan - Penyewaan Olahraga</title>
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
        .page-header-equipment {
            background: linear-gradient(to right, #17a2b8, #138496); 
            color: white;
            padding: 2.5rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            border-radius: .3rem;
        }
        .page-header-equipment h1 {
            font-weight: bold;
            font-size: 2.5rem;
        }
        .page-header-equipment .lead {
            font-size: 1.15rem;
        }
        .equipment-item-card {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: .5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            height: 100%; 
            text-align: center; 
        }
        .equipment-item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.4rem 1.2rem rgba(0,0,0,0.12);
        }
        .equipment-item-card .card-body {
            padding: 1.5rem; 
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            justify-content: space-between;
        }
        .equipment-item-card .icon-placeholder {
            font-size: 3rem; 
            color: #6c757d; 
            margin-bottom: 1rem;
            line-height: 1; /* Agar ikon SVG tidak menambah tinggi berlebih */
        }
        .equipment-item-card .card-title {
            font-weight: bold;
            font-size: 1.15rem;
            margin-bottom: 0.5rem;
            color: #343a40;
        }
        .equipment-item-card .item-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 1rem;
        }
        .info-text {
            background-color: #e9ecef;
            padding: 1rem;
            border-radius: .3rem;
            margin-top: 2rem;
            text-align: center;
            font-size: 0.95rem;
        }
         .no-items-alert {
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="dribbble-icon-path-nav-peralatan" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
    <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
  </symbol>
</svg>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="<?php echo $is_logged_in && isset($_SESSION['Level']) && $_SESSION['Level'] == 'user' ? 'user_home.php' : 'index.php'; ?>">
        <svg width="24" height="24" fill="currentColor"><use xlink:href="#dribbble-icon-path-nav-peralatan"/></svg>
        Penyewaan Olahraga
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $is_logged_in && isset($_SESSION['Level']) && $_SESSION['Level'] == 'user' ? 'user_home.php' : 'index.php'; ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="field_catalog.php">Sewa Lapangan</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="peralatan_user.php">Sewa Peralatan <span class="sr-only">(current)</span></a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="about_us.php">Tentang Kami</a>
        </li>
      </ul>
      <?php if ($is_logged_in && isset($_SESSION['Level']) && $_SESSION['Level'] == 'user'): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $username_session; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="profil_pengguna.php">Profil Saya</a>
            <a class="dropdown-item" href="history.php">Riwayat Booking</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
            </div>
        </div>
      <?php elseif ($is_logged_in && isset($_SESSION['Level']) && $_SESSION['Level'] == 'admin'): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $username_session; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="admin_home.php">Dashboard Admin</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
            </div>
        </div>
      <?php else: ?>
        <a class="btn btn-success my-2 my-sm-0" href="login.php" role="button">Masuk / Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main role="main" class="container mt-4">
    <div class="page-header-equipment">
        <h1>Katalog Peralatan Tambahan</h1>
        <p class="lead">Lengkapi kebutuhan olahraga Anda dengan pilihan peralatan kami.</p>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($result_tambahan) > 0) : ?>
            <?php while ($item = mysqli_fetch_assoc($result_tambahan)) : ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card equipment-item-card">
                    <div class="card-body">
                        <div class="icon-placeholder">
                            <?php echo get_icon_for_item($item['Nama']); ?>
                        </div>
                        <div>
                            <h5 class="card-title"><?php echo htmlspecialchars($item['Nama']);?></h5>
                            <p class="item-price"><?php echo format_rupiah_peralatan($item['Harga']);?> <span class="text-muted small">/ item</span></p>
                        </div>
                        <div>
                           <a href="field_catalog.php" class="btn btn-outline-primary btn-sm mt-2">Pilih Saat Booking Lapangan</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info no-items-alert">
                    <h4 class="alert-heading">Peralatan Belum Tersedia</h4>
                    <p>Saat ini belum ada peralatan tambahan yang terdaftar. Silakan cek kembali nanti.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="info-text">
        <p class="mb-0"><strong>Catatan:</strong> Semua peralatan tambahan di atas dapat dipilih dan ditambahkan saat Anda melakukan proses pemesanan lapangan.</p>
    </div>
    <hr class="mt-5 mb-4">
</main>

<footer class="text-center py-4 bg-light border-top">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Layanan Penyewaan Olahraga. Semua Hak Dilindungi.</p>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
</body>
</html>