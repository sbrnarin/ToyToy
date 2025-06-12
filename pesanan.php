<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sabrinalina";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil id_akun dari session
$id_akun = $_SESSION['id_akun'] ?? 0;
if (!$id_akun) {
    header("Location: login.php");
    exit;
}

// Cari id_pembeli berdasarkan id_akun
$stmtPembeli = $conn->prepare("SELECT id_pembeli FROM pembeli WHERE id_akun = ?");
$stmtPembeli->bind_param("i", $id_akun);
$stmtPembeli->execute();
$resultPembeli = $stmtPembeli->get_result();

if ($resultPembeli->num_rows === 0) {
    echo "Data pembeli tidak ditemukan.";
    exit;
}

$pembeli = $resultPembeli->fetch_assoc();
$id_pembeli = $pembeli['id_pembeli'];

// Ambil data pesanan
$stmt = $conn->prepare("
    SELECT p.id_pesanan, p.tanggal_pesan, p.status_pengiriman, p.total_harga, p.metode_pembayaran,
           pr.nama_produk, pr.harga, dp.jumlah
    FROM pesanan p
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN produk pr ON dp.id_produk = pr.id_produk
    WHERE p.id_pembeli = ?
    ORDER BY p.tanggal_pesan DESC
");
$stmt->bind_param("i", $id_pembeli);
$stmt->execute();
$result = $stmt->get_result();

// Gabungkan data pesanan dan detail
$pesananList = [];
while ($row = $result->fetch_assoc()) {
    $id_pesanan = $row['id_pesanan'];
    $status = trim($row['status_pengiriman'] ?? '') ?: 'Belum Diproses'; // ✅ perbaikan penting

    if (!isset($pesananList[$id_pesanan])) {
        $pesananList[$id_pesanan] = [
            'tanggal' => $row['tanggal_pesan'],
            'status' => $status,
            'total' => $row['total_harga'],
            'metode' => $row['metode_pembayaran'],
            'produk' => []
        ];
    }

    $pesananList[$id_pesanan]['produk'][] = [
        'nama' => $row['nama_produk'],
        'harga' => $row['harga'],
        'jumlah' => $row['jumlah']
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya</title>
    <link rel="stylesheet" href="user.css">
    <style>
        .order-item {
            border: 1px solid #ccc;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .order-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-item {
            margin-left: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h3>Akun Saya</h3>
        <ul>
            <li><a href="user.php">Profil</a></li>
            <li><a href="pesanan.php" class="active">Pesanan Saya</a></li>
            <li><a href="#">Pusat Bantuan</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Pesanan Saya</h2>

        <?php if (empty($pesananList)): ?>
            <p>Anda belum memiliki pesanan.</p>
        <?php else: ?>
            <?php foreach ($pesananList as $id => $pesanan): ?>
                <div class="order-item">
                    <div class="order-header">
                        Pesanan #<?= htmlspecialchars($id) ?> |
                        <?= htmlspecialchars($pesanan['tanggal']) ?> |
                        Status: <?= htmlspecialchars($pesanan['status']) ?>
                    </div>
                    <div>
                        <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars(ucfirst($pesanan['metode'])) ?></p>
                        <p><strong>Total:</strong> Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></p>
                        <p><strong>Produk:</strong></p>
                        <?php foreach ($pesanan['produk'] as $produk): ?>
                            <div class="product-item">
                                <?= htmlspecialchars($produk['nama']) ?> -
                                <?= $produk['jumlah'] ?> x Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <br>
        <button onclick="window.location.href='index.php'">Kembali ke Beranda</button>
    </div>
</div>
</body>
</html>
