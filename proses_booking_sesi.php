<?php
session_start();
require 'config.php';

function redirect_with_message_booking_proses($url, $message, $is_success = false) {
    if ($is_success) {
        $_SESSION['success_message'] = $message;
    } else {
        $_SESSION['error_message_booking_form'] = $message;
    }
    header("Location: " . $url);
    exit;
}

function format_rupiah_proses_booking($angka){
    if (!is_numeric($angka)) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "user") {
    redirect_with_message_booking_proses("login.php", "Anda harus login untuk melakukan booking.");
}

$user_id_session = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_session'])) {

    if (isset($_SESSION['user_id'], $_POST['id_lapangan'], $_POST['telepon'], 
              $_POST['tanggal_main'], $_POST['sesi_waktu_mulai'], $_POST['harga_lapangan_sesi'],
              $_POST['jumlah_tambahan'], 
              $_POST['jumlah_bayar'])) {

        $id_lapangan = mysqli_real_escape_string($db, $_POST['id_lapangan']);
        $telepon = mysqli_real_escape_string($db, $_POST['telepon']);
        $tanggal_main = mysqli_real_escape_string($db, $_POST['tanggal_main']);
        $sesi_waktu_mulai = mysqli_real_escape_string($db, $_POST['sesi_waktu_mulai']);
        
        $durasi_sesi = 2; 

        $array_jumlah_tambahan = $_POST['jumlah_tambahan']; 
        $harga_lapangan_per_sesi_dari_form = (float)$_POST['harga_lapangan_sesi'];
        $jumlah_bayar = (float)$_POST['jumlah_bayar'];

        if (empty($id_lapangan) || empty($telepon) || empty($tanggal_main) || empty($sesi_waktu_mulai)) {
            redirect_with_message_booking_proses("field_detail.php?ID_field=" . $id_lapangan . "&tanggal_main=" . $tanggal_main, "Semua data wajib diisi dengan benar (kecuali tambahan).");
        }
        
        if (strtotime($tanggal_main) < strtotime(date('Y-m-d'))) {
            redirect_with_message_booking_proses("field_detail.php?ID_field=" . $id_lapangan, "Tanggal main tidak boleh tanggal yang sudah berlalu.");
        }

        $nama_lapangan_untuk_pesanan = 'Lapangan Dipesan'; 
        $harga_lapangan_aktual_per_sesi = 0;

        $stmt_lap_info = mysqli_prepare($db, "SELECT Nama, Harga FROM lapangan WHERE ID = ? AND status = 1");
        mysqli_stmt_bind_param($stmt_lap_info, "i", $id_lapangan);
        mysqli_stmt_execute($stmt_lap_info);
        $result_lap_info = mysqli_stmt_get_result($stmt_lap_info);
        if ($lap_info = mysqli_fetch_assoc($result_lap_info)) {
            $nama_lapangan_untuk_pesanan = $lap_info['Nama'];
            $harga_lapangan_aktual_per_sesi = (float)$lap_info['Harga'];
        } else {
            redirect_with_message_booking_proses("booking.php", "Lapangan tidak valid atau tidak tersedia saat ini.");
        }
        mysqli_stmt_close($stmt_lap_info);

        if ($harga_lapangan_aktual_per_sesi != $harga_lapangan_per_sesi_dari_form) {
             redirect_with_message_booking_proses("field_detail.php?ID_field=" . $id_lapangan, "Informasi harga lapangan berubah. Mohon periksa kembali dan ulangi pemesanan.");
        }

        $stmt_cek_sesi = mysqli_prepare($db, "SELECT ID FROM pesanan WHERE ID_Lapangan = ? AND Tanggal = ? AND Jam = ? AND status != 2");
        mysqli_stmt_bind_param($stmt_cek_sesi, "iss", $id_lapangan, $tanggal_main, $sesi_waktu_mulai);
        mysqli_stmt_execute($stmt_cek_sesi);
        mysqli_stmt_store_result($stmt_cek_sesi);

        if (mysqli_stmt_num_rows($stmt_cek_sesi) > 0) {
            mysqli_stmt_close($stmt_cek_sesi);
            redirect_with_message_booking_proses("field_detail.php?ID_field=$id_lapangan&tanggal_main=$tanggal_main", "Maaf, sesi yang Anda pilih baru saja dipesan. Silakan pilih sesi lain.");
        }
        mysqli_stmt_close($stmt_cek_sesi);
        
        $total_biaya_semua_tambahan_calc = 0;
        $detail_tambahan_untuk_disimpan = []; 
        
        if (is_array($array_jumlah_tambahan)) {
            foreach ($array_jumlah_tambahan as $id_item_tambahan => $jumlah_item) {
                $jumlah_item = (int)$jumlah_item;
                if ($jumlah_item > 0) {
                    $id_item_tambahan_safe = mysqli_real_escape_string($db, $id_item_tambahan);
                    
                    $harga_satuan_item_saat_ini = 0;
                    $stmt_harga_item = mysqli_prepare($db, "SELECT Harga, Nama FROM tambahan WHERE ID = ?");
                    mysqli_stmt_bind_param($stmt_harga_item, "i", $id_item_tambahan_safe);
                    mysqli_stmt_execute($stmt_harga_item);
                    $result_harga_item = mysqli_stmt_get_result($stmt_harga_item);
                    if ($item_info_db = mysqli_fetch_assoc($result_harga_item)) {
                        $harga_satuan_item_saat_ini = (float)$item_info_db['Harga'];
                        $subtotal_per_item = $jumlah_item * $harga_satuan_item_saat_ini;
                        $total_biaya_semua_tambahan_calc += $subtotal_per_item;
                        
                        $detail_tambahan_untuk_disimpan[] = [
                            'id_tambahan' => $id_item_tambahan_safe,
                            'jumlah_item' => $jumlah_item,
                            'harga_satuan_saat_pesan' => $harga_satuan_item_saat_ini,
                            'subtotal_item' => $subtotal_per_item
                        ];
                    }
                    mysqli_stmt_close($stmt_harga_item);
                }
            }
        }
        
        $total_harga_keseluruhan = $harga_lapangan_aktual_per_sesi + $total_biaya_semua_tambahan_calc;
        $uang_kembali = $jumlah_bayar - $total_harga_keseluruhan;

        if ($jumlah_bayar < $total_harga_keseluruhan) {
            redirect_with_message_booking_proses("field_detail.php?ID_field=$id_lapangan&tanggal_main=$tanggal_main", "Jumlah bayar kurang. Total: " . format_rupiah_proses_booking($total_harga_keseluruhan) . ". Bayar Anda: " . format_rupiah_proses_booking($jumlah_bayar));
        }
        
        $status_awal_booking = 1; 
        $status_pembayaran_awal = ($jumlah_bayar >= $total_harga_keseluruhan) ? 'Lunas' : 'Belum Lunas';
        $tanggal_pemesanan_dibuat = date('Y-m-d H:i:s');

        $kolom_tambahan1_pesanan = 0; 
        $kolom_tambahan2_pesanan = 0;

        $stmt_booking = mysqli_prepare($db, "INSERT INTO pesanan (id_user, ID_lapangan, Nama, Telepon, Jam, durasi, Tanggal, Tambahan_1, Tambahan_2, total_harga, bayar, kembali, status, Status_Pembayaran, Tanggal_Pemesanan_Dibuat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_booking, "iisssisiiiddiss", $user_id_session, $id_lapangan, $nama_lapangan_untuk_pesanan, $telepon, $sesi_waktu_mulai, $durasi_sesi, $tanggal_main, $kolom_tambahan1_pesanan, $kolom_tambahan2_pesanan, $total_harga_keseluruhan, $jumlah_bayar, $uang_kembali, $status_awal_booking, $status_pembayaran_awal, $tanggal_pemesanan_dibuat);

        if (mysqli_stmt_execute($stmt_booking)) {
            $id_pesanan_baru = mysqli_insert_id($db);

            if (!empty($detail_tambahan_untuk_disimpan)) {
                $stmt_insert_tambahan = mysqli_prepare($db, "INSERT INTO pesanan_detail_tambahan (id_pesanan, id_tambahan, jumlah_item, harga_satuan_saat_pesan, subtotal_item) VALUES (?, ?, ?, ?, ?)");
                foreach ($detail_tambahan_untuk_disimpan as $detail_item) {
                    mysqli_stmt_bind_param($stmt_insert_tambahan, "iiiid", 
                        $id_pesanan_baru, 
                        $detail_item['id_tambahan'], 
                        $detail_item['jumlah_item'], 
                        $detail_item['harga_satuan_saat_pesan'], 
                        $detail_item['subtotal_item']
                    );
                    mysqli_stmt_execute($stmt_insert_tambahan);
                }
                mysqli_stmt_close($stmt_insert_tambahan);
            }
            redirect_with_message_booking_proses("book_detail.php?ID_book=" . $id_pesanan_baru, "Booking berhasil dibuat! ID Pesanan Anda: #" . $id_pesanan_baru . ". Terima kasih.", true);
        } else {
            redirect_with_message_booking_proses("field_detail.php?ID_field=$id_lapangan&tanggal_main=$tanggal_main", "Gagal melakukan booking: " . mysqli_stmt_error($stmt_booking));
        }
        mysqli_stmt_close($stmt_booking);
    } else {
        redirect_with_message_booking_proses("booking.php", "Data booking tidak lengkap atau sesi tidak valid.");
    }
} else {
    header("Location: booking.php");
    exit;
}
?>