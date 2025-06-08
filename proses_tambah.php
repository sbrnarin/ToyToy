<?php
include "koneksi.php";
// Ambil data dari form
$nama_produk = $_POST['nama_produk'];
$deskripsi = $_POST['deskripsi'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$id_merk = $_POST['id_merk'];
$id_kategori = $_POST['id_kategori'];
$tanggal_masuk = date ('Y-m-d');

// Upload gambar
//$nama_file = $_FILES['nama_file']['nama_file'];
//$tmp_file = $_FILES['nama_file']['tmp_nama'];
//$folder_tujuan = "./gambar/"; // Pastikan folder ini sudah ada dan writable
//$path_simpan = $folder_tujuan . $nama_file;

// Pindahkan file ke folder tujuan
//if(move_uploaded_file($tmp_file, $path_simpan)) {
    // Simpan data ke database (hanya nama file disimpan di DB)
$sql = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, id_merk, id_kategori, tanggal_masuk)
      VALUES ('$nama_produk', '$deskripsi', '$harga', '$stok', '$id_merk', '$id_kategori', '$tanggal_masuk')";
$query = mysqli_query($koneksi, $sql);

// simpan gambar
  $nama_file = $_FILES['gambar']['name'];
  $tmp = $_FILES['gambar']['tmp_name'];
  move_uploaded_file($tmp, "gambar_produk/" . $nama_file);

  mysqli_query($koneksi, "INSERT INTO gambar_produk (id_produk, nama_file)
  VALUES ('$id_produk', '$nama_file')");

    if($query){
        header("Location: admin.php?simpan=sukses");
        exit;
    } else {
        header("Location: admin.php?simpan=gagal_query");
        exit;
    }

?>