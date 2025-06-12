<?php
session_start();
include "db_config.php";

$conn = new mysqli("localhost", "root", "", "sabrinalina");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($conn->real_escape_string($_POST['nama'] ?? ''));
    $alamat     = trim($conn->real_escape_string($_POST['alamat'] ?? ''));
    $kota       = trim($conn->real_escape_string($_POST['kota'] ?? ''));
    $provinsi   = trim($conn->real_escape_string($_POST['provinsi'] ?? ''));
    $no_telp    = trim($conn->real_escape_string($_POST['nohp'] ?? ''));
    $produkJson = $_POST['produk'] ?? '[]';
    $ongkir     = isset($_POST['shippingCost']) ? (int)$_POST['shippingCost'] : 0;
    $metode_pengiriman = trim($conn->real_escape_string($_POST['shippingMethod'] ?? ''));
    $metode_pembayaran = '';
    $tanggal_pesan = date('Y-m-d');
    $waktu_expired = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $allowed_metode = ['Dana', 'Gopay'];
    if (!in_array($metode_pembayaran, $allowed_metode)) {
        $metode_pembayaran = 'Dana';
    }

    $produkList = json_decode($produkJson, true);
    if (!is_array($produkList) || empty($produkList)) {
        die(json_encode(['error' => 'Data produk tidak valid.']));
    }

    $id_akun = $_SESSION['id_akun'] ?? null;
    if (!$id_akun) {
        die("Silakan login terlebih dahulu.");
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("SELECT id_pembeli FROM pembeli WHERE id_akun = ?");
        $stmt->bind_param("i", $id_akun);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id_pembeli);
            $stmt->fetch();
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO pembeli (id_akun, nama_pembeli, alamat, kota, provinsi, no_telp) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtInsert->bind_param("isssss", $id_akun, $nama, $alamat, $kota, $provinsi, $no_telp);
            $stmtInsert->execute();
            $id_pembeli = $stmtInsert->insert_id;
            $stmtInsert->close();
        }
        $stmt->close();

        $status_pengiriman = "tertunda";
        $status_pembayaran = "belum bayar";

        $total_produk = 0;
        foreach ($produkList as $item) {
            $jumlah = (int)($item['quantity'] ?? 1);
            $total_produk += $jumlah;
        }
        $total_harga = 0;
        foreach ($produkList as $item) {
            $jumlah = (int)($item['quantity'] ?? 1);
            $harga = (float)($item['price'] ?? 0);
            $total_harga += $jumlah * $harga;
        }

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

        $stmtDetail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_beli) VALUES (?, ?, ?, ?)");

        foreach ($produkList as $item) {
            $id_produk = (int)($item['id_produk'] ?? 0);
            $jumlah = (int)($item['quantity'] ?? 1);
            $harga = (float)($item['price'] ?? 0);

            if ($id_produk <= 0) continue;

            $cekProduk = $conn->prepare("SELECT id_produk FROM produk WHERE id_produk = ?");
            $cekProduk->bind_param("i", $id_produk);
            $cekProduk->execute();
            $res = $cekProduk->get_result();
            if ($res->num_rows === 0) {
                $cekProduk->close();
                continue;
            }
            $cekProduk->close();

            $stmtDetail->bind_param("iiid", $id_pesanan, $id_produk, $jumlah, $harga);
            $stmtDetail->execute();
        }

        $stmtDetail->close();
        $conn->commit();

        header("Location: pembayaran.php?pesanan_id=" . urlencode($id_pesanan));
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
