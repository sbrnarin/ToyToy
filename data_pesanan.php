<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include "koneksi.php";

$sql = "SELECT pesanan.*, pembeli.nama_pembeli 
        FROM pesanan 
        JOIN pembeli ON pesanan.id_pembeli = pembeli.id_pembeli
        ORDER BY pesanan.id_pesanan DESC";
$query = mysqli_query($koneksi, $sql);
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pesanan - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f1f1f1;
            margin: 0;
            color: #333;
        }

        .sidebar {
            width: 230px;
            background-color: #081F5C;
            height: 100vh;
            position: fixed;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar .logo img {
            width: 130px;
            margin-bottom: 30px;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar nav ul li {
            margin-bottom: 20px;
        }

        .sidebar nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .sidebar nav ul li a:hover {
            background-color: #000000;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 40px;
            min-height: 100vh;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }
        
        .container {
            max-width: 100%;
            overflow-x: auto;
        }

        
        h2 {
            margin-top: 0;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: #081F5C;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        th, td {
            padding: 8px 10px;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        
        th {
            background-color: #081F5C;
            color: white;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .status-select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-weight: 600;
            cursor: pointer;
            min-width: 150px;
        }
        
        .status-select:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        .belum-diverifikasi {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .sudah-diverifikasi {
            background-color: #d4edda;
            color: #155724;
        }
        
        .batal-verifikasi {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .belum-diproses {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .dikemas {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .dikirim {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .selesai {
            background-color: #d4edda;
            color: #155724;
        }
        
        .bukti-transfer {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: transform 0.3s;
        }
        
        .bukti-transfer:hover {
            transform: scale(1.5);
        }
        
      .aksi-btn {
    display: flex;
    gap: 8px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    white-space: nowrap;
    width: 140px; 
    margin: 0 auto;
}

.aksi-btn button {
    padding: 8px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    width: auto;
    min-width: unset;
}

.aksi-btn a,
.aksi-btn form {
    display: inline-flex;
}

        .aksi-btn button.lihat {
            background-color: #007bff;
            color: white;
        }
        
        .aksi-btn button.hapus {
            background-color: #dc3545;
            color: white;
        }
        
        .aksi-btn button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .no-bukti {
            color: #6c757d;
            font-style: italic;
        }

        @media (max-width: 768px) {
    .main-content {
        padding: 20px;
    }

    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .container {
        padding: 0 10px;
    }

    table {
        font-size: 13px;

}

    </style>
</head>
<body>
<aside class="sidebar">
    <div class="logo">
      <img src="gambar/Kids Toys Logo (1).png" alt="Kids Toys Logo">
    </div>
    <nav>
      <ul>
        <li><a href="dash_admin.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="admin.php"><i class="fas fa-box"></i> Product</a></li>
        <li><a href="akun_admin.php"><i class="fas fa-user"></i> Account</a></li>
      </ul>
    </nav>
</aside>

<div class="main-content">
    <div class="container">
        <h2><i class="fas fa-clipboard-list"></i> Data Pesanan</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pembeli</th>
                    <th>Tanggal Pesan</th>
                    <th>Total Produk</th>
                    <th>Total Harga</th>
                    <th>Status Pengiriman</th>
                    <th>Bukti Transfer</th>
                    <th>Status Pembayaran</th>
                    <th>Metode Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($pesanan = mysqli_fetch_assoc($query)) { 
                    $isVerified = strtolower(trim($pesanan['status_pembayaran'])) === 'sudah diverifikasi';
                ?>
                <tr>
                    <td><?= $pesanan['id_pesanan'] ?></td>
                    <td><?= htmlspecialchars($pesanan['nama_pembeli']) ?></td>
                    <td><?= date('d/m/Y', strtotime($pesanan['tanggal_pesan'])) ?></td>
                    <td><?= $pesanan['total_produk'] ?></td>
                    <td>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></td>
                    <td>
                        <select class="status-select <?= strtolower(str_replace(' ', '-', $pesanan['status_pengiriman'])) ?>" 
                                data-id="<?= $pesanan['id_pesanan'] ?>"
                                <?= !$isVerified ? 'disabled' : '' ?>
                                data-old-value="<?= $pesanan['status_pengiriman'] ?>">
                            <option value="Belum Diproses" <?= $pesanan['status_pengiriman'] == 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
                            <option value="Dikemas" <?= $pesanan['status_pengiriman'] == 'Dikemas' ? 'selected' : '' ?>>Dikemas</option>
                            <option value="Dikirim" <?= $pesanan['status_pengiriman'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="Selesai" <?= $pesanan['status_pengiriman'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </td>
                    <td>
                        <?php if (!empty($pesanan['bukti_pembayaran'])) : ?>
                            <a href="uploads/bukti_pembayaran/<?= $pesanan['bukti_pembayaran'] ?>" target="_blank">
                                <img src="uploads/bukti_pembayaran/<?= $pesanan['bukti_pembayaran'] ?>" class="bukti-transfer" alt="Bukti Transfer">
                            </a>
                        <?php else : ?>
                            <span class="no-bukti">Belum Upload</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="update_status_pembayaran.php" method="POST" class="payment-status-form">
                            <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                            <select name="status_pembayaran" class="status-select <?= strtolower(str_replace(' ', '-', $pesanan['status_pembayaran'])) ?>">
                                <option value="Belum Diverifikasi" <?= $pesanan['status_pembayaran'] == 'Belum Diverifikasi' ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                <option value="Sudah Diverifikasi" <?= $pesanan['status_pembayaran'] == 'Sudah Diverifikasi' ? 'selected' : '' ?>>Sudah Diverifikasi</option>
                                <option value="Batal Verifikasi" <?= $pesanan['status_pembayaran'] == 'Batal Verifikasi' ? 'selected' : '' ?>>Batal Verifikasi</option>
                            </select>
                        </form>
                    </td>
                    <td><?= $pesanan['metode_pembayaran'] ?></td>
                    <td class="aksi-btn">
                        <a href="detail_pesanan.php?id_pesanan=<?= $pesanan['id_pesanan'] ?>">
                            <button class="lihat"><i class="fas fa-eye"></i> Lihat</button>
                        </a>
                        <form action="hapus_pesanan.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">
                            <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                            <button type="submit" class="hapus"><i class="fas fa-trash"></i> Hapus</button>
                        </form>
                    </td>

                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Fungsi untuk mengupdate status pengiriman
document.querySelectorAll('select[data-id]').forEach(select => {
    select.addEventListener('change', function() {
        const idPesanan = this.getAttribute('data-id');
        const newStatus = this.value;
        const oldStatus = this.getAttribute('data-old-value');
        
        fetch('update_status_pengiriman.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_pesanan=${idPesanan}&status_pengiriman=${encodeURIComponent(newStatus)}`
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Network response was not ok');
        })
        .then(result => {
            if (result === 'success') {
                // Update tampilan
                this.className = `status-select ${newStatus.toLowerCase().replace(' ', '-')}`;
                this.setAttribute('data-old-value', newStatus);
            } else {
                alert('Gagal mengupdate status pengiriman');
                this.value = oldStatus;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupdate status pengiriman');
            this.value = oldStatus;
        });
    });
});

// Fungsi untuk mengupdate status pembayaran
document.querySelectorAll('.payment-status-form select').forEach(select => {
    select.addEventListener('change', function () {
        const form = this.closest('form');
        const idPesanan = form.querySelector('input[name="id_pesanan"]').value;
        const newStatus = this.value;
        const oldStatus = this.getAttribute('data-old-value'); 

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_pesanan=${idPesanan}&status_pembayaran=${encodeURIComponent(newStatus)}`
        })
        .then(response => {
            if (response.ok) return response.text();
            throw new Error('Network response was not ok');
        })
        .then(result => {
            if (result === 'success') {
                this.className = `status-select ${newStatus.toLowerCase().replace(' ', '-')}`;
                this.setAttribute('data-old-value', newStatus);

                
                const row = this.closest('tr');
                const shippingSelect = row.querySelector('select[data-id]');
                
                if (newStatus === 'Sudah Diverifikasi') {
                    shippingSelect.disabled = false;
                } else {
                    shippingSelect.disabled = true;
                    shippingSelect.value = 'Belum Diproses';
                    shippingSelect.className = 'status-select belum-diproses';
                    
                    fetch('update_status_pengiriman.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_pesanan=${idPesanan}&status_pengiriman=Belum Diproses`
                    });
                }
            } else {
                alert('Gagal mengupdate status pembayaran');
                this.value = oldStatus; 
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupdate status pembayaran');
            this.value = oldStatus; 
        });
    });
});
</script>

</body>
</html>