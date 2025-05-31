<?php
$db = mysqli_connect("localhost", "root", "", "penyewaan");
if (!$db) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
