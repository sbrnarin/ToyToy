<?php
$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$pesanan_id = $_GET['pesanan_id'] ?? 0;
$pesanan_id = (int)$pesanan_id;

$stmt = $conn->prepare("
    SELECT p.id_pesanan, p.total_harga, p.status_pembayaran, p.metode_pembayaran, p.ongkir, p.metode_pengiriman, p.kode_pembayaran,
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
    die("Pesanan tidak ditemukan.");
}

$stmt2 = $conn->prepare("
    SELECT dp.nama_produk AS name, dp.harga_saat_beli AS price, dp.jumlah AS quantity 
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pembayaran Pesanan #<?= htmlspecialchars($pesanan_id) ?></title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f7f7f7; 
            color: #333; 
            margin: 0; 
            padding: 20px; 
        }
        .payment-container { 
            max-width: 700px; 
            margin: auto; 
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
        h3 { 
            color: #2c3e50; 
            margin-top: 25px; 
            font-size: 1.2em; 
        }
        .customer-info, .payment-instruction { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            border-left: 4px solid #3498db; 
        }
        .customer-info p { 
            margin: 8px 0; 
            line-height: 1.5; 
        }
        .summary-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px; 
        }
        .summary-table th { 
            background: #2c3e50; 
            color: white; 
            padding: 12px 15px; 
            text-align: left; 
        }
        .summary-table td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #eee; 
        }
        .summary-table tfoot td { 
            font-weight: bold; 
            color: #2c3e50; 
            font-size: 1.1em; 
        }
        .payment-method { 
            margin-bottom: 25px; 
        }
        .payment-options { 
            display: flex; 
            gap: 10px; 
            flex-wrap: wrap; 
        }
        .payment-option { 
            flex: 1; 
            min-width: 120px; 
            padding: 15px; 
            border: 2px solid #eee; 
            border-radius: 8px; 
            text-align: center; 
            cursor: pointer; 
            transition: all 0.3s; 
        }
        .payment-option.selected { 
            border-color: #27ae60; 
            background: #e8f5e9; 
        }
        .payment-option img { 
            width: 60px; 
            height: 30px; 
            object-fit: contain; 
            margin-bottom: 10px; 
        }
        .payment-option input { 
            display: none; 
        }
        .payment-code { 
            font-size: 18px; 
            font-weight: bold; 
            background: #fff; 
            padding: 10px; 
            border: 1px dashed #ccc; 
            border-radius: 5px; 
            text-align: center; 
            margin: 10px 0; 
            user-select: all; 
        }
        .btn-confirm { 
            width: 100%; 
            padding: 14px; 
            background: #27ae60; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-size: 16px; 
            cursor: pointer; 
            transition: background 0.3s; 
        }
        .btn-confirm:hover { 
            background: #2ecc71; 
        }
        .timer { 
            text-align: center; 
            font-size: 18px; 
            color: #e74c3c; 
            margin-top: 10px; 
        }
        .payment-instruction { 
            display: none; 
        }
        .payment-instruction.active { 
            display: block; 
        }
        .ringkasan-pesanan {
    font-family: Arial, sans-serif;
    color: #333;
    line-height: 1.5;
}

.ringkasan-pesanan table {
    width: 100%;
    border-collapse: collapse;
}

.ringkasan-pesanan th, 
.ringkasan-pesanan td {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.ringkasan-pesanan .total-section {
    border-top: 1px solid #ddd;
    padding-top: 10px;
    margin-top: 10px;
}
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Detail Pembayaran</h2>
        
        <div class="customer-info">
            <p><strong>Nama:</strong> <?= htmlspecialchars($dataPesanan['nama_pembeli']) ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($dataPesanan['alamat']) ?>, <?= htmlspecialchars($dataPesanan['kota']) ?>, <?= htmlspecialchars($dataPesanan['provinsi']) ?></p>
            <p><strong>Nomor Handphone:</strong> <?= htmlspecialchars($dataPesanan['no_telp']) ?></p>
        </div>

<h3>Ringkasan Pesanan</h3>
<div style="margin-bottom: 20px;">
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <thead>
            <tr style="border-bottom: 1px solid #ddd;">
                <th style="text-align: left; padding: 8px 0; font-weight: normal;">Produk</th>
                <th style="text-align: right; padding: 8px 0; font-weight: normal;">Harga</th>
                <th style="text-align: center; padding: 8px 0; font-weight: normal;">Jumlah</th>
                <th style="text-align: right; padding: 8px 0; font-weight: normal;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produk as $item): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px 0;"><?= htmlspecialchars($item['name']) ?></td>
                <td style="text-align: right; padding: 8px 0;"><?= formatRupiah($item['price']) ?></td>
                <td style="text-align: center; padding: 8px 0;"><?= (int)$item['quantity'] ?></td>
                <td style="text-align: right; padding: 8px 0;"><?= formatRupiah($item['price'] * $item['quantity']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="border-top: 1px solid #ddd; padding-top: 10px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>Ongkos Kirim (<?= htmlspecialchars($dataPesanan['metode_pengiriman']) ?>)</span>
            <span><?= formatRupiah($dataPesanan['ongkir']) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1em; margin-top: 10px;">
            <span>Total Pembayaran</span>
            <span><?= formatRupiah($dataPesanan['total_harga']) ?></span>
        </div>
    </div>
</div>

        <div class="payment-method">
            <h3>Pilih Metode Pembayaran</h3>
            <div class="payment-options">
                <div class="payment-option" onclick="selectPayment('gopay')">
                    <input type="radio" name="payment" value="gopay" id="gopay">
                    <img src="assets/payment/gopay.png" alt="GoPay" />
                    <div>GoPay</div>
                </div>
                <div class="payment-option" onclick="selectPayment('dana')">
                    <input type="radio" name="payment" value="dana" id="dana">
                    <img src="assets/payment/dana.png" alt="DANA" />
                    <div>DANA</div>
                </div>
            </div>

            <div id="gopay-instruction" class="payment-instruction">
                <h3>Instruksi Pembayaran GoPay</h3>
                <div class="payment-code"></div>
                <ol>
                    <li>Buka aplikasi Gojek, pilih <strong>Bayar</strong>.</li>
                    <li>Masukkan kode pembayaran di atas.</li>
                </ol>
                <div class="timer">Batas waktu pembayaran: <span>10:00</span></div>
            </div>

            <div id="dana-instruction" class="payment-instruction">
                <h3>Instruksi Pembayaran DANA</h3>
                <div class="payment-code">DANA-<?= date('Ymd') ?>-<?= strtoupper(substr(md5($pesanan_id), 0, 6)) ?></div>
                <ol>
                    <li>Buka aplikasi DANA, pilih <strong>Bayar</strong>.</li>
                    <li>Masukkan kode pembayaran di atas.</li>
                </ol>
                <div class="timer">Batas waktu pembayaran: <span>10:00</span></div>
            </div>
        </div>

        <button class="btn-confirm" onclick="confirmPayment()">Konfirmasi Pembayaran</button>
    </div>

    <script>
        function generatePaymentCode(method) {
            const prefix = method === 'gopay' ? 'GOPAY' : 'DANA';
            const date = new Date();
            const ymd = date.getFullYear() + ('0' + (date.getMonth()+1)).slice(-2) + ('0' + date.getDate()).slice(-2);
            const random = Math.random().toString(36).substring(2, 8).toUpperCase();
            return `${prefix}-${ymd}-${random}`;
        }

        function startTimer(duration, display) {
            let timer = duration;
            clearInterval(window.timerInterval);
            window.timerInterval = setInterval(() => {
                let minutes = String(Math.floor(timer / 60)).padStart(2, '0');
                let seconds = String(timer % 60).padStart(2, '0');
                display.textContent = `${minutes}:${seconds}`;
                if (--timer < 0) {
                    clearInterval(window.timerInterval);
                    alert("Waktu pembayaran telah habis. Silakan ulangi proses checkout.");
                    window.location.href = "checkout.html";
                }
            }, 1000);
        }

        function selectPayment(method) {
            // Sembunyikan semua instruksi terlebih dahulu
            document.querySelectorAll('.payment-instruction').forEach(inst => {
                inst.classList.remove('active');
            });
            
            // Hapus selected dari semua opsi
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Tandai opsi yang dipilih
            document.querySelector(`[onclick="selectPayment('${method}')"]`).classList.add('selected');
            
            // Tampilkan instruksi yang sesuai
            const instruction = document.getElementById(`${method}-instruction`);
            if (instruction) {
                instruction.classList.add('active');
                const code = method === 'dana' 
                    ? 'DANA-<?= date('Ymd') ?>-<?= strtoupper(substr(md5($pesanan_id), 0, 6)) ?>'
                    : generatePaymentCode(method);
                instruction.querySelector('.payment-code').textContent = code;
                startTimer(600, instruction.querySelector('.timer span'));
            }
        }


    function confirmPayment() {
        const selectedMethod = document.querySelector('.payment-option.selected');
        if (!selectedMethod) {
            alert('Silakan pilih metode pembayaran terlebih dahulu.');
            return;
        }
        
        // Ambil metode pembayaran yang dipilih
        const method = selectedMethod.querySelector('input').value;
        
        // Redirect ke halaman upload bukti pembayaran dengan membawa data pesanan
        window.location.href = `upload_bukti.php?pesanan_id=<?= $pesanan_id ?>&method=${method}`;
    }
</script>
    </script>
</body>
</html>