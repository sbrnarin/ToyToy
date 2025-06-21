<?php
include "koneksi.php";

$id = $_GET['id_produk'];
$produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = $id"));
$merk = mysqli_query($koneksi, "SELECT * FROM merk");
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Produk</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f2f4f8;
      padding: 20px 40px;
      color: #081F5C;
    }

    h2 {
      text-align: center;
      color: #081F5C;
      margin-top: 10px;
      margin-bottom: 25px;
    }

    form {
      background-color: #ffffff;
      padding: 25px 30px;
      border-radius: 12px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 4px 12px rgba(8, 31, 92, 0.1);
      border: 1px solid #081F5C;
    }

    label {
      display: block;
      margin-top: 18px;
      margin-bottom: 6px;
      font-weight: bold;
      color: #081F5C;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    input[type="file"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #081F5C;
      border-radius: 6px;
      font-size: 14px;
      box-sizing: border-box;
      background-color: #f9f9ff;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    button[type="submit"] {
      margin-top: 25px;
      padding: 12px 20px;
      background-color: #081F5C;
      color: white;
      font-size: 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #061744;
    }

    .current-image {
      margin-top: 10px;
      max-width: 200px;
      max-height: 200px;
      display: block;
    }
  </style>
</head>
<body>

  <h2>Edit Produk</h2>

  <form action="proses_edit.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">

    <label for="nama_produk">Nama Produk:</label>
    <input type="text" name="nama_produk" id="nama_produk" value="<?= $produk['nama_produk'] ?>" required>

    <label for="deskripsi">Deskripsi:</label>
    <textarea name="deskripsi" id="deskripsi" required><?= $produk['deskripsi'] ?></textarea>

    <label for="harga">Harga:</label>
    <input type="number" name="harga" id="harga" value="<?= $produk['harga'] ?>" required>

    <label for="stok">Stok:</label>
    <input type="number" name="stok" id="stok" value="<?= $produk['stok'] ?>" required>

    <label for="id_merk">Merk:</label>
    <select name="id_merk" id="id_merk" required>
      <option value="">-- Pilih Merk --</option>
      <?php while ($m = mysqli_fetch_assoc($merk)) { ?>
        <option value="<?= $m['id_merk'] ?>" <?= $m['id_merk'] == $produk['id_merk'] ? 'selected' : '' ?>>
          <?= $m['nama_merk'] ?>
        </option>
      <?php } ?>
    </select>

    <label for="id_kategori">Kategori:</label>
    <select name="id_kategori" id="id_kategori" required>
      <option value="">-- Pilih Kategori --</option>
      <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
        <option value="<?= $k['id_kategori'] ?>" <?= $k['id_kategori'] == $produk['id_kategori'] ? 'selected' : '' ?>>
          <?= $k['nama_kategori'] ?>
        </option>
      <?php } ?>
    </select>

    <label for="tanggal_masuk">Tanggal Masuk:</label>
    <input type="date" name="tanggal_masuk" id="tanggal_masuk" value="<?= date('Y-m-d', strtotime($produk['tanggal_masuk'])) ?>" required>

    <label for="current_image">Gambar Saat Ini:</label>
    <?php if (!empty($produk['nama_file'])) { ?>
      <img src="uploads/<?= $produk['nama_file'] ?>" alt="Gambar Produk" class="current-image">
    <?php } else { ?>
      <p>Tidak ada gambar</p>
    <?php } ?>

    <label for="nama_file">Ganti Gambar (opsional):</label>
    <input type="file" name="nama_file" id="nama_file" accept="image/*">

    <button type="submit">Simpan Perubahan</button>
  </form>

</body>
</html>