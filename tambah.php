<?php
include "koneksi.php";

// Ambil data merk dan kategori
$merk = mysqli_query($koneksi, "SELECT * FROM merk");
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<form action="proses_tambah.php" method="post" enctype="multipart/form-data">
  <label>Nama Produk:</label><br>
  <input type="text" name="nama_produk"><br><br>

  <label>Deskripsi:</label><br>
  <textarea name="deskripsi" rows="4" cols="40" required></textarea><br><br>

  <label>Harga:</label><br>
  <input type="number" name="harga"><br><br>

  <label>Stok:</label><br>
  <input type="number" name="stok"><br><br>

  <label>Merk:</label><br>
  <select name="id_merk">
    <?php while ($m = mysqli_fetch_assoc($merk)) { ?>
      <option value="<?= $m['id_merk'] ?>"><?= $m['nama_merk'] ?></option>
    <?php } ?>
  </select><br><br>

  <label>Kategori:</label><br>
  <select name="id_kategori">
    <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
      <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
    <?php } ?>
  </select><br><br>

  <label>Gambar Produk:</label><br>
  <input type="file" name="gambar"><br><br>

  <button type="submit">Tambah Produk</button>
</form>
