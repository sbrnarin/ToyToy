<?php
session_start();
include "db_config.php";

$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_akun = $_SESSION['id_akun'] ?? null;
    if (!$id_akun) die("Silakan login terlebih dahulu.");

    $stmt = $conn->prepare("SELECT id_pembeli FROM pembeli WHERE id_akun = ?");
    $stmt->bind_param("i", $id_akun);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_pembeli);
        $stmt->fetch();
    } else {
        $nama     = trim($conn->real_escape_string($_POST['nama'] ?? ''));
        $alamat   = trim($conn->real_escape_string($_POST['alamat'] ?? ''));
        $kota     = trim($conn->real_escape_string($_POST['kota'] ?? ''));
        $provinsi = trim($conn->real_escape_string($_POST['provinsi'] ?? ''));
        $no_telp  = trim($conn->real_escape_string($_POST['nohp'] ?? ''));

        $stmtInsert = $conn->prepare("INSERT INTO pembeli (id_akun, nama_pembeli, alamat, kota, provinsi, no_telp) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("isssss", $id_akun, $nama, $alamat, $kota, $provinsi, $no_telp);
        $stmtInsert->execute();
        $id_pembeli = $stmtInsert->insert_id;
        $stmtInsert->close();
    }
    $stmt->close();

    $produkJson = $_POST['produk'] ?? '[]';
    $produkList = json_decode($produkJson, true);
    if (!is_array($produkList) || empty($produkList)) {
        die(json_encode(['error' => 'Data produk tidak valid.']));
    }

    $ongkir             = (int)($_POST['shippingCost'] ?? 0);
    $metode_pengiriman  = trim($conn->real_escape_string($_POST['shippingMethod'] ?? ''));
    $metode_pembayaran  = ''; // default kosong dulu
    $tanggal_pesan      = date('Y-m-d');
    $waktu_expired      = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $allowed_metode = ['Dana', 'Gopay'];
    if (!in_array($metode_pembayaran, $allowed_metode)) {
        $metode_pembayaran = 'Dana';
    }

    $status_pengiriman  = "tertunda";
    $status_pembayaran  = "belum bayar";

    // Hitung total
    $total_produk = 0;
    $total_harga  = 0;
    $productIds   = [];

    foreach ($produkList as $item) {
        $jumlah = (int)($item['quantity'] ?? 1);
        $harga  = (float)($item['price'] ?? 0);
        $total_produk += $jumlah;
        $total_harga  += $jumlah * $harga;
        if (isset($item['id_produk'])) {
            $productIds[] = (int)$item['id_produk'];
        }
    }

    // Transaksi mulai
    $conn->begin_transaction();

    try {
        // Masukkan ke tabel pesanan
        $stmtPesanan = $conn->prepare("INSERT INTO pesanan (
            id_pembeli, tanggal_pesan, status_pengiriman, total_harga, total_produk, 
            status_pembayaran, metode_pembayaran, ongkir, 
            metode_pengiriman, waktu_expired
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmtPesanan->bind_param(
            "issississs",
            $id_pembeli, $tanggal_pesan, $status_pengiriman, $total_harga, $total_produk,
            $status_pembayaran, $metode_pembayaran, $ongkir,
            $metode_pengiriman, $waktu_expired
        );

        $stmtPesanan->execute();
        $id_pesanan = $stmtPesanan->insert_id;
        $stmtPesanan->close();

        // Masukkan ke detail_pesanan
        $stmtDetail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_beli) VALUES (?, ?, ?, ?)");
        foreach ($produkList as $item) {
            $id_produk = (int)($item['id_produk'] ?? 0);
            $jumlah = (int)($item['quantity'] ?? 1);
            $harga = (float)($item['price'] ?? 0);
            if ($id_produk <= 0) continue;

            $stmtDetail->bind_param("iiid", $id_pesanan, $id_produk, $jumlah, $harga);
            $stmtDetail->execute();
        }
        $stmtDetail->close();

        $tanggal_penjualan = date('Y-m-d');
        $stmtPenjualan = $conn->prepare("INSERT INTO penjualan (tanggal) VALUES (?)");
        $stmtPenjualan->bind_param("s", $tanggal_penjualan);
        $stmtPenjualan->execute();
        $id_jual = $stmtPenjualan->insert_id;
        $stmtPenjualan->close();

        $stmtDetailJual = $conn->prepare("INSERT INTO detail_penjualan (id_jual, id_produk, jumlah, harga) VALUES (?, ?, ?, ?)");
        foreach ($produkList as $item) {
            $id_produk = (int)($item['id_produk'] ?? 0);
            $jumlah = (int)($item['quantity'] ?? 1);
            $harga = (float)($item['price'] ?? 0);
            if ($id_produk <= 0) continue;

            $stmtDetailJual->bind_param("iiid", $id_jual, $id_produk, $jumlah, $harga);
            $stmtDetailJual->execute();
        }
        $stmtDetailJual->close();

        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $types = str_repeat('i', count($productIds));
            $stmtDelete = $conn->prepare("DELETE FROM keranjang WHERE id_pembeli = ? AND id_produk IN ($placeholders)");
            $stmtDelete->bind_param("i" . $types, $id_pembeli, ...$productIds);
            $stmtDelete->execute();
            $stmtDelete->close();
        }

        $conn->commit();

        echo "<script>
            localStorage.removeItem('cart');
            window.location.href = 'pembayaran.php?pesanan_id=$id_pesanan';
        </script>";
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Terjadi kesalahan saat proses checkout: " . $e->getMessage());
    }

} else {
    http_response_code(405);
    echo "Metode tidak diperbolehkan";
}
?>
