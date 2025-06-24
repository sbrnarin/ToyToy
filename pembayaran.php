<?php
$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$pesanan_id = isset($_GET['pesanan_id']) ? (int)$_GET['pesanan_id'] : 0;
if ($pesanan_id <= 0) {
    header("Location: pesanan.php");
    exit();
}

// Ambil data pesanan
$stmt = $conn->prepare("
    SELECT p.id_pesanan, p.total_harga,
           p.metode_pembayaran, p.tanggal_pesan, p.ongkir, 
           p.metode_pengiriman, p.kode_pembayaran,
           pb.nama_pembeli, pb.alamat, pb.kota, pb.provinsi, pb.no_telp 
    FROM pesanan p
    JOIN pembeli pb ON p.id_pembeli = pb.id_pembeli 
    WHERE p.id_pesanan = ?
");
$stmt->bind_param("i", $pesanan_id);
$stmt->execute();
$result = $stmt->get_result();
$dataPesanan = $result->fetch_assoc();

if (!$dataPesanan) {
    header("Location: pesanan.php");
    exit();
}


$waktu_expired = date('Y-m-d H:i:s', strtotime($dataPesanan['tanggal_pesan'] . ' 23:59:59'));
$stmt2 = $conn->prepare("
    SELECT p.nama_produk, dp.harga_saat_beli, dp.jumlah 
    FROM detail_pesanan dp
    JOIN produk p ON dp.id_produk = p.id_produk
    WHERE dp.id_pesanan = ?
");
$stmt2->bind_param("i", $pesanan_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$produk = [];
while ($row = $result2->fetch_assoc()) {
    $produk[] = $row;
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Hitung waktu tersisa untuk pembayaran
$waktu_sekarang = new DateTime();
$waktu_expired_obj = new DateTime($waktu_expired);
$interval = $waktu_sekarang->diff($waktu_expired_obj);
$jam_tersisa = $interval->h;
$menit_tersisa = $interval->i;
$detik_tersisa = $interval->s;
$total_detik_tersisa = ($jam_tersisa * 3600) + ($menit_tersisa * 60) + $detik_tersisa;

// Generate kode pembayaran
$kode_gopay = "GOPAY-" . date('Ymd') . "-" . strtoupper(substr(md5('gopay' . $pesanan_id), 0, 6));
$kode_dana = "DANA-" . date('Ymd') . "-" . strtoupper(substr(md5('dana' . $pesanan_id), 0, 6));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - ToyToy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
        --primary: #0a1c4c;
        --primary-light: #14378a;
        --secondary: #f8f9fa;
        --danger: #e74c3c;
        --success: #2ecc71;
        --dark: #2c3e50;
        --light: #ffffff;
        --gray: #6c757d;
        --border: #e0e0e0;
    }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .payment-header {
            background: var(--primary);
            color: var(--light);
            padding: 16px 24px;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
        }

        .section {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .customer-info .info-item {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .customer-info .info-item strong {
            font-weight: 500;
            color: var(--dark);
        }

        .order-summary {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .order-summary th, 
        .order-summary td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .text-right {
            text-align: right;
        }

        .order-total {
            font-weight: 600;
            color: var(--primary);
        }

.payment-methods {
    display: flex;
    gap: 16px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.payment-method {
    border: 2px solid var(--border);
    border-radius: 8px;
    padding: 12px;
    width: 140px;
    text-align: center;
    cursor: pointer;
    transition: 0.3s ease;
}

.payment-method.selected {
    border-color: var(--primary);
    background-color: #f0f4ff;
}

.payment-method img {
    width: 60px;
    height: 40px;
    object-fit: contain;
    margin-bottom: 5px;
}

.payment-method input {
    display: none;
}


        .payment-code {
            font-size: 15px;
            font-weight: bold;
            color: var(--primary);
            padding: 10px;
            background: #eef3fb;
            text-align: center;
            border: 1px dashed var(--primary);
            border-radius: 6px;
            margin: 10px 0;
        }

        ol, ul {
            list-style: none;
            padding-left: 0;
        }

        .payment-instruction {
            display: none;
            background: #f0f2f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            animation: fadeIn 0.3s ease;
            font-size: 14px;
        }

        .payment-instruction.active {
            display: block;
        }

        .timer {
            text-align: center;
            margin-top: 10px;
            font-weight: 500;
            color: var(--danger);
            font-size: 14px;
        }

        .btn-confirm {
            margin: 20px 0;
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-confirm:hover {
            background: var(--primary-light);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="payment-header">
                <h2>Detail Pembayaran</h2>
            </div>
                
                <div class="section">
                    <div class="section-title">Informasi Pelanggan</div>
                    <div class="customer-info">
                        <div class="info-item"><strong>Nama:</strong> <?= htmlspecialchars($dataPesanan['nama_pembeli']) ?></div>
                        <div class="info-item"><strong>Alamat:</strong> <?= htmlspecialchars($dataPesanan['alamat']) ?>, <?= htmlspecialchars($dataPesanan['kota']) ?>, <?= htmlspecialchars($dataPesanan['provinsi']) ?></div>
                        <div class="info-item"><strong>No. HP:</strong> <?= htmlspecialchars($dataPesanan['no_telp']) ?></div>
                        <div class="info-item"><strong>Tanggal Pesanan:</strong> <?= date('d/m/Y', strtotime($dataPesanan['tanggal_pesan'])) ?></div>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">Ringkasan Pesanan</div>
                    <table class="order-summary">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Jumlah</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produk as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                <td class="text-right"><?= formatRupiah($item['harga_saat_beli']) ?></td>
                                <td class="text-right"><?= (int)$item['jumlah'] ?></td>
                                <td class="text-right"><?= formatRupiah($item['harga_saat_beli'] * $item['jumlah']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Ongkos Kirim (<?= htmlspecialchars($dataPesanan['metode_pengiriman']) ?>)</td>
                                <td class="text-right"><?= formatRupiah($dataPesanan['ongkir']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="order-total">Total Pembayaran</td>
                                <td class="text-right order-total"><?= formatRupiah($dataPesanan['total_harga']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
<div class="section">
    <div class="section-title">Metode Pembayaran</div>
    <div class="payment-methods">
        <div class="payment-method" onclick="selectPayment('gopay')">
            <input type="radio" name="payment" value="gopay" id="gopay" hidden>
            <img src="gambar/gopay.png" onerror="this.src='gambar/placeholder.png'" alt="GoPay">
            <div>GoPay</div>
        </div>
        <div class="payment-method" onclick="selectPayment('dana')">
            <input type="radio" name="payment" value="dana" id="dana" hidden>
            <img src="gambar/dana.png" onerror="this.src='gambar/placeholder.png'" alt="DANA">
            <div>DANA</div>
        </div>
    </div>
</div>

                    </div>
                    
                    <div id="gopay-instruction" class="payment-instruction">
                        <h3>Instruksi Pembayaran GoPay</h3>
                        <div class="payment-code"><?= htmlspecialchars($kode_gopay) ?></div>
                        <ol>
                            <li>Buka aplikasi Gojek dan pilih <strong>Bayar</strong></li>
                            <li>Masukkan kode pembayaran di atas atau scan QR code</li>
                            <li>Pastikan nominal pembayaran sesuai</li>
                            <li>Selesaikan pembayaran sebelum waktu habis</li>
                        </ol>
                        <div class="timer">Batas waktu pembayaran: <span id="gopay-timer"><?= date('H:i', strtotime($waktu_expired)) ?></span></div>
                    </div>
                    
                    <div id="dana-instruction" class="payment-instruction">
                        <h3>Instruksi Pembayaran DANA</h3>
                        <div class="payment-code"><?= htmlspecialchars($kode_dana) ?></div>
                        <ol>
                            <li>Buka aplikasi DANA dan pilih <strong>Bayar</strong></li>
                            <li>Masukkan kode pembayaran di atas</li>
                            <li>Pastikan nominal pembayaran sesuai</li>
                            <li>Selesaikan pembayaran sebelum waktu habis</li>
                        </ol>
                        <div class="timer">Batas waktu pembayaran: <span id="dana-timer"><?= date('H:i', strtotime($waktu_expired)) ?></span></div>
                    </div>
                    
                    <button class="btn-confirm" onclick="confirmPayment()">Konfirmasi Pembayaran</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timerInterval;
        const totalDetikTersisa = <?= $total_detik_tersisa ?>;

        function startTimer(duration, displayId) {
            let timer = duration;
            clearInterval(timerInterval);
            

            updateTimerDisplay(timer, displayId);
            
            timerInterval = setInterval(() => {
                timer--;
                updateTimerDisplay(timer, displayId);
                
                if (timer <= 0) {
                    clearInterval(timerInterval);
                    alert("Waktu pembayaran telah habis. Silakan ulangi proses checkout.");
                    window.location.href = "pesanan.php";
                }
            }, 1000);
        }
        
        function updateTimerDisplay(timer, displayId) {
            let hours = Math.floor(timer / 3600);
            let minutes = Math.floor((timer % 3600) / 60);
            let seconds = timer % 60;
            
            hours = String(hours).padStart(2, '0');
            minutes = String(minutes).padStart(2, '0');
            seconds = String(seconds).padStart(2, '0');
            
            document.getElementById(displayId).textContent = `${hours}:${minutes}:${seconds}`;
        }

        function selectPayment(method) {
            document.querySelectorAll('.payment-instruction').forEach(el => {
                el.classList.remove('active');
            });
            

            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            

            const instruction = document.getElementById(`${method}-instruction`);
            if (instruction) {
                instruction.classList.add('active');
                startTimer(totalDetikTersisa, `${method}-timer`);
            }
            
            event.currentTarget.classList.add('selected');
        }

        function confirmPayment() {
            const selectedMethod = document.querySelector('.payment-method.selected');
            
            if (!selectedMethod) {
                alert('Silakan pilih metode pembayaran terlebih dahulu.');
                return;
            }
            
            const method = selectedMethod.querySelector('input').value;
            window.location.href = `upload_bukti.php?pesanan_id=<?= $pesanan_id ?>&method=${method}`;
        }
        
        // otomatis pilih
        document.addEventListener('DOMContentLoaded', function() {
            const firstMethod = document.querySelector('.payment-method');
            if (firstMethod) {
                firstMethod.click();
            }
        });
    </script>
</body>
</html>