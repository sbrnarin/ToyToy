<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "sabrinalina");
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

// Ambil data pesanan dan produk
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
    <link rel="stylesheet" href="user.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
        }
        .container {
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #fff;
            padding: 20px;
            border-right: 1px solid #ddd;
            min-height: 100vh;
        }
        .sidebar h3 {
            margin-top: 0;
        }
        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }
        .sidebar ul li {
            margin: 10px 0;
        }
        .sidebar ul li a {
            text-decoration: none;
            color: #333;
        }
        .sidebar ul li a.active {
            font-weight: bold;
            color: #007bff;
        }
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .order-item {
            background-color: #fff;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }
        .order-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-item {
            margin-left: 20px;
        }
        button {
            background-color: #2c3e50;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color:rgb(65, 91, 117);
        }
        .kembali-btn {
            margin-top: 30px;
            background-color: #2c3e50;
        }
        .kembali-btn:hover {
            background-color: #0056b3;
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
                        Status: <strong><?= htmlspecialchars($pesanan['status']) ?></strong>
                    </div>
                    <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars(ucfirst($pesanan['metode'])) ?></p>
                    <p><strong>Total:</strong> Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></p>
                    <p><strong>Produk:</strong></p>
                    <?php foreach ($pesanan['produk'] as $produk): ?>
                        <div class="product-item">
                            <?= htmlspecialchars($produk['nama']) ?> - <?= $produk['jumlah'] ?> x Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if (strtolower($pesanan['status']) !== 'selesai'): ?>
                        <form method="POST" action="konfirmasi_selesai.php" style="margin-top: 15px;">
                            <input type="hidden" name="id_pesanan" value="<?= $id ?>">
                            <button type="submit">Konfirmasi Pesanan Selesai</button>
                        </form>
                    <?php else: ?>
                        <p style="color: green; font-weight: bold; margin-top: 10px;">✔ Pesanan telah selesai.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <button class="kembali-btn" onclick="window.location.href='index.php'">Kembali ke Beranda</button>
    </div>
</div>

</body>
</html>
