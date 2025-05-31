<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] !== 'admin') {
    $_SESSION['error_message_status'] = "Akses ditolak. Anda harus login sebagai admin.";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status_booking'])) {
    if (isset($_POST['ID_book'], $_POST['status_pesanan_baru'], $_POST['status_pembayaran_baru'])) {
        
        $id_book = mysqli_real_escape_string($db, $_POST['ID_book']);
        $status_pesanan_baru = mysqli_real_escape_string($db, $_POST['status_pesanan_baru']);
        $status_pembayaran_baru = mysqli_real_escape_string($db, $_POST['status_pembayaran_baru']);

        if (!is_numeric($id_book)) {
            $_SESSION['error_message_status'] = "ID Booking tidak valid.";
            header("Location: riwayat_booking.php");
            exit();
        }

        $valid_status_pesanan = ['0', '1', '2']; 
        $valid_status_pembayaran = ['Lunas', 'Belum Lunas'];

        if (!in_array($status_pesanan_baru, $valid_status_pesanan)) {
            $_SESSION['error_message_status'] = "Status pesanan baru tidak valid.";
            header("Location: admin_book_detail_view.php?ID_book=" . $id_book);
            exit();
        }
        if (!in_array($status_pembayaran_baru, $valid_status_pembayaran)) {
            $_SESSION['error_message_status'] = "Status pembayaran baru tidak valid.";
            header("Location: admin_book_detail_view.php?ID_book=" . $id_book);
            exit();
        }
        
        $stmt_update = mysqli_prepare($db, "UPDATE pesanan SET status = ?, Status_Pembayaran = ? WHERE ID = ?");
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "ssi", $status_pesanan_baru, $status_pembayaran_baru, $id_book);
            
            if (mysqli_stmt_execute($stmt_update)) {
                if (mysqli_stmt_affected_rows($stmt_update) > 0) {
                    $_SESSION['success_message_status'] = "Status booking berhasil diperbarui.";
                } else {
                    $_SESSION['success_message_status'] = "Tidak ada perubahan status atau booking tidak ditemukan.";
                }
            } else {
                $_SESSION['error_message_status'] = "Gagal memperbarui status booking: " . mysqli_stmt_error($stmt_update);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $_SESSION['error_message_status'] = "Gagal menyiapkan statement update: " . mysqli_error($db);
        }
        
        header("Location: admin_book_detail_view.php?ID_book=" . $id_book);
        exit();

    } else {
        $_SESSION['error_message_status'] = "Data pembaruan status tidak lengkap.";
        header("Location: riwayat_booking.php"); 
        exit();
    }
} else {
    header("Location: riwayat_booking.php");
    exit();
}
?>