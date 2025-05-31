<?php
session_start();
require 'config.php';

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != "admin") {
    header("location:login.php");
    exit;
}

function redirect_with_message_lapangan($url, $message, $type = 'error_message') {
    $_SESSION[$type] = $message;
    header("Location: " . $url);
    exit;
}

// Direktori untuk menyimpan foto lapangan
$target_dir = "img/"; // Pastikan folder 'img' ada di root proyek Anda, atau sesuaikan path ini

if (isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    // Proses TAMBAH Lapangan
    if ($aksi == 'tambah') {
        if (isset($_POST['nama_lapangan'], $_POST['tipe_lapangan'], $_POST['jenis_lapangan'], $_POST['harga_lapangan'], $_POST['status_lapangan'])) {
            
            $nama_lapangan = mysqli_real_escape_string($db, trim($_POST['nama_lapangan']));
            $tipe_lapangan = mysqli_real_escape_string($db, $_POST['tipe_lapangan']);
            $jenis_lapangan = mysqli_real_escape_string($db, $_POST['jenis_lapangan']);
            $harga_lapangan = mysqli_real_escape_string($db, $_POST['harga_lapangan']);
            $status_lapangan = mysqli_real_escape_string($db, $_POST['status_lapangan']);
            $nama_foto = '';

            if (empty($nama_lapangan) || empty($tipe_lapangan) || empty($jenis_lapangan) || !is_numeric($harga_lapangan) || $harga_lapangan < 0 || ($status_lapangan !== '0' && $status_lapangan !== '1')) {
                redirect_with_message_lapangan('lapangan_tambah.php', 'Semua field wajib diisi dengan format yang benar.');
            }

            // Proses upload foto jika ada
            if (isset($_FILES['foto_lapangan']) && $_FILES['foto_lapangan']['error'] == 0) {
                $file_tmp = $_FILES['foto_lapangan']['tmp_name'];
                $file_name_original = $_FILES['foto_lapangan']['name'];
                $file_size = $_FILES['foto_lapangan']['size'];
                $file_ext_arr = explode('.', $file_name_original);
                $file_ext = strtolower(end($file_ext_arr));
                
                $extensions = array("jpeg","jpg","png","gif");
                
                if (in_array($file_ext, $extensions) === false) {
                    redirect_with_message_lapangan('lapangan_tambah.php', 'Ekstensi file foto tidak diizinkan, silakan pilih JPG, PNG, atau GIF.');
                }
                
                if ($file_size > 2097152) { // 2MB
                    redirect_with_message_lapangan('lapangan_tambah.php', 'Ukuran file foto maksimal adalah 2MB.');
                }
                
                $nama_foto = "lapangan_" . time() . "_" . uniqid() . "." . $file_ext;
                $target_file = $target_dir . $nama_foto;
                
                if (!move_uploaded_file($file_tmp, $target_file)) {
                    redirect_with_message_lapangan('lapangan_tambah.php', 'Maaf, terjadi kesalahan saat mengupload file foto.');
                }
            }

            $stmt_insert = mysqli_prepare($db, "INSERT INTO lapangan (Nama, Tipe, Jenis, Harga, status, foto) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "sssiss", $nama_lapangan, $tipe_lapangan, $jenis_lapangan, $harga_lapangan, $status_lapangan, $nama_foto);
            
            if (mysqli_stmt_execute($stmt_insert)) {
                redirect_with_message_lapangan('datalapangan.php', 'Lapangan baru berhasil ditambahkan!', 'success_message');
            } else {
                if (!empty($nama_foto) && file_exists($target_file)) {
                    unlink($target_file); 
                }
                redirect_with_message_lapangan('lapangan_tambah.php', 'Gagal menambahkan lapangan: ' . mysqli_stmt_error($stmt_insert));
            }
            mysqli_stmt_close($stmt_insert);

        } else {
            redirect_with_message_lapangan('lapangan_tambah.php', 'Data tidak lengkap.');
        }
    } 
    // Proses EDIT Lapangan
    elseif ($aksi == 'edit') {
        if (isset($_POST['id_lapangan'], $_POST['nama_lapangan'], $_POST['tipe_lapangan'], $_POST['jenis_lapangan'], $_POST['harga_lapangan'], $_POST['status_lapangan'])) {
            
            $id_lapangan = mysqli_real_escape_string($db, $_POST['id_lapangan']);
            $nama_lapangan = mysqli_real_escape_string($db, trim($_POST['nama_lapangan']));
            $tipe_lapangan = mysqli_real_escape_string($db, $_POST['tipe_lapangan']);
            $jenis_lapangan = mysqli_real_escape_string($db, $_POST['jenis_lapangan']);
            $harga_lapangan = mysqli_real_escape_string($db, $_POST['harga_lapangan']);
            $status_lapangan = mysqli_real_escape_string($db, $_POST['status_lapangan']);
            $foto_lama = mysqli_real_escape_string($db, $_POST['foto_lama']);
            $nama_foto_baru = $foto_lama; 

            if (empty($id_lapangan) || !is_numeric($id_lapangan) || empty($nama_lapangan) || empty($tipe_lapangan) || empty($jenis_lapangan) || !is_numeric($harga_lapangan) || $harga_lapangan < 0 || ($status_lapangan !== '0' && $status_lapangan !== '1')) {
                redirect_with_message_lapangan('lapangan_edit.php?id=' . $id_lapangan, 'Semua field wajib diisi dengan format yang benar.');
            }

            if (isset($_FILES['foto_lapangan']) && $_FILES['foto_lapangan']['error'] == 0) {
                $file_tmp = $_FILES['foto_lapangan']['tmp_name'];
                $file_name_original = $_FILES['foto_lapangan']['name'];
                $file_size = $_FILES['foto_lapangan']['size'];
                $file_ext_arr = explode('.', $file_name_original);
                $file_ext = strtolower(end($file_ext_arr));
                
                $extensions = array("jpeg","jpg","png","gif");
                
                if (in_array($file_ext, $extensions) === false) {
                    redirect_with_message_lapangan('lapangan_edit.php?id=' . $id_lapangan, 'Ekstensi file foto tidak diizinkan (JPG, PNG, GIF).');
                }
                
                if ($file_size > 2097152) {
                    redirect_with_message_lapangan('lapangan_edit.php?id=' . $id_lapangan, 'Ukuran file foto maksimal adalah 2MB.');
                }
                
                $nama_foto_baru = "lapangan_" . time() . "_" . uniqid() . "." . $file_ext;
                $target_file_baru = $target_dir . $nama_foto_baru;
                
                if (!move_uploaded_file($file_tmp, $target_file_baru)) {
                    redirect_with_message_lapangan('lapangan_edit.php?id=' . $id_lapangan, 'Gagal mengupload foto baru.');
                } else {
                    if (!empty($foto_lama) && file_exists($target_dir . $foto_lama)) {
                        unlink($target_dir . $foto_lama); 
                    }
                }
            }

            $stmt_update = mysqli_prepare($db, "UPDATE lapangan SET Nama = ?, Tipe = ?, Jenis = ?, Harga = ?, status = ?, foto = ? WHERE ID = ?");
            mysqli_stmt_bind_param($stmt_update, "sssissi", $nama_lapangan, $tipe_lapangan, $jenis_lapangan, $harga_lapangan, $status_lapangan, $nama_foto_baru, $id_lapangan);
            
            if (mysqli_stmt_execute($stmt_update)) {
                redirect_with_message_lapangan('datalapangan.php', 'Data lapangan berhasil diupdate!', 'success_message');
            } else {
                if ($nama_foto_baru !== $foto_lama && file_exists($target_dir . $nama_foto_baru)) {
                    unlink($target_dir . $nama_foto_baru); 
                }
                redirect_with_message_lapangan('lapangan_edit.php?id=' . $id_lapangan, 'Gagal mengupdate data lapangan: ' . mysqli_stmt_error($stmt_update));
            }
            mysqli_stmt_close($stmt_update);

        } else {
            redirect_with_message_lapangan('datalapangan.php', 'Data untuk edit tidak lengkap.');
        }
    } else {
        redirect_with_message_lapangan('datalapangan.php', 'Aksi tidak dikenal.');
    }
} 
// Proses HAPUS Lapangan
elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_lapangan = mysqli_real_escape_string($db, $_GET['id']);

        $query_get_foto = mysqli_prepare($db, "SELECT foto FROM lapangan WHERE ID = ?");
        mysqli_stmt_bind_param($query_get_foto, "i", $id_lapangan);
        mysqli_stmt_execute($query_get_foto);
        $result_foto = mysqli_stmt_get_result($query_get_foto);
        $data_foto = mysqli_fetch_assoc($result_foto);
        $nama_foto_hapus = $data_foto ? $data_foto['foto'] : null;
        mysqli_stmt_close($query_get_foto);

        $stmt_delete = mysqli_prepare($db, "DELETE FROM lapangan WHERE ID = ?");
        mysqli_stmt_bind_param($stmt_delete, "i", $id_lapangan);

        if (mysqli_stmt_execute($stmt_delete)) {
            if (!empty($nama_foto_hapus) && file_exists($target_dir . $nama_foto_hapus)) {
                unlink($target_dir . $nama_foto_hapus);
            }
            redirect_with_message_lapangan('datalapangan.php', 'Data lapangan berhasil dihapus!', 'success_message');
        } else {
            redirect_with_message_lapangan('datalapangan.php', 'Gagal menghapus data lapangan: ' . mysqli_stmt_error($stmt_delete));
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        redirect_with_message_lapangan('datalapangan.php', 'ID lapangan tidak valid untuk dihapus.');
    }
} else {
    redirect_with_message_lapangan('datalapangan.php', 'Tidak ada aksi yang dilakukan.');
}
?>