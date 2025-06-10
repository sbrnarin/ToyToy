<?php
include "db_config.php";
$conn = new mysqli("localhost", "root", "", "sabrinalina");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi dan sanitasi input
    $nama = trim($conn->real_escape_string($_POST['nama'] ?? ''));
    $alamat = trim($conn->real_escape_string($_POST['alamat'] ?? ''));
    $kota = trim($conn->real_escape_string($_POST['kota'] ?? ''));
    $provinsi = trim($conn->real_escape_string($_POST['provinsi'] ?? ''));
    $no_telp = trim($conn->real_escape_string($_POST['nohp'] ?? ''));
    $produkJson = $_POST['produk'] ?? '[]';
    $total = isset($_POST['total']) ? (int)$_POST['total'] : 0;
    $metode_pembayaran = trim($conn->real_escape_string($_POST['payment_method'] ?? 'tunai'));
    $ongkir = isset($_POST['ongkir']) ? (int)$_POST['ongkir'] : 0;
    $metode_pengiriman = trim($conn->real_escape_string($_POST['metode_pengiriman'] ?? ''));
    $tanggal_pesan = date('Y-m-d');
    $waktu_expired = date('Y-m-d H:i:s', strtotime('+1 hour')); // Batas waktu pembayaran 1 jam

    $produkList = json_decode($produkJson, true);
    if (!is_array($produkList) || empty($produkList)) {
        die(json_encode(['error' => 'Data produk tidak valid.']));
    }

    $conn->begin_transaction();

    try {
        // Cek atau tambahkan pembeli
        $stmt = $conn->prepare("SELECT id_pembeli FROM pembeli WHERE no_telp = ?");
        $stmt->bind_param("s", $no_telp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id_pembeli);
            $stmt->fetch();
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO pembeli (nama_pembeli, alamat, kota, provinsi, no_telp) VALUES (?, ?, ?, ?, ?)");
            $stmtInsert->bind_param("sssss", $nama, $alamat, $kota, $provinsi, $no_telp);
            $stmtInsert->execute();
            $id_pembeli = $stmtInsert->insert_id;
            $stmtInsert->close();
        }
        $stmt->close();

        // Insert pesanan
        $status_pengiriman = "tertunda";
        $status_pembayaran = "belum bayar";
        
        $stmtPesanan = $conn->prepare("INSERT INTO pesanan (
            id_pembeli, tanggal_pesan, status_pengiriman, total_harga, 
            status_pembayaran, metode_pembayaran, ongkir, 
            metode_pengiriman, waktu_expired
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmtPesanan->bind_param(
            "issississ", 
            $id_pembeli, $tanggal_pesan, $status_pengiriman, $total,
            $status_pembayaran, $metode_pembayaran, $ongkir,
            $metode_pengiriman, $waktu_expired
        );
        
        $stmtPesanan->execute();
        $id_pesanan = $stmtPesanan->insert_id;
        $stmtPesanan->close();

        // Insert detail pesanan
        $stmtDetail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_beli) VALUES (?, ?, ?, ?)");

        foreach ($produkList as $item) {
            $id_produk = (int)($item['id_produk'] ?? 0);
            $jumlah = (int)($item['quantity'] ?? 1);
            $harga = (float)($item['price'] ?? 0);

            if ($id_produk <= 0) continue;

            // Validasi produk
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