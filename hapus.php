<?php
include "koneksi.php";

$id = $_GET['id_produk'];

// Hapus dari database saja, tanpa menghapus file gambar di folder
mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='$id'");

header("Location: admin.php?hapus=sukses");
exit;
?>
