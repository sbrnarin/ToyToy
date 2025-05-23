<?php
include("koneksi.php");  // Pastikan file ini menginisialisasi $conn dengan benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data dari form
    $nama     = $_POST['nama'];
    $alamat   = $_POST['alamat'];
    $kota     = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $nohp     = $_POST['nohp'];
    $produk   = json_decode($_POST['produk'], true); // Produk dalam bentuk JSON
    $total    = (float) $_POST['total']; // Pastikan ini float
    $metode   = $_POST['shippingMethod'];
    $tanggal  = date("Y-m-d");

    // 1. Cek apakah pembeli sudah ada berdasarkan no_telp
    $stmt = $conn->prepare("SELECT id_pembeli FROM pembeli WHERE no_telp = ?");
    $stmt->bind_param("s", $nohp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Pembeli sudah ada
        $row = $result->fetch_assoc();
        $id_pembeli = $row['id_pembeli'];
    } else {
        // Tambah pembeli baru
        $username = "user_" . time(); // Username unik
        $stmt = $conn->prepare("INSERT INTO pembeli (nama_pembeli, username, alamat, kota, no_telp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $username, $alamat, $kota, $nohp);
        $stmt->execute();
        $id_pembeli = $stmt->insert_id;
    }

    // 2. Simpan ke tabel pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (id_pembeli, tanggal_transaksi, status, status_pembayaran, metode_pembayaran, total, kota)
                            VALUES (?, ?, 'tertunda', 'belum bayar', ?, ?, ?)");
    $stmt->bind_param("issds", $id_pembeli, $tanggal, $metode, $total, $kota);
    $stmt->execute();
    $id_pesanan = $stmt->insert_id;

    // 3. Simpan ke tabel detail_pesanan
    $id_produk = $produk['id'];
    $jumlah    = $produk['quantity'];
    $harga     = $produk['price'];

    $stmt = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_beli) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $id_pesanan, $id_produk, $jumlah, $harga);
    $stmt->execute();

    // Selesai
    $conn->close();
    header("Location: pembayaran.html");
    exit();
}
?>
