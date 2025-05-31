<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['Level']) || $_SESSION['Level'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$adminFullName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator';
$adminFoto = 'https://st3.depositphotos.com/9998432/13335/v/450/depositphotos_133352010-stock-illustration-default-placeholder-man-and-woman.jpg';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - <?php echo $adminFullName; ?></title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style type="text/css">
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 56px;
        }
        .content-wrapper {
            flex: 1;
            padding-top: 30px;
            padding-bottom: 30px;
        }
        .profile-card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: .75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .profile-img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 4px solid #007bff;
            padding: 3px;
            background-color: white;
        }
        .profile-card h3 {
            font-weight: bold;
            color: #343a40;
        }
        .profile-card .admin-role {
            color: #28a745;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .management-links .list-group-item {
            font-size: 1.05rem;
            padding-top: 15px;
            padding-bottom: 15px;
            border-left: 3px solid transparent;
            transition: all 0.2s ease-in-out;
            color: #495057;
        }
        .management-links .list-group-item:hover,
        .management-links .list-group-item.active {
            background-color: #e9f2ff;
            border-left-color: #007bff;
            color: #0056b3;
            font-weight: 500;
        }
        .management-links .list-group-item svg {
            margin-right: 10px;
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }
        .footer {
            padding: 1.5rem 0;
            background-color: #343a40;
            color: #adb5bd;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto;
        }
        .navbar-brand-custom {
            font-weight: bold;
        }
        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            margin-bottom: 2rem;
            color: #343a40;
            text-align: center;
        }
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
      <ul class="navbar-nav mr-auto">
        <li class="nav-item"><a class="nav-link" href="admin_home.php">Dashboard</a></li>
        <li class="nav-item active"><a class="nav-link" href="profil_admin.php">Profil Admin <span class="sr-only">(current)</span></a></li>
      </ul>
      <span class="navbar-text mr-3 text-light">
            Halo, <?php echo $adminFullName; ?>!
      </span>
      <a class="btn btn-outline-danger btn-sm" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
    </div>
  </div>
</nav>

<div class="content-wrapper">
<main role="main" class="container">
    <h2 class="page-title mt-4">Profil Administrator</h2>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="profile-card mb-4">
                <img src="<?php echo $adminFoto; ?>" alt="Foto Admin" class="profile-img">
                <h3 class="mb-1"><?php echo $adminFullName; ?></h3>
                <p class="admin-role">Administrator Sistem</p>
                <p class="text-muted small mb-3">
                    Selamat datang di panel admin. Anda dapat mengelola berbagai aspek sistem penyewaan melalui menu di bawah ini.
                </p>
                <a href="profil_admin_edit.php" class="btn btn-info btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square mr-1" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
                    Edit Profil Saya
                </a>
            </div>

            <div class="management-links">
                <h5 class="text-center mb-3 text-secondary">Akses Cepat Menu Pengelolaan:</h5>
                <div class="list-group shadow-sm">
                    <a href="datalapangan.php" class="list-group-item list-group-item-action">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-grid-1x2-fill" viewBox="0 0 16 16"><path d="M0 1a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V1zm9 0a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1V1zm0 9a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1V10z"/></svg>
                        Kelola Data Lapangan
                    </a>
                    <a href="laporan_keuangan.php" class="list-group-item list-group-item-action">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-bar-graph-fill" viewBox="0 0 16 16"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zm.5 10v-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-2.5.5a.5.5 0 0 1-.5-.5v-4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1zm-3 0a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-1z"/></svg>
                        Kelola Laporan Keuangan
                    </a>
                    <a href="peralatan.php" class="list-group-item list-group-item-action">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-tools" viewBox="0 0 16 16"><path d="M1 0L0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.356 3.356a1 1 0 0 0 1.414 0l1.586-1.586a1 1 0 0 0 0-1.414l-3.356-3.356a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96A3.005 3.005 0 0 0 13 3c0-.269-.035-.53-.102-.777l-2.14 2.141L12 4l-.354-.354l.353-.354a.5.5 0 0 0 0-.708L11.293.293a.5.5 0 0 0-.707 0L10.293 1L8.152.141A3.002 3.002 0 0 0 5.447 2.66L3.081 4.88l1.06-.914a1 1 0 0 1 .44-.29L4.58 3.5l2.404-2.404L6.142 0H1zm3.458 10.458a.5.5 0 1 1-.708.707l-1.293-1.293a.5.5 0 0 1 .707-.707l1.293 1.293zm-2.121 2.121a.5.5 0 1 1-.708.707L2.93 13.07a.5.5 0 0 1 .707-.707l1.293 1.293z"/></svg>
                        Kelola Peralatan 
                    </a>
                    <a href="riwayat_booking.php" class="list-group-item list-group-item-action">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
                        Riwayat Booking
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
</div>

<footer class="footer">
    <div class="container">
        <span>&copy; <?php echo date("Y"); ?> Panel Admin Penyewaan Olahraga. Hak Cipta Dilindungi.</span>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>

</body>
</html>