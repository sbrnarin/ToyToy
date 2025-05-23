<?php
include("koneksi.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data dari form
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

    // 1. Cek apakah pembeli sudah ada
    $stmt = $conn->prepare("SELECT id_pembeli FROM pembeli WHERE no_telp = ?");
    $stmt->bind_param("s", $nohp); // <-- gunakan "s" (string) karena no_telp sering pakai 0 di awal
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_pembeli = $row['id_pembeli'];
    } else {
        // Tambah pembeli baru
        $username = "user_" . time();
        $stmt = $conn->prepare("INSERT INTO pembeli (nama_pembeli, username, alamat, kota, no_telp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $username, $alamat, $kota, $nohp);
        $stmt->execute();
        $id_pembeli = $stmt->insert_id;
    }

    // 2. Simpan ke tabel pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (
        id_pembeli, 
        tanggal_transaksi, 
        status, 
        status_pembayaran, 
        metode_pembayaran, 
        total_harga) 
        VALUES (?, ?, 'tertunda', 'belum bayar', ?, ?)");
    $stmt->bind_param("issi", $id_pembeli, $tanggal, $metode, $total);
    $stmt->execute();
    $id_pesanan = $stmt->insert_id;

    // 3. Simpan ke detail_pesanan
    foreach ($produk as $p) {
        // Cek kelengkapan data produk
        if (
            !isset($p['id_produk'], $p['jumlah'], $p['harga']) ||
            !is_numeric($p['id_produk']) ||
            !is_numeric($p['jumlah']) ||
            !is_numeric($p['harga'])
        ) {
            continue; // skip data tidak valid
        }

        $id_produk = (int)$p['id_produk'];
        $jumlah    = (int)$p['jumlah'];
        $harga     = (int)$p['harga'];

        $stmt = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_beli) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $id_pesanan, $id_produk, $jumlah, $harga);
        $stmt->execute();
    }

    $conn->close();
    header("Location: pembayaran.html");
    exit();
}
?>
