<?php
session_start();
if (!isset($_SESSION['id_pembeli'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$id_pesanan = $_GET['id_pesanan'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE id_pesanan = ?");
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT dp.*, p.nama, p.harga FROM detail_pesanan dp JOIN produk p ON dp.id_produk = p.id_produk WHERE dp.id_pesanan = ?");
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$detail_pesanan = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pesanan</title>
</head>
<body>
    <h2>Konfirmasi Pesanan</h2>
    <p>Pesanan ID: <?= $pesanan['id_pesanan'] ?></p>
    <p>Tanggal Transaksi: <?= $pesanan['tanggal_transaksi'] ?></p>
    <p>Total Harga: Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></p>

    <h3>Detail Pesanan:</h3>
    <ul>
        <?php foreach ($detail_pesanan as $item): ?>
        <li>
            <?= $item['nama'] ?> - <?= $item['jumlah'] ?> x Rp <?= number_format($item['harga_saat_beli'], 0, ',', '.') ?>
            = Rp <?= number_format($item['harga_saat_beli'] * $item['jumlah'], 0, ',', '.') ?>
        </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
