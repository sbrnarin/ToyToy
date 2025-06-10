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
    SELECT p.id_pesanan, p.total_harga, p.status_pembayaran, 
           p.metode_pembayaran, p.tanggal_transaksi, p.ongkir, 
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

// Hitung waktu expired (hingga jam 23:59:59 di hari transaksi)
$waktu_expired = date('Y-m-d H:i:s', strtotime($dataPesanan['tanggal_transaksi'] . ' 23:59:59'));

// Ambil item produk dalam pesanan
$stmt2 = $conn->prepare("
    SELECT dp.nama_produk, dp.harga_saat_beli, dp.jumlah 
    FROM detail_pesanan dp
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
    <title>Pembayaran - Sabrina Lina</title>
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 15px;
        }
        
        .payment-card {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .payment-header {
            background: var(--primary);
            color: var(--light);
            padding: 20px;
            text-align: center;
        }
        
        .payment-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .payment-body {
            padding: 25px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }
        
        .customer-info {
            background: var(--secondary);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-item strong {
            font-weight: 500;
        }
        
        .order-summary {
            width: 100%;
            border-collapse: collapse;
        }
        
        .order-summary th, 
        .order-summary td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        
        .order-summary th {
            background: var(--secondary);
            font-weight: 500;
        }
        
        .order-summary tbody tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .order-total {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary);
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .payment-method {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .payment-method:hover {
            border-color: var(--primary-light);
        }
        
        .payment-method.selected {
            border-color: var(--primary);
            background-color: rgba(10, 28, 76, 0.05);
        }
        
        .payment-method img {
            width: 80px;
            height: 50px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        
        .payment-method input {
            display: none;
        }
        
        .payment-instruction {
            display: none;
            background: var(--secondary);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            animation: fadeIn 0.3s ease;
        }
        
        .payment-instruction.active {
            display: block;
        }
        
        .payment-code {
            font-size: 1.2rem;
            font-weight: 600;
            background: var(--light);
            padding: 12px;
            border: 1px dashed var(--gray);
            border-radius: 6px;
            text-align: center;
            margin: 15px 0;
            user-select: all;
            color: var(--primary);
        }
        
        .timer {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--danger);
            text-align: center;
            margin-top: 15px;
        }
        
        .btn-confirm {
            display: block;
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: var(--light);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 25px;
        }
        
        .btn-confirm:hover {
            background: var(--primary-light);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffeeba;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #bee5eb;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .payment-methods {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            
            .order-summary th, 
            .order-summary td {
                padding: 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="payment-header">
                <h2>Detail Pembayaran</h2>
            </div>
            
            <div class="payment-body">
                <?php if ($dataPesanan['status_pembayaran'] === 'belum bayar' || $dataPesanan['status_pembayaran'] === NULL): ?>
                <div class="alert alert-warning">
                    Silakan selesaikan pembayaran sebelum <?= date('d/m/Y H:i', strtotime($waktu_expired)) ?> untuk menghindari pembatalan otomatis.
                </div>
                <?php endif; ?>
                
                <div class="section">
                    <div class="section-title">Informasi Pelanggan</div>
                    <div class="customer-info">
                        <div class="info-item"><strong>Nama:</strong> <?= htmlspecialchars($dataPesanan['nama_pembeli']) ?></div>
                        <div class="info-item"><strong>Alamat:</strong> <?= htmlspecialchars($dataPesanan['alamat']) ?>, <?= htmlspecialchars($dataPesanan['kota']) ?>, <?= htmlspecialchars($dataPesanan['provinsi']) ?></div>
                        <div class="info-item"><strong>No. HP:</strong> <?= htmlspecialchars($dataPesanan['no_telp']) ?></div>
                        <div class="info-item"><strong>Tanggal Pesanan:</strong> <?= date('d/m/Y', strtotime($dataPesanan['tanggal_transaksi'])) ?></div>
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
                
                <?php if ($dataPesanan['status_pembayaran'] === 'belum bayar' || $dataPesanan['status_pembayaran'] === NULL): ?>
                <div class="section">
                    <div class="section-title">Metode Pembayaran</div>
                    <div class="payment-methods">
                        <div class="payment-method" onclick="selectPayment('gopay')">
                            <input type="radio" name="payment" value="gopay" id="gopay">
                            <img src="gambar/gopay.png" onerror="this.src='gambar/placeholder.png'" alt="GoPay">
                            <div>GoPay</div>
                        </div>
                        <div class="payment-method" onclick="selectPayment('dana')">
                            <input type="radio" name="payment" value="dana" id="dana">
                            <img src="gambar/dana.png" onerror="this.src='gambar/placeholder.png'" alt="DANA">
                            <div>DANA</div>
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
                <?php else: ?>
                <div class="alert alert-info">
                    Status pembayaran: <strong><?= ucfirst($dataPesanan['status_pembayaran']) ?></strong>
                </div>
                <?php endif; ?>
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