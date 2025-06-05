<?php
include "koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM produk WHERE id = '$id'";

    if (mysqli_query($koneksi, $sql)) {
        header("Location: admin.php"); // ganti sesuai nama file utama kamu
    } else {
        echo "Gagal menghapus produk: " . mysqli_error($koneksi);
    }
} else {
    echo "ID produk tidak ditemukan.";
}
