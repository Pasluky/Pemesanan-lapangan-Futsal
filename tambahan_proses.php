<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "admin") {
    header("location:login.php");
    exit;
}

function redirect_with_message_tambahan($url, $message, $type = 'error_message_tambahan') {
    $_SESSION[$type] = $message;
    header("Location: " . $url);
    exit;
}

if (isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi == 'tambah') {
        if (isset($_POST['nama_item'], $_POST['harga_item'])) {
            $nama_item = mysqli_real_escape_string($db, trim($_POST['nama_item']));
            $harga_item = $_POST['harga_item'];

            if (empty($nama_item) || !is_numeric($harga_item) || $harga_item < 0) {
                redirect_with_message_tambahan('tambahan_tambah.php', 'Nama item harus diisi dan harga harus angka positif.');
            } else {
                $stmt_insert = mysqli_prepare($db, "INSERT INTO tambahan (Nama, Harga) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt_insert, "si", $nama_item, $harga_item);
                
                if (mysqli_stmt_execute($stmt_insert)) {
                    redirect_with_message_tambahan('peralatan.php', 'Item tambahan berhasil ditambahkan!', 'success_message');
                } else {
                    redirect_with_message_tambahan('tambahan_tambah.php', 'Gagal menambahkan item: ' . mysqli_stmt_error($stmt_insert));
                }
                mysqli_stmt_close($stmt_insert);
            }
        } else {
            redirect_with_message_tambahan('tambahan_tambah.php', 'Semua field harus diisi.');
        }
    } 
    elseif ($aksi == 'edit') {
        if (isset($_POST['id_item'], $_POST['nama_item'], $_POST['harga_item'])) {
            $id_item = mysqli_real_escape_string($db, $_POST['id_item']);
            $nama_item = mysqli_real_escape_string($db, trim($_POST['nama_item']));
            $harga_item = $_POST['harga_item'];

            if (empty($nama_item) || !is_numeric($harga_item) || $harga_item < 0 || !is_numeric($id_item)) {
                 redirect_with_message_tambahan('tambahan_edit.php?id=' . $id_item, 'Data tidak valid. Pastikan semua field terisi dengan benar.');
            } else {
                $stmt_update = mysqli_prepare($db, "UPDATE tambahan SET Nama = ?, Harga = ? WHERE ID = ?");
                mysqli_stmt_bind_param($stmt_update, "sii", $nama_item, $harga_item, $id_item);
                
                if (mysqli_stmt_execute($stmt_update)) {
                    redirect_with_message_tambahan('peralatan.php', 'Item tambahan berhasil diupdate!', 'success_message');
                } else {
                    redirect_with_message_tambahan('tambahan_edit.php?id=' . $id_item, 'Gagal mengupdate item: ' . mysqli_stmt_error($stmt_update));
                }
                mysqli_stmt_close($stmt_update);
            }
        } else {
            redirect_with_message_tambahan('peralatan.php', 'Data untuk edit tidak lengkap.');
        }
    } else {
        redirect_with_message_tambahan('peralatan.php', 'Aksi tidak dikenal.');
    }
} 
elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_item = mysqli_real_escape_string($db, $_GET['id']);
        
        $stmt_delete = mysqli_prepare($db, "DELETE FROM tambahan WHERE ID = ?");
        mysqli_stmt_bind_param($stmt_delete, "i", $id_item);

        if (mysqli_stmt_execute($stmt_delete)) {
            redirect_with_message_tambahan('peralatan.php', 'Item tambahan berhasil dihapus!', 'success_message');
        } else {
            redirect_with_message_tambahan('peralatan.php', 'Gagal menghapus item: ' . mysqli_stmt_error($stmt_delete));
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        redirect_with_message_tambahan('peralatan.php', 'ID item tidak valid untuk dihapus.');
    }
} else {
    redirect_with_message_tambahan('peralatan.php', 'Tidak ada aksi yang dilakukan atau parameter tidak lengkap.');
}
?>