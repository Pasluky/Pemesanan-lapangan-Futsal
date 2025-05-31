<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_id_session = $_SESSION['user_id']; // Menggunakan user_id dari session sebagai ID admin
$adminFullNameSession = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator';

$admin_data = null;
$error_message_fetch = '';

$stmt_fetch = mysqli_prepare($db, "SELECT Nama, Email FROM users WHERE ID = ? AND Level = 'admin'");
if ($stmt_fetch) {
    mysqli_stmt_bind_param($stmt_fetch, "i", $admin_id_session);
    mysqli_stmt_execute($stmt_fetch);
    $result_admin = mysqli_stmt_get_result($stmt_fetch);
    if ($result_admin && mysqli_num_rows($result_admin) == 1) {
        $admin_data = mysqli_fetch_assoc($result_admin);
    } else {
        $error_message_fetch = "Gagal mengambil data profil admin.";
    }
    mysqli_stmt_close($stmt_fetch);
} else {
    $error_message_fetch = "Gagal menyiapkan statement untuk mengambil data admin: " . mysqli_error($db);
}

if (!$admin_data && empty($error_message_fetch)) {
     $error_message_fetch = "Data profil admin tidak ditemukan.";
}

$current_nama = $admin_data['Nama'] ?? $adminFullNameSession;
$current_email = $admin_data['Email'] ?? '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Admin - <?php echo $adminFullNameSession; ?></title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { font-weight: bold; }
        .form-container { 
            max-width: 650px; 
            margin: 2rem auto; 
            background-color: #fff; 
            padding: 2.5rem; 
            border-radius: .75rem; 
            box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.08);
        }
        .form-container h3 { 
            margin-bottom: 1.5rem; 
            text-align:center; 
            color:#343a40; 
            font-weight:bold; 
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.75rem;
            display: inline-block;
        }
        .form-control { border-radius: .25rem; }
        .btn-block { padding: .75rem; font-size: 1.05rem;}
        .btn-outline-secondary { border-color: #6c757d; color: #6c757d;}
        .btn-outline-secondary:hover { background-color: #6c757d; color: #fff;}
        .alert-container { max-width: 650px; margin: 1rem auto 0 auto; }
        .password-note { font-size: 0.85rem; color: #6c757d; }
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
      <span class="navbar-text mr-3 text-light">Halo, <?php echo $adminFullNameSession; ?>!</span>
      <a class="btn btn-outline-danger btn-sm" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="form-container">
        <div class="text-center">
             <h3>Edit Profil Anda</h3>
        </div>

        <div class="alert-container">
            <?php
            if (!empty($error_message_fetch)) {
                 echo '<div class="alert alert-warning">' . $error_message_fetch . ' Kembali ke <a href="profil_admin.php">profil</a>.</div>';
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

        <?php if ($admin_data): ?>
        <form action="admin_proses_update_profil.php" method="POST">
            <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin_id_session); ?>">
            
            <div class="form-group">
                <label for="nama_admin">Nama Lengkap / Username</label>
                <input type="text" class="form-control" id="nama_admin" name="nama_admin" value="<?php echo htmlspecialchars($current_nama); ?>" required>
            </div>

            <div class="form-group">
                <label for="email_admin">Alamat Email</label>
                <input type="email" class="form-control" id="email_admin" name="email_admin" value="<?php echo htmlspecialchars($current_email); ?>" required>
            </div>
            
            <hr class="my-4">
            <p class="text-muted"><strong>Ubah Password (Opsional)</strong><br>
            <span class="password-note">Kosongkan field password jika Anda tidak ingin mengubah password Anda saat ini. Jika ingin mengubah, isi semua field password di bawah.</span></p>

            <div class="form-group">
                <label for="password_saat_ini">Password Saat Ini</label>
                <input type="password" class="form-control" id="password_saat_ini" name="password_saat_ini" placeholder="Masukkan password saat ini untuk verifikasi">
                <small class="form-text text-muted">Wajib diisi jika ingin mengubah password.</small>
            </div>

            <div class="form-group">
                <label for="password_baru">Password Baru</label>
                <input type="password" class="form-control" id="password_baru" name="password_baru" placeholder="Masukkan password baru">
            </div>

            <div class="form-group">
                <label for="konfirmasi_password_baru">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="konfirmasi_password_baru" name="konfirmasi_password_baru" placeholder="Ketik ulang password baru">
            </div>
            
            <hr class="my-4">
            <button type="submit" name="update_profil_admin" class="btn btn-primary btn-block">Simpan Perubahan Profil</button>
            <a href="profil_admin.php" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
        </form>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center py-4 bg-dark text-light mt-5">
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

    const formEditProfil = document.getElementById('updateProfilForm'); 
    if (formEditProfil) {
        formEditProfil.addEventListener('submit', function(event) {
            const passwordBaru = document.getElementById('password_baru').value;
            const konfirmasiPasswordBaru = document.getElementById('konfirmasi_password_baru').value;
            const passwordSaatIni = document.getElementById('password_saat_ini').value;
            const alertContainer = document.querySelector('.alert-container');
            
            let existingAlert = alertContainer.querySelector('.alert-password-match-edit');
            if (existingAlert) {
                existingAlert.remove();
            }

            if (passwordBaru || konfirmasiPasswordBaru || passwordSaatIni) {
                if (!passwordSaatIni) {
                     event.preventDefault();
                     displayProfileEditError('Password saat ini wajib diisi untuk mengubah password.');
                     return;
                }
                if (passwordBaru !== konfirmasiPasswordBaru) {
                    event.preventDefault();
                    displayProfileEditError('Password baru dan konfirmasi password baru tidak cocok!');
                    return;
                }
                if (passwordBaru && passwordBaru.length < 6) { 
                     event.preventDefault();
                     displayProfileEditError('Password baru minimal harus 6 karakter.');
                     return;
                }
            }
        });
    }

    function displayProfileEditError(message) {
        const alertContainer = document.querySelector('.alert-container');
        let alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show alert-password-match-edit';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = message +
                             '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                             '<span aria-hidden="true">&times;</span></button>';
        
        let existingAlert = alertContainer.querySelector('.alert-password-match-edit');
        if (existingAlert) {
            existingAlert.remove();
        }
        alertContainer.prepend(alertDiv); 
        window.setTimeout(function() {
            $(alertDiv).fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 5000);
    }
</script>
</body>
</html>