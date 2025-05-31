<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profil_admin'])) {
    if (isset($_POST['admin_id'], $_POST['nama_admin'], $_POST['email_admin'])) {

        $admin_id = mysqli_real_escape_string($db, $_POST['admin_id']);
        $nama_admin_baru = mysqli_real_escape_string($db, trim($_POST['nama_admin']));
        $email_admin_baru = mysqli_real_escape_string($db, trim($_POST['email_admin']));

        $password_saat_ini = $_POST['password_saat_ini'];
        $password_baru = $_POST['password_baru'];
        $konfirmasi_password_baru = $_POST['konfirmasi_password_baru'];

        if (empty($nama_admin_baru) || empty($email_admin_baru)) {
            $_SESSION['error_message_profile'] = "Nama dan Email tidak boleh kosong.";
            header("Location: profil_admin_edit.php");
            exit();
        }

        if (!filter_var($email_admin_baru, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message_profile'] = "Format email tidak valid.";
            header("Location: profil_admin_edit.php");
            exit();
        }

        $stmt_check_email = mysqli_prepare($db, "SELECT ID FROM users WHERE Email = ? AND ID != ?");
        mysqli_stmt_bind_param($stmt_check_email, "si", $email_admin_baru, $admin_id);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);

        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $_SESSION['error_message_profile'] = "Email sudah digunakan oleh pengguna lain.";
            mysqli_stmt_close($stmt_check_email);
            header("Location: profil_admin_edit.php");
            exit();
        }
        mysqli_stmt_close($stmt_check_email);

        $update_password = false;
        if (!empty($password_baru) || !empty($konfirmasi_password_baru) || !empty($password_saat_ini) ) {
            if (empty($password_saat_ini)) {
                $_SESSION['error_message_profile'] = "Password saat ini wajib diisi jika ingin mengubah password.";
                header("Location: profil_admin_edit.php");
                exit();
            }
            if (empty($password_baru)) {
                $_SESSION['error_message_profile'] = "Password baru tidak boleh kosong jika ingin mengubah password.";
                header("Location: profil_admin_edit.php");
                exit();
            }
            if ($password_baru !== $konfirmasi_password_baru) {
                $_SESSION['error_message_profile'] = "Password baru dan konfirmasi password tidak cocok.";
                header("Location: profil_admin_edit.php");
                exit();
            }
            if (strlen($password_baru) < 6) { 
                 $_SESSION['error_message_profile'] = "Password baru minimal harus 6 karakter.";
                 header("Location: profil_admin_edit.php");
                 exit();
            }

            $stmt_get_current_password = mysqli_prepare($db, "SELECT Password FROM users WHERE ID = ?");
            mysqli_stmt_bind_param($stmt_get_current_password, "i", $admin_id);
            mysqli_stmt_execute($stmt_get_current_password);
            $result_current_password = mysqli_stmt_get_result($stmt_get_current_password);
            $admin_current_data = mysqli_fetch_assoc($result_current_password);
            mysqli_stmt_close($stmt_get_current_password);

            if (!$admin_current_data || $password_saat_ini !== $admin_current_data['Password']) {
                $_SESSION['error_message_profile'] = "Password saat ini yang Anda masukkan salah.";
                header("Location: profil_admin_edit.php");
                exit();
            }
            $update_password = true;
        }

        if ($update_password) {
            $stmt_update_profile = mysqli_prepare($db, "UPDATE users SET Nama = ?, Email = ?, Password = ? WHERE ID = ? AND Level = 'admin'");
            mysqli_stmt_bind_param($stmt_update_profile, "sssi", $nama_admin_baru, $email_admin_baru, $password_baru, $admin_id);
        } else {
            $stmt_update_profile = mysqli_prepare($db, "UPDATE users SET Nama = ?, Email = ? WHERE ID = ? AND Level = 'admin'");
            mysqli_stmt_bind_param($stmt_update_profile, "ssi", $nama_admin_baru, $email_admin_baru, $admin_id);
        }

        if (mysqli_stmt_execute($stmt_update_profile)) {
            $_SESSION['username'] = $nama_admin_baru; 
            $_SESSION['success_message_profile'] = "Profil berhasil diperbarui.";
            header("Location: profil_admin.php");
            exit();
        } else {
            $_SESSION['error_message_profile'] = "Gagal memperbarui profil: " . mysqli_stmt_error($stmt_update_profile);
            header("Location: profil_admin_edit.php");
            exit();
        }
        mysqli_stmt_close($stmt_update_profile);

    } else {
        $_SESSION['error_message_profile'] = "Data tidak lengkap.";
        header("Location: profil_admin_edit.php");
        exit();
    }
} else {
    header("Location: profil_admin.php");
    exit();
}
?>