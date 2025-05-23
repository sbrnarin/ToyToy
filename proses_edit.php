<?php
include "koneksi.php";

$id = $_POST['id_produk'];
$nama = $_POST['nama_produk'];
$deskripsi = $_POST['deskripsi'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$id_merk = $_POST['id_merk'];
$id_kategori = $_POST['id_kategori'];
$tanggal = $_POST['tanggal_masuk'];

$sql = "UPDATE produk SET 
        nama_produk = '$nama',
        deskripsi = '$deskripsi',
        harga = '$harga',
        stok = '$stok',
        id_merk = '$id_merk',
        id_kategori = '$id_kategori',
        tanggal_masuk = '$tanggal'
        WHERE id_produk = $id";

if (mysqli_query($koneksi, $sql)) {
    header("Location: daftar_produk.php"); // ubah ke file utama kamu
} else {
    echo "Gagal mengedit produk: " . mysqli_error($koneksi);
}
