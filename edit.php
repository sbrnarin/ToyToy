<?php
include "koneksi.php";

$id = $_GET['id_produk'];
$produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = $id"));
$merk = mysqli_query($koneksi, "SELECT * FROM merk");
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<form action="proses_edit.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">

  <label>Nama Produk:</label><br>
  <input type="text" name="nama_produk" value="<?= $produk['nama_produk'] ?>"><br><br>

  <label>Deskripsi:</label><br>
  <textarea name="deskripsi" rows="4" cols="40" required></textarea><br><br>

  <label>Harga:</label><br>
  <input type="number" name="harga" value="<?= $produk['harga'] ?>"><br><br>

  <label>Stok:</label><br>
  <input type="number" name="stok" value="<?= $produk['stok'] ?>"><br><br>

  <label>Merk:</label><br>
  <select name="id_merk">
    <?php while ($m = mysqli_fetch_assoc($merk)) { ?>
      <option value="<?= $m['id_merk'] ?>" <?= $m['id_merk'] == $produk['id_merk'] ? 'selected' : '' ?>>
        <?= $m['nama_merk'] ?>
      </option>
    <?php } ?>
  </select><br><br>

  <label>Kategori:</label><br>
  <select name="id_kategori">
    <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
      <option value="<?= $k['id_kategori'] ?>" <?= $k['id_kategori'] == $produk['id_kategori'] ? 'selected' : '' ?>>
        <?= $k['nama_kategori'] ?>
      </option>
    <?php } ?>
  </select><br><br>

  <label>Ganti Gambar (opsional):</label><br>
  <input type="file" name="gambar"><br><br>

  <button type="submit">Simpan Perubahan</button>
</form>
