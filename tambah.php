<?php
include "koneksi.php";

// Ambil data merk dan kategori
$merk = mysqli_query($koneksi, "SELECT * FROM merk");
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Produk</title>
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
  </style>
</head>
<body>

  <h2>Tambah Produk</h2>

  <form action="proses_tambah.php" method="post" enctype="multipart/form-data">
    <label for="nama_produk">Nama Produk:</label>
    <input type="text" name="nama_produk" id="nama_produk" required>

    <label for="deskripsi">Deskripsi:</label>
    <textarea name="deskripsi" id="deskripsi" required></textarea>

    <label for="harga">Harga:</label>
    <input type="number" name="harga" id="harga" required>

    <label for="stok">Stok:</label>
    <input type="number" name="stok" id="stok" required>

    <label for="id_merk">Merk:</label>
    <select name="id_merk" id="id_merk" required>
      <option value="">-- Pilih Merk --</option>
      <?php while ($m = mysqli_fetch_assoc($merk)) { ?>
        <option value="<?= $m['id_merk'] ?>"><?= $m['nama_merk'] ?></option>
      <?php } ?>
    </select>

    <label for="id_kategori">Kategori:</label>
    <select name="id_kategori" id="id_kategori" required>
      <option value="">-- Pilih Kategori --</option>
      <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
        <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
      <?php } ?>
    </select>

    <label for="tanggal_masuk">Tanggal Masuk:</label>
    <input type="date" name="tanggal_masuk" id="tanggal_masuk" required>

    <label for="nama_file">Gambar Produk:</label>
    <input type="file" name="nama_file" id="nama_file" accept="image/*" required>

    <button type="submit">Tambah Produk</button>
  </form>

</body>
</html>
