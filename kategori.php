<?php
include "koneksi.php";

$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
while ($k = mysqli_fetch_assoc($kategori)) {
    echo "<a href='produk_kategori.php?id_kategori={$k['id_kategori']}'>{$k['nama_kategori']}</a><br>";
}
?>
