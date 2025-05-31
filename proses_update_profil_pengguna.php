<?php
session_start();
require 'config.php';

function redirect_with_profile_message($url, $message, $type = 'error_message_profile') {
    $_SESSION[$type] = $message;
    header("Location: " . $url);
    exit;
}

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user" || !isset($_SESSION['user_id'])) {
    redirect_with_profile_message("login.php", "Sesi tidak valid atau Anda tidak memiliki hak akses.");
}

$logged_in_user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profil_pengguna'])) {

    if (isset($_POST['user_id_to_edit'], $_POST['nama_pengguna'], $_POST['email_pengguna'])) {

        $user_id_from_form = mysqli_real_escape_string($db, $_POST['user_id_to_edit']);
        $nama_pengguna_baru = mysqli_real_escape_string($db, trim($_POST['nama_pengguna']));
        $email_pengguna_baru = mysqli_real_escape_string($db, trim($_POST['email_pengguna']));
        $foto_profil_lama = $_POST['foto_profil_lama'] ?? null;

        $password_saat_ini = $_POST['password_saat_ini_user'];
        $password_baru = $_POST['password_baru_user'];
        $konfirmasi_password_baru = $_POST['konfirmasi_password_baru_user'];

        if ($user_id_from_form != $logged_in_user_id) {
            redirect_with_profile_message("profil_pengguna_edit.php", "Error: ID pengguna tidak cocok.");
        }

        if (empty($nama_pengguna_baru) || empty($email_pengguna_baru)) {
            redirect_with_profile_message("profil_pengguna_edit.php", "Nama dan Email tidak boleh kosong.");
        }

        if (!filter_var($email_pengguna_baru, FILTER_VALIDATE_EMAIL)) {
            redirect_with_profile_message("profil_pengguna_edit.php", "Format email tidak valid.");
        }

        $stmt_check_email = mysqli_prepare($db, "SELECT ID FROM users WHERE Email = ? AND ID != ?");
        mysqli_stmt_bind_param($stmt_check_email, "si", $email_pengguna_baru, $logged_in_user_id);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);

        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            redirect_with_profile_message("profil_pengguna_edit.php", "Email sudah digunakan oleh pengguna lain.");
        }
        mysqli_stmt_close($stmt_check_email);

        $kolom_update = [];
        $tipe_data_bind = "";
        $nilai_bind = [];

        $kolom_update[] = "Nama = ?";
        $tipe_data_bind .= "s";
        $nilai_bind[] = &$nama_pengguna_baru;

        $kolom_update[] = "Email = ?";
        $tipe_data_bind .= "s";
        $nilai_bind[] = &$email_pengguna_baru;

        $password_akan_diupdate = false;
        if (!empty($password_baru) || !empty($konfirmasi_password_baru) || !empty($password_saat_ini)) {
            if (empty($password_saat_ini)) {
                redirect_with_profile_message("profil_pengguna_edit.php", "Password saat ini wajib diisi untuk mengubah password.");
            }
            if (empty($password_baru)) {
                redirect_with_profile_message("profil_pengguna_edit.php", "Password baru tidak boleh kosong jika ingin mengubah password.");
            }
            if ($password_baru !== $konfirmasi_password_baru) {
                redirect_with_profile_message("profil_pengguna_edit.php", "Password baru dan konfirmasi password tidak cocok.");
            }
            if (strlen($password_baru) < 6) {
                 redirect_with_profile_message("profil_pengguna_edit.php", "Password baru minimal harus 6 karakter.");
            }

            $stmt_get_pass = mysqli_prepare($db, "SELECT Password FROM users WHERE ID = ?");
            mysqli_stmt_bind_param($stmt_get_pass, "i", $logged_in_user_id);
            mysqli_stmt_execute($stmt_get_pass);
            $result_pass = mysqli_stmt_get_result($stmt_get_pass);
            $user_current_pass_data = mysqli_fetch_assoc($result_pass);
            mysqli_stmt_close($stmt_get_pass);

            if (!$user_current_pass_data || $password_saat_ini !== $user_current_pass_data['Password']) {
                redirect_with_profile_message("profil_pengguna_edit.php", "Password saat ini yang Anda masukkan salah.");
            }
            
            $kolom_update[] = "Password = ?";
            $tipe_data_bind .= "s";
            $nilai_bind[] = &$password_baru; 
            $password_akan_diupdate = true;
        }

        $nama_file_foto_baru = $foto_profil_lama; 
        $target_dir_profile = "img_profile/"; 

        if (isset($_FILES['foto_profil_baru']) && $_FILES['foto_profil_baru']['error'] == UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['foto_profil_baru']['tmp_name'];
            $file_name_original = basename($_FILES['foto_profil_baru']['name']);
            $file_size = $_FILES['foto_profil_baru']['size'];
            $file_ext = strtolower(pathinfo($file_name_original, PATHINFO_EXTENSION));
            $allowed_extensions = array("jpeg", "jpg", "png", "gif");

            if (!in_array($file_ext, $allowed_extensions)) {
                redirect_with_profile_message("profil_pengguna_edit.php", "Format foto tidak diizinkan (hanya JPG, PNG, GIF).");
            }
            if ($file_size > 1048576) { 
                redirect_with_profile_message("profil_pengguna_edit.php", "Ukuran foto maksimal adalah 1MB.");
            }

            $nama_file_foto_unik = "user_" . $logged_in_user_id . "_" . time() . "." . $file_ext;
            $target_file_upload = $target_dir_profile . $nama_file_foto_unik;

            if (move_uploaded_file($file_tmp, $target_file_upload)) {
                if (!empty($foto_profil_lama) && $foto_profil_lama != 'default.png' && file_exists($target_dir_profile . $foto_profil_lama)) {
                    unlink($target_dir_profile . $foto_profil_lama);
                }
                $nama_file_foto_baru = $nama_file_foto_unik;
                $kolom_update[] = "FotoProfil = ?";
                $tipe_data_bind .= "s";
                $nilai_bind[] = &$nama_file_foto_baru;
            } else {
                redirect_with_profile_message("profil_pengguna_edit.php", "Gagal mengupload foto profil baru.");
            }
        } elseif (isset($_FILES['foto_profil_baru']) && $_FILES['foto_profil_baru']['error'] != UPLOAD_ERR_NO_FILE) {
            redirect_with_profile_message("profil_pengguna_edit.php", "Terjadi kesalahan saat mengupload foto: error code " . $_FILES['foto_profil_baru']['error']);
        }
        
        $nilai_bind[] = &$logged_in_user_id; 
        $tipe_data_bind .= "i";

        $query_update_sql = "UPDATE users SET " . implode(", ", $kolom_update) . " WHERE ID = ?";
        
        $stmt_update_profile = mysqli_prepare($db, $query_update_sql);
        
        if ($stmt_update_profile) {
            mysqli_stmt_bind_param($stmt_update_profile, $tipe_data_bind, ...$nilai_bind);
            
            if (mysqli_stmt_execute($stmt_update_profile)) {
                $_SESSION['username'] = $nama_pengguna_baru; 
                redirect_with_profile_message("profil_pengguna.php", "Profil berhasil diperbarui.", 'success_message_profile');
            } else {
                redirect_with_profile_message("profil_pengguna_edit.php", "Gagal memperbarui profil: " . mysqli_stmt_error($stmt_update_profile));
            }
            mysqli_stmt_close($stmt_update_profile);
        } else {
             redirect_with_profile_message("profil_pengguna_edit.php", "Gagal menyiapkan statement update profil: " . mysqli_error($db));
        }

    } else {
        redirect_with_profile_message("profil_pengguna_edit.php", "Data profil tidak lengkap.");
    }
} else {
    header("Location: profil_pengguna.php");
    exit();
}
?>