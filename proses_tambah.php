<?php
include "koneksi.php";

// Ambil data dari form
$nama_produk = $_POST['nama_produk'];
$deskripsi = $_POST['deskripsi'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$id_merk = $_POST['id_merk'];
$id_kategori = $_POST['id_kategori'];
$tanggal_masuk = $_POST['tanggal_masuk'];

// Upload gambar
$nama_file = $_FILES['nama_file']['nama'];
$tmp_file = $_FILES['nama_file']['tmp_nama'];
$folder_tujuan = "./gambar/"; // Pastikan folder ini sudah ada dan writable
$path_simpan = $folder_tujuan . $nama_file;

// Pindahkan file ke folder tujuan
if(move_uploaded_file($tmp_file, $path_simpan)) {
    // Simpan data ke database (hanya nama file disimpan di DB)
    $sql = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, id_merk, id_kategori, tanggal_masuk, nama_file)
            VALUES ('$nama_produk', $deskripsi, '$harga', '$stok', '$id_merk', '$id_kategori', '$tanggal_masuk', '$nama_file')";
    $query = mysqli_query($koneksi, $sql);

    if($query){
        header("Location: admin.php?simpan=sukses");
        exit;
    } else {
        // Jika query gagal
        header("Location: admin.php?simpan=gagal_query");
        exit;
    }
} else {
    // Jika upload file gagal
    header("Location: admin.php?simpan=gagal_upload");
    exit;
}
?>