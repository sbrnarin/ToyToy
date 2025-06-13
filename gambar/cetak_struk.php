<?php
$conn = new mysqli("localhost", "root", "", "sabrinalina");
$pesanan_id = (int)($_GET['pesanan_id'] ?? 0);
if ($pesanan_id <= 0) die("ID pesanan tidak valid.");

// Ambil data pesanan dan pembeli
$stmt = $conn->prepare("SELECT p.*, pb.nama_pembeli, pb.alamat, pb.kota, pb.provinsi, pb.no_telp 
                        FROM pesanan p JOIN pembeli pb ON p.id_pembeli = pb.id_pembeli 
                        WHERE p.id_pesanan = ?");
$stmt->bind_param("i", $pesanan_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ambil produk
$produk = [];
$stmt = $conn->prepare("SELECT p.nama_produk, dp.harga_saat_beli, dp.jumlah 
                        FROM detail_pesanan dp JOIN produk p ON dp.id_produk = p.id_produk 
                        WHERE dp.id_pesanan = ?");
$stmt->bind_param("i", $pesanan_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $produk[] = $row;

function rupiah($num) {
    return "Rp " . number_format($num, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Struk Pembayaran</title>
  <style>
    body { font-family: monospace; padding: 20px; }
    h2, h4 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    td, th { padding: 6px; font-size: 13px; }
    .total { font-weight: bold; border-top: 1px dashed #000; }
    .right { text-align: right; }
  </style>
</head>
<body onload="window.print()">
  <h2>Struk Pembayaran</h2>
  <h4>ID Pesanan: <?= $data['id_pesanan'] ?> | <?= date('d/m/Y', strtotime($data['tanggal_pesan'])) ?></h4>
  
  <p>
    <strong>Pembeli:</strong><br>
    <?= $data['nama_pembeli'] ?><br>
    <?= $data['alamat'] ?>, <?= $data['kota'] ?>, <?= $data['provinsi'] ?><br>
    <?= $data['no_telp'] ?>
  </p>

  <table>
    <thead>
      <tr>
        <th>Produk</th>
        <th class="right">Harga</th>
        <th class="right">Qty</th>
        <th class="right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($produk as $item): ?>
      <tr>
        <td><?= $item['nama_produk'] ?></td>
        <td class="right"><?= rupiah($item['harga_saat_beli']) ?></td>
        <td class="right"><?= $item['jumlah'] ?></td>
        <td class="right"><?= rupiah($item['harga_saat_beli'] * $item['jumlah']) ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="3" class="right">Ongkir</td>
        <td class="right"><?= rupiah($data['ongkir']) ?></td>
      </tr>
      <tr>
        <td colspan="3" class="right total">Total</td>
        <td class="right total"><?= rupiah($data['total_harga']) ?></td>
      </tr>
      <tr>
        <td colspan="3" class="right">Metode Pembayaran</td>
        <td class="right"><?= ucfirst($data['metode_pembayaran']) ?></td>
      </tr>
      <tr>
        <td colspan="3" class="right">Status Pembayaran</td>
        <td class="right"><?= ucfirst($data['status_pembayaran']) ?></td>
      </tr>
    </tbody>
  </table>

  <p style="text-align:center; margin-top:30px;">Terima kasih telah berbelanja di ToyToyShop!</p>
</body>
</html>
