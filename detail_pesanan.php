<?php
include "koneksi.php";

// Aktifkan error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validasi parameter id_pesanan
if (!isset($_GET['id_pesanan']) || empty($_GET['id_pesanan'])) {
    header("Location: data_pesanan.php?error=missing_id");
    exit();
}

// Validasi dan sanitasi input
$id_pesanan = filter_input(INPUT_GET, 'id_pesanan', FILTER_VALIDATE_INT);
if ($id_pesanan === false || $id_pesanan <= 0) {
    header("Location: data_pesanan.php?error=invalid_id");
    exit();
}

// Query untuk mendapatkan detail pesanan dengan prepared statement
$sql_pesanan = "SELECT pesanan.*, pembeli.* 
                FROM pesanan 
                JOIN pembeli ON pesanan.id_pembeli = pembeli.id_pembeli
                WHERE pesanan.id_pesanan = ?";
$stmt_pesanan = mysqli_prepare($koneksi, $sql_pesanan);

if (!$stmt_pesanan) {
    die('Error prepare statement: ' . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt_pesanan, "i", $id_pesanan);
mysqli_stmt_execute($stmt_pesanan);
$result_pesanan = mysqli_stmt_get_result($stmt_pesanan);

if (mysqli_num_rows($result_pesanan) == 0) {
    header("Location: data_pesanan.php?error=order_not_found");
    exit();
}

$pesanan = mysqli_fetch_assoc($result_pesanan);

// Query untuk mendapatkan item pesanan dengan prepared statement
$sql_items = "SELECT detail_pesanan.*, produk.nama_produk, produk.harga, produk.nama_file 
              FROM detail_pesanan 
              JOIN produk ON detail_pesanan.id_produk = produk.id_produk
              WHERE detail_pesanan.id_pesanan = ?";
$stmt_items = mysqli_prepare($koneksi, $sql_items);

if (!$stmt_items) {
    die('Error prepare statement: ' . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt_items, "i", $id_pesanan);
mysqli_stmt_execute($stmt_items);
$query_items = mysqli_stmt_get_result($stmt_items);

// Jika tidak ada item, tetap lanjutkan (beberapa pesanan mungkin tidak punya item)
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan #<?= $id_pesanan ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    color: #333;
}

.sidebar {
    width: 230px;
    background-color: #081F5C;
    height: 100vh;
    position: fixed;
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar .logo img {
    width: 120px;
    margin-bottom: 40px;
}

.sidebar nav ul {
    list-style: none;
    padding: 0;
    width: 100%;
}

.sidebar nav ul li {
    margin-bottom: 15px;
}

.sidebar nav ul li a {
    color: white;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    transition: background 0.3s;
}

.sidebar nav ul li a:hover {
    background-color: #0b2d8c;
}

.main-content {
    margin-left: 250px;
    padding: 40px 50px;
    background-color: #f9f9f9;
    min-height: 100vh;
}

.container {
    background: white;
    padding: 35px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

h2 {
    text-align: center;
    margin-bottom: 40px;
    color: #081F5C;
    font-size: 28px;
}

.section {
    margin-bottom: 40px;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 30px;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 25px;
    color: #081F5C;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.info-item .info-label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #555;
}

.info-value {
    background-color: #f0f2f5;
    padding: 10px 15px;
    border-left: 4px solid #081F5C;
    border-radius: 6px;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 25px;
    font-weight: 600;
    display: inline-block;
    font-size: 0.85rem;
    text-transform: capitalize;
}

.payment-pending { background: #fff3cd; color: #856404; }
.payment-verified { background: #d4edda; color: #155724; }
.payment-canceled { background: #f8d7da; color: #721c24; }
.shipping-pending { background: #e2e3e5; color: #383d41; }
.shipping-processing { background: #cce5ff; color: #004085; }
.shipping-shipped { background: #d1ecf1; color: #0c5460; }
.shipping-completed { background: #d4edda; color: #155724; }

.bukti-pembayaran {
    max-width: 250px;
    border-radius: 8px;
    border: 1px solid #ccc;
    margin-top: 12px;
    transition: transform 0.3s;
}
.bukti-pembayaran:hover {
    transform: scale(1.05);
}

.products-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.products-table th {
    background-color: #081F5C;
    color: white;
    padding: 14px;
    font-weight: 600;
    text-align: left;
}

.products-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.product-image {
    width: 75px;
    height: 75px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.summary-table {
    margin-top: 30px;
    float: right;
    width: 300px;
}

.summary-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.summary-table tr:last-child td {
    font-weight: bold;
    font-size: 1.05em;
    border-bottom: none;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-top: 40px;
    padding: 12px 20px;
    background-color: #081F5C;
    color: white;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}
.back-btn:hover {
    background-color: #0b2d8c;
    transform: translateY(-2px);
}
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="logo">
      <img src="gambar/Kids Toys Logo (1).png" alt="Kids Toys Logo">
    </div>
    <nav>
      <ul>
        <li><a href="dash_admin.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="admin.php"><i class="fas fa-box"></i> Product</a></li>
        <li><a href="akun_admin.php"><i class="fas fa-user"></i> Account</a></li>
      </ul>
    </nav>
</aside>

<div class="main-content">
    <div class="container">
        <h2><i class="fas fa-file-alt"></i> Detail Pesanan #<?= $id_pesanan ?></h2>
        
        <div class="section">
            <div class="section-title"><i class="fas fa-info-circle"></i> Informasi Pesanan</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Status Pembayaran:</div>
                    <div class="info-value">
                        <?php
                        $paymentStatusClass = '';
                        switch(strtolower(trim($pesanan['status_pembayaran']))) {
                            case 'sudah diverifikasi':
                                $paymentStatusClass = 'payment-verified';
                                break;
                            case 'batal verifikasi':
                                $paymentStatusClass = 'payment-canceled';
                                break;
                            default:
                                $paymentStatusClass = 'payment-pending';
                        }
                        ?>
                        <span class="status-badge <?= $paymentStatusClass ?>"><?= $pesanan['status_pembayaran'] ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Pengiriman:</div>
                    <div class="info-value">
                        <?php
                        $shippingStatusClass = '';
                        switch(strtolower(trim($pesanan['status_pengiriman']))) {
                            case 'dikemas':
                                $shippingStatusClass = 'shipping-processing';
                                break;
                            case 'dikirim':
                                $shippingStatusClass = 'shipping-shipped';
                                break;
                            case 'selesai':
                                $shippingStatusClass = 'shipping-completed';
                                break;
                            default:
                                $shippingStatusClass = 'shipping-pending';
                        }
                        ?>
                        <span class="status-badge <?= $shippingStatusClass ?>"><?= $pesanan['status_pengiriman'] ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Pesan:</div>
                    <div class="info-value"><?= date('d/m/Y H:i', strtotime($pesanan['tanggal_pesan'])) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Metode Pembayaran:</div>
                    <div class="info-value"><?= $pesanan['metode_pembayaran'] ?></div>
                </div>
                <?php if (!empty($pesanan['bukti_pembayaran'])) : ?>
                <div class="info-item">
                    <div class="info-label">Bukti Pembayaran:</div>
                    <div class="info-value">
                        <a href="uploads/bukti_pembayaran/<?= $pesanan['bukti_pembayaran'] ?>" target="_blank">
                            <img src="uploads/bukti_pembayaran/<?= $pesanan['bukti_pembayaran'] ?>" class="bukti-pembayaran" alt="Bukti Pembayaran">
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title"><i class="fas fa-user"></i> Informasi Pelanggan</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama:</div>
                    <div class="info-value"><?= htmlspecialchars($pesanan['nama_pembeli']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?= htmlspecialchars($pesanan['email']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telepon:</div>
                    <div class="info-value"><?= htmlspecialchars($pesanan['no_telp']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Alamat:</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($pesanan['alamat'])) ?></div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title"><i class="fas fa-box-open"></i> Produk yang Dipesan</div>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_items = 0;
                    $total_harga = 0;
                    while($item = mysqli_fetch_assoc($query_items)) {
                        $subtotal = $item['harga'] * $item['jumlah'];
                        $total_items += $item['jumlah'];
                        $total_harga += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 15px;">
                              <img src="gambar/<?= htmlspecialchars($item['nama_file']) ?>" class="product-image" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                              <div><?= htmlspecialchars($item['nama_produk']) ?></div>
                          </div>

                        </td>
                        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td><?= $item['jumlah'] ?></td>
                        <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <table class="summary-table">
                <tr>
                    <td>Total Produk:</td>
                    <td><?= $total_items ?> item(s)</td>
                </tr>
                <tr>
                    <td>Subtotal:</td>
                    <td>Rp <?= number_format($total_harga, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Total Pesanan:</td>
                    <td>Rp <?= number_format($total_harga, 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>
        
        <a href="data_pesanan.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Pesanan</a>
    </div>
</div>

</body>
</html>