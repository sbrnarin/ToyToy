<?php
include "koneksi.php";

$id_produk = $_GET['id_produk'];

// Mulai transaction
mysqli_begin_transaction($koneksi);

try {
    // 1. Hapus dulu dari detail_pesanan
    mysqli_query($koneksi, "DELETE FROM detail_pesanan WHERE id_produk = '$id_produk'");
    
    // 2. Baru hapus dari produk
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk = '$id_produk'");
    
    // Commit transaksi jika berhasil
    mysqli_commit($koneksi);
    
    header("Location: admin.php?pesan=sukses_hapus");
} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($koneksi);
    header("Location: admin.php?pesan=gagal_hapus&error=" . urlencode($e->getMessage()));
}

mysqli_close($koneksi);
?>