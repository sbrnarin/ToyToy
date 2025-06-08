<?php
// koneksi
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sabrinalina";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $kota = trim($_POST['kota'] ?? '');
    $provinsi = trim($_POST['provinsi'] ?? '');
    $no_telp = trim($_POST['nohp'] ?? '');
    $produkJson = $_POST['produk'] ?? '[]';
    $total = isset($_POST['total']) ? (int)$_POST['total'] : 0;
    $metode_pembayaran = trim($_POST['payment_method'] ?? 'tunai'); // sesuai form
    $tanggal_transaksi = date('Y-m-d');

    $produkList = json_decode($produkJson, true);
    if (!is_array($produkList) || empty($produkList)) {
        die("Data produk tidak valid.");
    }

    $conn->begin_transaction();

    try {
        // cek pembeli berdasarkan no_telp
        $stmt = $conn->prepare("SELECT id_pembeli FROM pembeli WHERE no_telp = ?");
        $stmt->bind_param("s", $no_telp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id_pembeli);
            $stmt->fetch();
        } else {
            // insert pembeli baru
            $stmtInsert = $conn->prepare("INSERT INTO pembeli (nama_pembeli, alamat, kota, provinsi, no_telp) VALUES (?, ?, ?, ?, ?)");
            $stmtInsert->bind_param("sssss", $nama, $alamat, $kota, $provinsi, $no_telp);
            $stmtInsert->execute();
            $id_pembeli = $stmtInsert->insert_id;
            $stmtInsert->close();
        }
        $stmt->close();

        $status = "tertunda";
        $status_pembayaran = "belum bayar";

        // insert pesanan
        $stmtPesanan = $conn->prepare("INSERT INTO pesanan (id_pembeli, tanggal_transaksi, status, total_harga, status_pembayaran, metode_pembayaran) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtPesanan->bind_param("ississ", $id_pembeli, $tanggal_transaksi, $status, $total, $status_pembayaran, $metode_pembayaran);
        $stmtPesanan->execute();
        $id_pesanan = $stmtPesanan->insert_id;
        $stmtPesanan->close();

        // insert detail pesanan
        $stmtDetail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_beli) VALUES (?, ?, ?, ?)");

        foreach ($produkList as $item) {
            $id_produk = (int)($item['id_produk'] ?? 0);
            $jumlah = (int)($item['quantity'] ?? 1);  // kamu bisa sesuaikan key, misal 'quantity' sesuai form JSON produk
            $harga = (int)($item['price'] ?? 0);      // sesuaikan dengan key harga

            if ($id_produk <= 0) continue;

            // cek produk ada tidak
            $cekProduk = $conn->prepare("SELECT id_produk FROM produk WHERE id_produk = ?");
            $cekProduk->bind_param("i", $id_produk);
            $cekProduk->execute();
            $res = $cekProduk->get_result();
            if ($res->num_rows === 0) {
                $cekProduk->close();
                continue;
            }
            $cekProduk->close();

            $stmtDetail->bind_param("iiii", $id_pesanan, $id_produk, $jumlah, $harga);
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
