<?php
include "koneksi.php";

$id = $_POST['id_produk'];
$nama_produk = $_POST['nama_produk'];
$deskripsi = $_POST['deskripsi'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$id_merk = $_POST['id_merk'];
$id_kategori = $_POST['id_kategori'];
$tanggal_masuk = $_POST['tanggal_masuk'];

// Cek apakah gambar baru di-upload
if (!empty($_FILES['nama_file']['name'])) {
    $nama_file = $_FILES['nama_file']['name'];
    $tmp = $_FILES['nama_file']['tmp_name'];
    move_uploaded_file($tmp, "gambar/" . $nama_file);

    $sql = "UPDATE produk SET
      nama_produk='$nama_produk',
      deskripsi='$deskripsi',
      harga='$harga',
      stok='$stok',
      id_merk='$id_merk',
      id_kategori='$id_kategori',
      tanggal_masuk='$tanggal_masuk',
      nama_file='$nama_file'
      WHERE id_produk='$id'";
} else {
    $sql = "UPDATE produk SET
      nama_produk='$nama_produk',
      deskripsi='$deskripsi',
      harga='$harga',
      stok='$stok',
      id_merk='$id_merk',
      id_kategori='$id_kategori',
      tanggal_masuk='$tanggal_masuk'
      WHERE id_produk='$id'";
}

mysqli_query($koneksi, $sql);
header("Location: admin.php?edit=sukses");
?>