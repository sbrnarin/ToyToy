<?php
include "koneksi.php";

if (isset($_GET['id_produk'])) {
    $id = $_GET['id_produk'];
    $sql = "DELETE FROM produk WHERE id_produk = $id";

    if (mysqli_query($koneksi, $sql)) {
        header("Location: daftar_produk.php"); // ganti sesuai nama file utama kamu
    } else {
        echo "Gagal menghapus produk: " . mysqli_error($koneksi);
    }
} else {
    echo "ID produk tidak ditemukan.";
}
