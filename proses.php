<?php
session_start();
require 'config.php';

function redirect_with_message($url, $message, $type = 'error_message') {
    $_SESSION[$type] = $message;
    header("Location: " . $url);
    exit;
}

function format_rupiah_proses($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

if (isset($_POST['login'])) {
    if (isset($_POST['email'], $_POST['password'])) {
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $password = $_POST['password']; 

        $stmt = mysqli_prepare($db, "SELECT ID, Nama, Email, Password, Level FROM users WHERE Email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            
            if ($password == $data['Password']) { 
                $_SESSION['username'] = $data['Nama'];
                $_SESSION['user_id'] = $data['ID'];
                $_SESSION['Level'] = $data['Level'];

                if ($data['Level'] == "admin") {
                    header("location: admin_home.php");
                    exit;
                } else if ($data['Level'] == "user") {
                    $redirect_url = $_SESSION['redirect_after_login'] ?? 'user_home.php';
                    unset($_SESSION['redirect_after_login']);
                    header("location: " . $redirect_url);
                    exit;
                }
            } else {
                redirect_with_message('login.php', 'Email atau password salah.');
            }
        } else {
            redirect_with_message('login.php', 'Email atau password salah.');
        }
        mysqli_stmt_close($stmt);
    } else {
        redirect_with_message('login.php', 'Email dan password harus diisi.');
    }
}

if (isset($_POST['register'])) {
    if (isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
        $username = mysqli_real_escape_string($db, trim($_POST['username']));
        $email = mysqli_real_escape_string($db, trim($_POST['email']));
        $password = $_POST['password']; 
        $confirm_password = $_POST['confirm_password'];

        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            redirect_with_message('register.php', 'Semua field harus diisi.');
        } elseif ($password !== $confirm_password) {
            redirect_with_message('register.php', 'Password dan Konfirmasi Password tidak cocok.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirect_with_message('register.php', 'Format email tidak valid.');
        } else {
            $stmt_check_email = mysqli_prepare($db, "SELECT ID FROM users WHERE Email = ?");
            mysqli_stmt_bind_param($stmt_check_email, "s", $email);
            mysqli_stmt_execute($stmt_check_email);
            mysqli_stmt_store_result($stmt_check_email);

            if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
                redirect_with_message('register.php', 'Email sudah terdaftar. Silakan gunakan email lain.');
            } else {
                mysqli_stmt_close($stmt_check_email);
                
                $stmt_insert_user = mysqli_prepare($db, "INSERT INTO users (Nama, Email, Password, Level) VALUES (?, ?, ?, 'user')");
                mysqli_stmt_bind_param($stmt_insert_user, "sss", $username, $email, $password); 
                
                if (mysqli_stmt_execute($stmt_insert_user)) {
                    $_SESSION['success_message'] = "Akun berhasil dibuat! Silakan login.";
                    header("location: login.php");
                    exit;
                } else {
                    redirect_with_message('register.php', 'Registrasi gagal. Silakan coba lagi: ' . mysqli_stmt_error($stmt_insert_user));
                }
                mysqli_stmt_close($stmt_insert_user);
            }
             if(isset($stmt_check_email)) mysqli_stmt_close($stmt_check_email);
        }
    } else {
        redirect_with_message('register.php', 'Semua field registrasi harus diisi.');
    }
}


if (isset($_POST['book_session'])) {
    if (isset($_SESSION['user_id'], $_POST['id_lapangan'], $_POST['telepon'], 
              $_POST['tanggal_main'], $_POST['sesi_waktu_mulai'], $_POST['harga_lapangan_sesi'],
              $_POST['jumlah_sepatu'], $_POST['jumlah_kostum'], $_POST['jumlah_bayar'])) {

        $id_lapangan = mysqli_real_escape_string($db, $_POST['id_lapangan']);
        $id_user = mysqli_real_escape_string($db, $_SESSION['user_id']);
        $telepon = mysqli_real_escape_string($db, $_POST['telepon']);
        $tanggal_main = mysqli_real_escape_string($db, $_POST['tanggal_main']);
        $sesi_waktu_mulai = mysqli_real_escape_string($db, $_POST['sesi_waktu_mulai']);
        $durasi = 2; 
        
        $jumlah_sepatu = (int)$_POST['jumlah_sepatu'];
        $jumlah_kostum = (int)$_POST['jumlah_kostum'];
        $harga_lapangan_per_sesi_dari_form = (float)$_POST['harga_lapangan_sesi']; 
        $jumlah_bayar = (float)$_POST['jumlah_bayar'];

        $nama_lapangan_untuk_pesanan = 'Nama Lapangan Belum Diambil'; 
        $harga_lapangan_aktual_per_sesi = 0;

        $stmt_lap_info = mysqli_prepare($db, "SELECT Nama, Harga FROM lapangan WHERE ID = ? AND status = 1");
        mysqli_stmt_bind_param($stmt_lap_info, "i", $id_lapangan);
        mysqli_stmt_execute($stmt_lap_info);
        $result_lap_info = mysqli_stmt_get_result($stmt_lap_info);
        if ($lap_info = mysqli_fetch_assoc($result_lap_info)) {
            $nama_lapangan_untuk_pesanan = $lap_info['Nama'];
            $harga_lapangan_aktual_per_sesi = (float)$lap_info['Harga'];
        } else {
            redirect_with_message("booking.php", "Lapangan tidak valid atau tidak tersedia.");
        }
        mysqli_stmt_close($stmt_lap_info);

        if ($harga_lapangan_aktual_per_sesi != $harga_lapangan_per_sesi_dari_form) {
             redirect_with_message("field_detail.php?ID_field=$id_lapangan", "Terjadi perubahan harga lapangan, silakan periksa kembali.");
        }

        $stmt_cek_sesi = mysqli_prepare($db, "SELECT ID FROM pesanan WHERE ID_Lapangan = ? AND Tanggal = ? AND Jam = ? AND status != 2");
        mysqli_stmt_bind_param($stmt_cek_sesi, "iss", $id_lapangan, $tanggal_main, $sesi_waktu_mulai);
        mysqli_stmt_execute($stmt_cek_sesi);
        mysqli_stmt_store_result($stmt_cek_sesi);

        if (mysqli_stmt_num_rows($stmt_cek_sesi) > 0) {
            mysqli_stmt_close($stmt_cek_sesi);
            redirect_with_message("field_detail.php?ID_field=$id_lapangan&tanggal_main=$tanggal_main", "Maaf, sesi yang Anda pilih baru saja dipesan orang lain. Silakan pilih sesi lain.");
        }
        mysqli_stmt_close($stmt_cek_sesi);
        
        $harga_satuan_sepatu = 0;
        $harga_satuan_kostum = 0;
        
        $stmt_tambahan_sepatu = mysqli_prepare($db, "SELECT Harga FROM tambahan WHERE ID = 3"); 
        if ($stmt_tambahan_sepatu) {
            mysqli_stmt_execute($stmt_tambahan_sepatu);
            $result_tambahan_sepatu = mysqli_stmt_get_result($stmt_tambahan_sepatu);
            if ($row_tambahan_s = mysqli_fetch_assoc($result_tambahan_sepatu)) {
                $harga_satuan_sepatu = (float)$row_tambahan_s['Harga'];
            }
            mysqli_stmt_close($stmt_tambahan_sepatu);
        }
        
        $stmt_tambahan_kostum = mysqli_prepare($db, "SELECT Harga FROM tambahan WHERE ID = 2"); 
         if ($stmt_tambahan_kostum) {
            mysqli_stmt_execute($stmt_tambahan_kostum);
            $result_tambahan_kostum = mysqli_stmt_get_result($stmt_tambahan_kostum);
            if ($row_tambahan_k = mysqli_fetch_assoc($result_tambahan_kostum)) {
                $harga_satuan_kostum = (float)$row_tambahan_k['Harga'];
            }
            mysqli_stmt_close($stmt_tambahan_kostum);
        }

        $total_biaya_sepatu = $jumlah_sepatu * $harga_satuan_sepatu;
        $total_biaya_kostum = $jumlah_kostum * $harga_satuan_kostum;
        $total_harga_keseluruhan = $harga_lapangan_aktual_per_sesi + $total_biaya_sepatu + $total_biaya_kostum;
        
        $uang_kembali = $jumlah_bayar - $total_harga_keseluruhan;

        if ($jumlah_bayar < $total_harga_keseluruhan) {
            redirect_with_message("field_detail.php?ID_field=$id_lapangan&tanggal_main=$tanggal_main", "Jumlah bayar kurang. Total: " . format_rupiah_proses($total_harga_keseluruhan) . ". Bayar Anda: " . format_rupiah_proses($jumlah_bayar));
        }
        
        $status_awal_booking = 1; 
        $status_pembayaran_awal = ($jumlah_bayar >= $total_harga_keseluruhan) ? 'Lunas' : 'Belum Lunas';


        $stmt_booking = mysqli_prepare($db, "INSERT INTO pesanan (ID_User, ID_Lapangan, Nama, Telepon, Jam, durasi, Tanggal, Tambahan_1, Tambahan_2, total_harga, bayar, kembali, status, Status_Pembayaran, Tanggal_Pemesanan_Dibuat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt_booking, "iisssisiiiddiss", $id_user, $id_lapangan, $nama_lapangan_untuk_pesanan, $telepon, $sesi_waktu_mulai, $durasi, $tanggal_main, $jumlah_sepatu, $jumlah_kostum, $total_harga_keseluruhan, $jumlah_bayar, $uang_kembali, $status_awal_booking, $status_pembayaran_awal);

        if (mysqli_stmt_execute($stmt_booking)) {
            $id_pesanan_baru = mysqli_insert_id($db);
            $_SESSION['success_message'] = "Booking berhasil dibuat! ID Pesanan Anda: #" . $id_pesanan_baru . ". Terima kasih.";
            header("location: book_detail.php?ID_book=" . $id_pesanan_baru);
            exit;
        } else {
            redirect_with_message("field_detail.php?ID_field=$id_lapangan&tanggal_main=$tanggal_main", "Gagal melakukan booking: " . mysqli_stmt_error($stmt_booking));
        }
        mysqli_stmt_close($stmt_booking);
    } else {
        redirect_with_message("booking.php", "Data booking tidak lengkap atau sesi tidak valid.");
    }
}

if (isset($_POST["add_addon"])) {
    if (isset($_POST["name"], $_POST["price"])) {
        $addon_name = mysqli_real_escape_string($db, $_POST["name"]);
        $addon_price = mysqli_real_escape_string($db, $_POST["price"]);

        if (empty($addon_name) || !is_numeric($addon_price) || $addon_price < 0) {
            redirect_with_message('peralatan.php', 'Nama item harus diisi dan harga harus angka positif.');
        } else {
            $stmt = mysqli_prepare($db, "INSERT INTO tambahan (Nama, Harga) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "si", $addon_name, $addon_price);

            if (mysqli_stmt_execute($stmt)) {
                redirect_with_message('peralatan.php', 'Item tambahan berhasil dibuat!', 'success_message');
            } else {
                redirect_with_message('peralatan.php', 'Gagal menambahkan item: ' . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        }
    } else {
         redirect_with_message('peralatan.php', 'Nama dan harga item harus diisi.');
    }
}
?>