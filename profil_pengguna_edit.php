<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user") {
    header("location:login.php");
    exit;
}

// Variabel untuk nama pengguna dari session, digunakan untuk tampilan di navbar dan judul
$username_display_session = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna';
$user_id_session = $_SESSION['user_id']; // ID pengguna yang sedang login

$user_data_for_form = null;
$fetch_data_error = '';
$current_foto_profil = null; // Akan diisi dengan nama file foto dari DB

// Mengambil data pengguna saat ini dari database untuk mengisi form
$stmt_fetch_current_user = mysqli_prepare($db, "SELECT Nama, Email, FotoProfil FROM users WHERE ID = ?");
if ($stmt_fetch_current_user) {
    mysqli_stmt_bind_param($stmt_fetch_current_user, "i", $user_id_session);
    mysqli_stmt_execute($stmt_fetch_current_user);
    $result_user = mysqli_stmt_get_result($stmt_fetch_current_user);
    if ($result_user && mysqli_num_rows($result_user) == 1) {
        $user_data_for_form = mysqli_fetch_assoc($result_user);
        $current_foto_profil = $user_data_for_form['FotoProfil']; // Ambil nama foto dari DB
    } else {
        $fetch_data_error = "Gagal mengambil data profil Anda untuk diedit.";
    }
    mysqli_stmt_close($stmt_fetch_current_user);
} else {
    $fetch_data_error = "Terjadi kesalahan dalam persiapan mengambil data profil: " . mysqli_error($db);
}

// Data yang akan diisikan ke form, prioritaskan dari DB, fallback ke session jika query gagal
$current_nama_form = $user_data_for_form['Nama'] ?? $username_display_session;
$current_email_form = $user_data_for_form['Email'] ?? '';
$default_foto_path = 'https://i.pinimg.com/736x/28/ff/49/28ff492b09cd297ae2f16ff9371b17d4.jpg'; // Foto default
$foto_display_path = (!empty($current_foto_profil) && file_exists('./img_profile/' . $current_foto_profil)) ? './img_profile/' . htmlspecialchars($current_foto_profil) : $default_foto_path;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - <?php echo $username_display_session; ?></title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .navbar-brand-custom { font-weight: bold; }
        .form-edit-container { 
            max-width: 700px; 
            margin: 2rem auto; 
            background-color: #fff; 
            padding: 2.5rem; 
            border-radius: .75rem; 
            box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.08);
        }
        .form-edit-container h3 { 
            margin-bottom: 2rem; 
            text-align:center; 
            color:#007bff; 
            font-weight:bold; 
        }
        .profile-preview-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .profile-preview-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #dee2e6;
            margin-bottom: 0.5rem;
        }
        .custom-file-label::after { content: "Pilih berkas"; }
        .alert-container-profile-edit { max-width: 700px; margin: 1rem auto 0 auto; }
        .password-note { font-size: 0.85rem; color: #6c757d; }
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
        <li class="nav-item"><a class="nav-link" href="history.php">Riwayat</a></li>
      </ul>
      <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo $username_display_session; ?> 
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

<div class="container">
    <div class="form-edit-container">
        <div class="text-center">
             <h3>Edit Profil Anda</h3>
        </div>

        <div class="alert-container-profile-edit">
            <?php
            if (!empty($fetch_data_error)) {
                 echo '<div class="alert alert-warning">' . $fetch_data_error . '</div>';
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

        <?php if ($user_data_for_form): ?>
        <form action="proses_update_profil_pengguna.php" method="POST" id="editProfilPenggunaForm" enctype="multipart/form-data">
            <input type="hidden" name="user_id_to_edit" value="<?php echo htmlspecialchars($user_id_session); ?>">
            <input type="hidden" name="foto_profil_lama" value="<?php echo htmlspecialchars($current_foto_profil ?? ''); ?>">

            <div class="profile-preview-container">
                <img src="<?php echo $foto_display_path; ?>" alt="Foto Profil Saat Ini" id="fotoProfilPreview" class="profile-preview-img">
            </div>
            
            <div class="form-group">
                <label for="foto_profil_baru">Ganti Foto Profil (Opsional)</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="foto_profil_baru" name="foto_profil_baru" accept="image/jpeg, image/png, image/gif">
                    <label class="custom-file-label" for="foto_profil_baru" data-browse="Pilih">Pilih berkas baru...</label>
                </div>
                <small class="form-text text-muted">Format: JPG, PNG, GIF. Ukuran maks: 1MB. Biarkan kosong jika tidak ingin mengganti.</small>
            </div>
            
            <div class="form-group">
                <label for="nama_pengguna">Nama Lengkap / Username</label>
                <input type="text" class="form-control" id="nama_pengguna" name="nama_pengguna" value="<?php echo htmlspecialchars($current_nama_form); ?>" required>
            </div>

            <div class="form-group">
                <label for="email_pengguna">Alamat Email</label>
                <input type="email" class="form-control" id="email_pengguna" name="email_pengguna" value="<?php echo htmlspecialchars($current_email_form); ?>" required>
            </div>
            
            <hr class="my-4">
            <p class="text-muted"><strong>Ubah Password (Opsional)</strong><br>
            <span class="password-note">Kosongkan field password jika Anda tidak ingin mengubah password Anda saat ini. Jika ingin mengubah, isi semua field password di bawah.</span></p>

            <div class="form-group">
                <label for="password_saat_ini_user">Password Saat Ini</label>
                <input type="password" class="form-control" id="password_saat_ini_user" name="password_saat_ini_user" placeholder="Masukkan password saat ini untuk verifikasi">
                <small class="form-text text-muted">Wajib diisi jika ingin mengubah password.</small>
            </div>

            <div class="form-group">
                <label for="password_baru_user">Password Baru</label>
                <input type="password" class="form-control" id="password_baru_user" name="password_baru_user" placeholder="Minimal 6 karakter">
            </div>

            <div class="form-group">
                <label for="konfirmasi_password_baru_user">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="konfirmasi_password_baru_user" name="konfirmasi_password_baru_user" placeholder="Ketik ulang password baru">
            </div>
            
            <hr class="my-4">
            <button type="submit" name="update_profil_pengguna" class="btn btn-primary btn-block">Simpan Perubahan</button>
            <a href="profil_pengguna.php" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
        </form>
        <?php elseif(empty($fetch_data_error)): ?>
            <div class="alert alert-warning">Tidak dapat memuat form edit profil saat ini. Silakan coba lagi nanti.</div>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center py-4 bg-light border-top mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Penyewaan Olahraga</p>
    </div>
</footer>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var customFileInput = document.getElementById('foto_profil_baru');
        if (customFileInput) {
            customFileInput.addEventListener('change', function(e){
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih berkas baru...';
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;

                if (e.target.files && e.target.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        document.getElementById('fotoProfilPreview').setAttribute('src', event.target.result);
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        }

        window.setTimeout(function() {
            let alerts = document.querySelectorAll('.alert-container-profile-edit .alert');
            alerts.forEach(function(alert) {
                $(alert).fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            });
        }, 5000);

        const formEditProfilPengguna = document.getElementById('editProfilPenggunaForm'); 
        if (formEditProfilPengguna) {
            formEditProfilPengguna.addEventListener('submit', function(event) {
                const passwordBaru = document.getElementById('password_baru_user').value;
                const konfirmasiPasswordBaru = document.getElementById('konfirmasi_password_baru_user').value;
                const passwordSaatIni = document.getElementById('password_saat_ini_user').value;
                const alertContainer = document.querySelector('.alert-container-profile-edit');
                
                let existingAlert = alertContainer.querySelector('.alert-password-match-edit-user');
                if (existingAlert) {
                    existingAlert.remove();
                }

                function displayProfileUserEditError(message) {
                    let alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show alert-password-match-edit-user';
                    alertDiv.setAttribute('role', 'alert');
                    alertDiv.innerHTML = message +
                                         '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                         '<span aria-hidden="true">&times;</span></button>';
                    
                    let currentAlert = alertContainer.querySelector('.alert-password-match-edit-user');
                    if (currentAlert) {
                        currentAlert.remove();
                    }
                    alertContainer.prepend(alertDiv); 
                    window.setTimeout(function() {
                        $(alertDiv).fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove(); 
                        });
                    }, 5000);
                }

                if (passwordBaru || konfirmasiPasswordBaru) { 
                    if (!passwordSaatIni) {
                         event.preventDefault();
                         displayProfileUserEditError('Password saat ini wajib diisi untuk mengubah password.');
                         return;
                    }
                    if (passwordBaru !== konfirmasiPasswordBaru) {
                        event.preventDefault();
                        displayProfileUserEditError('Password baru dan konfirmasi password baru tidak cocok!');
                        return;
                    }
                    if (passwordBaru && passwordBaru.length > 0 && passwordBaru.length < 6) { 
                         event.preventDefault();
                         displayProfileUserEditError('Password baru minimal harus 6 karakter.');
                         return;
                    }
                }
            });
        }
    });
</script>
</body>
</html>