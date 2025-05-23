<?php
include "koneksi.php";

$nama = $_POST['nama_produk'];
$deskripsi = $_POST['deskripsi'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$id_merk = $_POST['id_merk'];
$id_kategori = $_POST['id_kategori'];
$tanggal = $_POST['tanggal_masuk'];

$sql = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, id_merk, id_kategori, tanggal_masuk) 
        VALUES ('$nama', '$deskripsi', '$harga', '$stok', '$id_merk', '$id_kategori', '$tanggal')";

if (mysqli_query($koneksi, $sql)) {
    header("Location: daftar_produk.php"); // ubah ke file utama kamu
} else {
    echo "Gagal menambah produk: " . mysqli_error($koneksi);
}
