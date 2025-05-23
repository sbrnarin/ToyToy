<?php include "koneksi.php"; ?>

<h2>Tambah Produk</h2>
<form action="proses_tambah.php" method="POST">
  <label>Nama Produk:</label><br>
  <input type="text" name="nama_produk" required><br>

  <label>Deskripsi:</label><br>
  <textarea name="deskripsi" required></textarea><br>

  <label>Harga:</label><br>
  <input type="number" name="harga" required><br>

  <label>Stok:</label><br>
  <input type="number" name="stok" required><br>

  <label>ID Merk:</label><br>
  <input type="text" name="id_merk" required><br>

  <label>ID Kategori:</label><br>
  <input type="text" name="id_kategori" required><br>

  <label>Tanggal Masuk:</label><br>
  <input type="date" name="tanggal_masuk" required><br><br>

  <button type="submit">Simpan</button>
</form>
