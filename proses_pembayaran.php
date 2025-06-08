<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Koneksi database gagal']));
}

function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Set header untuk response JSON
header('Content-Type: application/json');

// Ambil data dari POST
$pesanan_id = intval($_POST['pesanan_id'] ?? 0);
$metode_pembayaran = clean_input($_POST['metode_pembayaran'] ?? '');

// Validasi input
if ($pesanan_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID pesanan tidak valid']);
    exit;
}

if (!in_array($metode_pembayaran, ['gopay', 'dana'])) {
    echo json_encode(['success' => false, 'message' => 'Metode pembayaran tidak valid']);
    exit;
}

// Generate payment code
$paymentCode = '';
if ($metode_pembayaran === 'dana') {
    $paymentCode = 'DANA-' . date('Ymd') . '-' . strtoupper(substr(md5($pesanan_id), 0, 6));
} else if ($metode_pembayaran === 'gopay') {
    $paymentCode = 'GOPAY-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

$conn->begin_transaction();

try {
    // Update data pesanan
    $statusPembayaran = 'menunggu';
    
    $stmt = $conn->prepare("
        UPDATE pesanan 
        SET metode_pembayaran = ?, 
            kode_pembayaran = ?,
            status_pembayaran = ?,
            waktu_pembayaran = NOW()
        WHERE id_pesanan = ?
    ");
    $stmt->bind_param("sssi", $metode_pembayaran, $paymentCode, $statusPembayaran, $pesanan_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal memperbarui data pesanan");
    }
    $stmt->close();

    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'payment_code' => $paymentCode,
        'redirect_url' => "upload_bukti.php?pesanan_id=$pesanan_id"
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

$conn->close();
?>