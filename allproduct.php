<?php
include 'koneksi.php'; // pastikan path-nya benar

$query = "SELECT p.id_produk, p.nama_produk, p.harga, gp.nama_file
          FROM produk p
          LEFT JOIN gambar_produk gp ON p.id_produk = gp.id_produk
          GROUP BY p.id_produk";

$produk = mysqli_query($koneksi, $query);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Toko Mainan</title>
</head>
<body>
    <h1>Daftar Produk</h1>

    <?php while ($row = mysqli_fetch_assoc($produk)) : ?>
        <div>
            <h3><?= $row['nama_produk']; ?></h3>
            <img src="images/<?= $row['gambar/pinguin eskrim.jpg']; ?>" width="200" alt="<?= $row['']; ?>">
            <p>Harga: Rp<?= number_format($row['harga']); ?></p>
        </div>
    <?php endwhile; ?>
</body>
</html>
