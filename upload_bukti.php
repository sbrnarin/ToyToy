<?php
$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$pesanan_id = $_GET['pesanan_id'] ?? 0;
$pesanan_id = (int)$pesanan_id;
$method = $_GET['method'] ?? '';

// Validasi metode pembayaran
if (!in_array($method, ['gopay', 'dana'])) {
    die("Metode pembayaran tidak valid.");
}

// Ambil data pesanan
$stmt = $conn->prepare("SELECT total_harga FROM pesanan WHERE id_pesanan = ?");
$stmt->bind_param("i", $pesanan_id);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        .upload-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h2 {
            color: #2c3e50;
            margin-top: 0;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .payment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
        }
        .upload-form {
            margin-top: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px dashed #ccc;
            border-radius: 6px;
            background: #f9f9f9;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #14378a;
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <h2>Upload Bukti Pembayaran</h2>
        
        <div class="payment-info">
            <p><strong>Nomor Pesanan:</strong> #<?= htmlspecialchars($pesanan_id) ?></p>
            <p><strong>Metode Pembayaran:</strong> <?= strtoupper(htmlspecialchars($method)) ?></p>
            <p><strong>Total Pembayaran:</strong> <?= formatRupiah($pesanan['total_harga']) ?></p>
        </div>

        <form class="upload-form" action="proses_upload_bukti.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="pesanan_id" value="<?= $pesanan_id ?>">
            <input type="hidden" name="metode_pembayaran" value="<?= $method ?>">
            
            <div class="form-group">
                <label for="bukti_pembayaran">Upload Bukti Pembayaran</label>
                <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*,.pdf" required>
                <small>Format: JPG, PNG, atau PDF (Maks. 2MB)</small>
            </div>
            
            <button type="submit" class="btn-submit">Kirim Bukti Pembayaran</button>
        </form>
    </div>
</body>
</html>