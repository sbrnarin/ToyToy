<?php
include "koneksi.php";

$id = $_GET['id_produk'];
$produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = $id"));
$merk = mysqli_query($koneksi, "SELECT * FROM merk");
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
  body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f2f4f8;
  padding: 20px 40px; /* ganti dari 40px ke 20px atas-bawah */
  color: #081F5C;
}

h2 {
  text-align: center;
  color: #081F5C;
  margin-top: 10px; /* naikkan dari default */
  margin-bottom: 20px;
}


  form {
    background-color: #ffffff;
    padding: 25px 30px;
    border-radius: 12px;
    max-width: 500px;
    margin: auto;
    box-shadow: 0 4px 12px rgba(8, 31, 92, 0.1);
    border: 1px solid #081F5C;
  }

  label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: #081F5C;
  }

  input[type="text"],
  input[type="number"],
  input[type="date"],
  select,
  textarea {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
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

  input[type="file"] {
    margin-top: 8px;
  }

  button[type="submit"] {
    margin-top: 20px;
    padding: 12px 20px;
    background-color: #081F5C;
    color: white;
    font-size: 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  button[type="submit"]:hover {
    background-color: #061744;
  }
</style>

</head>
<body>
  <h2>Edit Produk</h2>
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

  <label>Tanggal Masuk:</label><br>
  <input type="datetime" name="tanggal_masuk" value="<?= $produk['tanggal_masuk'] ?>"><br><br>

  <label>Ganti Gambar (opsional):</label><br>
  <input type="file" name="nama_file"><br><br>

  <button type="submit">Simpan Perubahan</button>
</form>
</body>
</html>

