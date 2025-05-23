<?php
include "koneksi.php";
$id = $_GET['id_produk'];
$sql = "SELECT * FROM produk WHERE id_produk = $id";
$query = mysqli_query($koneksi, $sql);
$produk = mysqli_fetch_assoc($query);
?>

<h2>Edit Produk</h2>
<form action="proses_edit.php" method="POST">
  <input type="hidden" name="id_produk" value="<?=$produk['id_produk']?>">

  <label>Nama Produk:</label><br>
  <input type="text" name="nama_produk" value="<?=$produk['nama_produk']?>" required><br>

  <label>Deskripsi:</label><br>
  <textarea name="deskripsi" required><?=$produk['deskripsi']?></textarea><br>

  <label>Harga:</label><br>
  <input type="number" name="harga" value="<?=$produk['harga']?>" required><br>

  <label>Stok:</label><br>
  <input type="number" name="stok" value="<?=$produk['stok']?>" required><br>

  <label>ID Merk:</label><br>
  <input type="text" name="id_merk" value="<?=$produk['id_merk']?>" required><br>

  <label>ID Kategori:</label><br>
  <input type="text" name="id_kategori" value="<?=$produk['id_kategori']?>" required><br>

  <label>Tanggal Masuk:</label><br>
  <input type="date" name="tanggal_masuk" value="<?=$produk['tanggal_masuk']?>" required><br><br>

  <button type="submit">Update</button>
</form>
