<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_akun = $_SESSION['id_akun'] ?? 0;
if (!$id_akun) {
    header("Location: login.php");
    exit;
}

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

$pesananList = [];
while ($row = $result->fetch_assoc()) {
    $id_pesanan = $row['id_pesanan'];
    $status = trim($row['status_pengiriman'] ?? '') ?: 'Belum Diproses';

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
    <style>
* {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
    outline: none; 
    border: none;
    text-decoration: none;
    text-transform: none;
    transition: .2s linear;
}

body {
    background-color: #f4f6fc;
    color: #333;
}

.container {
    display: flex;
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
    gap: 20px;
}

.sidebar {
    width: 260px;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    padding: 25px;
}

.sidebar h3 {
    font-size: 20px;
    color: #081F5C;
    margin-bottom: 20px;
    border-bottom: 2px solid #081F5C;
    padding-bottom: 10px;
}

.sidebar a {
    display: block;
    padding: 10px 15px;
    background-color: #f0f3ff;
    color: #081F5C;
    border-radius: 6px;
    font-weight: 500;
    margin-bottom: 12px; /* Jarak antar tombol */
}

.sidebar a:hover {
    background-color: #081F5C;
    color: #fff;
}

.main-content {
    flex: 1;
    background-color: #fff;
    border-radius: 12px;
    padding: 35px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.main-content h2 {
    font-size: 26px;
    color: #081F5C;
    margin-bottom: 25px;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.order-item {
    background-color: #fff;
    border: 1px solid #ddd;
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 8px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.order-item p {
    margin: 6px 0;
}

.product-item {
    margin-left: 20px;
    font-size: 14px;
}

.btn-konfirmasi {
    background-color: #001f5f;
    color: white;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}

.btn-konfirmasi:hover {
    background-color: #003399;
}
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h3>Akun Saya</h3>
        <a href="user.php">Profil</a>
        <a href="pesanan.php" class="active">Pesanan Saya</a>
        <a href="#">Pusat Bantuan</a>
        <a href="logout.php">Logout</a>
        <a href="index.php">Home</a>
    </div>

    <div class="main-content">
        <h2>Pesanan Saya</h2>

        <?php if (empty($pesananList)): ?>
            <p style="text-align:center;">Belum ada pesanan.</p>
        <?php else: ?>
            <?php foreach ($pesananList as $id => $pesanan): ?>
                <div class="order-item">
                    <p><strong>ID Pesanan:</strong> <?= $id ?></p>
                    <p><strong>Tanggal:</strong> <?= $pesanan['tanggal'] ?></p>
                    <p><strong>Status:</strong> <?= $pesanan['status'] ?></p>
                    <p><strong>Metode Pembayaran:</strong> <?= $pesanan['metode'] ?></p>
                    <p><strong>Total:</strong> Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></p>
                    <p><strong>Produk:</strong></p>
                    <?php foreach ($pesanan['produk'] as $produk): ?>
                        <div class="product-item">
                            <?= $produk['nama'] ?> - <?= $produk['jumlah'] ?> x Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (strtolower($pesanan['status']) !== 'selesai'): ?>
                        <form method="POST" action="konfirmasi_selesai.php">
                            <input type="hidden" name="id_pesanan" value="<?= $id ?>">
                            <button class="btn-konfirmasi" type="submit">Konfirmasi Selesai</button>
                        </form>
                    <?php else: ?>
                        <p style="color: green; font-weight: bold;">✔ Selesai</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
