<?php
include("koneksi.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = $_POST['nama'] ?? '';
    $alamat   = $_POST['alamat'] ?? '';
    $kota     = $_POST['kota'] ?? '';  
    $provinsi = $_POST['provinsi'] ?? '';
    $nohp     = $_POST['nohp'] ?? '';
    $produk   = isset($_POST['produk']) ? json_decode($_POST['produk'], true) : [];
    $total    = isset($_POST['total']) ? (int)$_POST['total'] : 0;
    $metode   = $_POST['shippingMethod'] ?? '';
    $tanggal  = date("Y-m-d");

    if (empty($produk) || !is_array($produk)) {
        die("Data produk tidak valid.");
    }

    // cek pembeli
    $stmt = $koneksi->prepare("SELECT id_pembeli FROM pembeli WHERE no_telp = ?");
    $stmt->bind_param("s", $nohp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_pembeli = $row['id_pembeli'];
    } else {
        $username = "user_" . time();
        $stmt = $koneksi->prepare("INSERT INTO pembeli (nama_pembeli, username, alamat, kota, no_telp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $username, $alamat, $kota, $nohp);
        $stmt->execute();
        $id_pembeli = $stmt->insert_id;
    }

    // simpan pesanan
    $stmt = $koneksi->prepare("INSERT INTO pesanan (id_pembeli, tanggal_transaksi, status, status_pembayaran, metode_pembayaran, total_harga) VALUES (?, ?, 'tertunda', 'belum bayar', ?, ?)");
    $stmt->bind_param("issi", $id_pembeli, $tanggal, $metode, $total);
    $stmt->execute();
    $id_pesanan = $stmt->insert_id;

    // simpan detail pesanan
    foreach ($produk as $p) {
        if (!isset($p['id_produk'], $p['jumlah'], $p['harga'])) continue;

        $id_produk = (int)$p['id_produk'];
        $jumlah    = (int)$p['jumlah'];
        $harga     = (int)$p['harga'];

        $stmt = $koneksi->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_beli) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $id_pesanan, $id_produk, $jumlah, $harga);
        $stmt->execute();
    }

    $koneksi->close();
    header("Location: pembayaran.html");
    exit();
}
?>
