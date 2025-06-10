<?php
include "koneksi.php";

$id_kategori = $_GET['id_kategori'];

$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_kategori = '$id_kategori'");

while ($produk = mysqli_fetch_assoc($query)) {
    echo "<h3>{$produk['nama_produk']}</h3>";
    echo "<img src='gambar/{$produk['nama_file']}' width='150'><br>";
    echo "Harga: Rp " . number_format($produk['harga']) . "<hr>";
}
?>
