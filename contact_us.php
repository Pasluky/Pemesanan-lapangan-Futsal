<?php
session_start();
require 'config.php';

$is_logged_in = isset($_SESSION['Level']) && $_SESSION['Level'] == "user";
$username_session = $is_logged_in ? ($_SESSION['username'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - Penyewaan Olahraga</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
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
            display: flex;
            align-items: center;
        }
        .navbar-brand-custom svg {
            margin-right: 8px;
        }
        .page-header-contact {
            background-color: #007bff; 
            color: white;
            padding: 3rem 1.5rem;
            margin-bottom: 3rem;
            text-align: center;
            border-radius: .3rem;
        }
        .page-header-contact h1 {
            font-weight: bold;
            font-size: 2.8rem;
        }
        .page-header-contact .lead {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
        }
        .contact-info-card, .contact-form-card {
            background-color: #fff;
            border-radius: .5rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .contact-info-card h4, .contact-form-card h4 {
            font-weight: bold;
            color: #343a40;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #007bff;
            display: inline-block;
        }
        .contact-info-item {
            display: flex;
            align-items: flex-start; 
            margin-bottom: 1.25rem;
            font-size: 1rem;
        }
        .contact-info-item svg {
            width: 24px;
            height: 24px;
            margin-right: 15px;
            color: #007bff;
            flex-shrink: 0; 
            margin-top: 3px; 
        }
        .contact-info-item p {
            margin-bottom: 0.25rem;
            color: #495057;
        }
        .contact-info-item a {
            color: #0056b3;
            text-decoration: none;
        }
        .contact-info-item a:hover {
            text-decoration: underline;
        }
        .form-control {
            border-radius: .25rem;
            margin-bottom: 1rem;
        }
        .btn-submit-contact {
            font-weight: bold;
            padding: 0.6rem 1.5rem;
        }
        .footer {
            padding: 1.5rem 0;
            background-color: #343a40; 
            color: #adb5bd;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto; 
        }
        .map-responsive {
            overflow:hidden;
            padding-bottom:50%; 
            position:relative;
            height:0;
            border-radius: .5rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.08);
            margin-top: 1.5rem;
        }
        .map-responsive iframe{
            left:0;
            top:0;
            height:100%;
            width:100%;
            position:absolute;
            border:0;
        }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="dribbble-icon-brand-contact" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
    <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
  </symbol>
</svg>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="<?php echo $is_logged_in ? 'user_home.php' : 'index.php'; ?>">
        <svg width="24" height="24" fill="currentColor"><use xlink:href="#dribbble-icon-brand-contact"/></svg>
        Penyewaan Olahraga
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $is_logged_in ? 'user_home.php' : 'index.php'; ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="field_catalog.php">Sewa Lapangan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="peralatan_user.php">Sewa Peralatan</a>
        </li>
         <li class="nav-item active">
          <a class="nav-link" href="contact_us.php">Hubungi Kami <span class="sr-only">(current)</span></a>
        </li>
      </ul>
      <?php if ($is_logged_in): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo htmlspecialchars($username_session); ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="profil_pengguna.php">Profil Saya</a>
            <a class="dropdown-item" href="history.php">Riwayat Booking</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')">Keluar</a>
            </div>
        </div>
      <?php else: ?>
        <a class="btn btn-success my-2 my-sm-0" href="login.php" role="button">Masuk / Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="content-wrapper">
<main role="main">
    <header class="page-header-contact">
        <div class="container">
            <h1>Hubungi Kami</h1>
            <p class="lead">Kami senang mendengar dari Anda! Jangan ragu untuk menghubungi jika ada pertanyaan atau masukan.</p>
        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="contact-info-card h-100">
                    <h4>Informasi Kontak</h4>
                    <div class="contact-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
                        <div>
                            <p class="font-weight-bold mb-0">Alamat Kami:</p>
                            <p>Jl. Olahraga No. 123, Kota Sehat, Indonesia 45678</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/></svg>
                        <div>
                            <p class="font-weight-bold mb-0">Telepon:</p>
                            <p><a href="tel:+621234567890">+62 123 456 7890</a></p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16"><path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555zM0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757zm3.436-.586L16 11.801V4.697l-5.803 3.546z"/></svg>
                        <div>
                            <p class="font-weight-bold mb-0">Email:</p>
                            <p><a href="mailto:info@penyewaanolahraga.com">info@penyewaanolahraga.com</a></p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/></svg>
                        <div>
                            <p class="font-weight-bold mb-0">Jam Operasional CS:</p>
                            <p>Senin - Jumat: 08:00 - 17:00 WIB<br>Sabtu: 09:00 - 15:00 WIB</p>
                        </div>
                    </div>
                     <div class="map-responsive">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.010079023775!2d106.82715207488993!3d-6.262386793730378!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f231311aaab5%3A0x9025573854681634!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1717029602300!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="contact-form-card h-100">
                    <h4>Kirim Pesan Langsung</h4>
                    <form action="proses_kontak.php" method="POST">
                        <div class="form-group">
                            <label for="contact_nama">Nama Anda</label>
                            <input type="text" class="form-control" id="contact_nama" name="contact_nama" value="<?php echo $is_logged_in ? htmlspecialchars($username_session) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_email">Alamat Email Anda</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_subjek">Subjek Pesan</label>
                            <input type="text" class="form-control" id="contact_subjek" name="contact_subjek" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_pesan">Pesan Anda</label>
                            <textarea class="form-control" id="contact_pesan" name="contact_pesan" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="kirim_pesan_kontak" class="btn btn-primary btn-block btn-submit-contact">Kirim Pesan</button>
                    </form>
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
        let alerts = document.querySelectorAll('.alert-container .alert, .alert-container-form .alert');
        alerts.forEach(function(alert) {
            $(alert).fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        });
    }, 5000);
</script>
</body>
</html>