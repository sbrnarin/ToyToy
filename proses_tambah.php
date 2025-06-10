<?php
include "koneksi.php";

$nama_produk = $_POST['nama_produk'];
$deskripsi = $_POST['deskripsi'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$id_merk = $_POST['id_merk'];
$id_kategori = $_POST['id_kategori'];
$tanggal_masuk = date('Y-m-d');

// Ambil data gambar
$nama_file = $_FILES['nama_file']['name'];
$tmp = $_FILES['nama_file']['tmp_name'];

// Simpan gambar ke folder 'gambar'
move_uploaded_file($tmp, "gambar/" . $nama_file);

// Simpan ke tabel produk langsung
$sql = "INSERT INTO produk 
(nama_produk, deskripsi, harga, stok, id_merk, id_kategori, tanggal_masuk, nama_file)
VALUES
('$nama_produk', '$deskripsi', '$harga', '$stok', '$id_merk', '$id_kategori', '$tanggal_masuk', '$nama_file')";

$query = mysqli_query($koneksi, $sql);

if ($query) {
    header("Location: admin.php?simpan=sukses");
} else {
    header("Location: admin.php?simpan=gagal_query");
}


?>