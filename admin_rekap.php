<?php
$koneksi = new mysqli("localhost", "root", "", "sabrinalina");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulan']) && isset($_POST['tahun'])) {
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Jakarta');
    $bulan = filter_input(INPUT_POST, 'bulan', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 12]
    ]);
    $tahun = filter_input(INPUT_POST, 'tahun', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 2020, 'max_range' => date('Y')]
    ]);

    if (!$bulan || !$tahun) {
        http_response_code(400);
        echo json_encode(['error' => 'Input bulan/tahun tidak valid']);
        exit;
    }

    try {
        $sql = "SELECT 
            p.nama_produk,
            m.nama_merk,
            k.nama_kategori,
            SUM(dp.jumlah) AS total_terjual,
            SUM(dp.jumlah * p.harga) AS total_pendapatan
        FROM detail_penjualan dp
        JOIN penjualan pj ON dp.id_jual = pj.id_jual
        JOIN produk p ON dp.id_produk = p.id_produk
        LEFT JOIN merk m ON p.id_merk = m.id_merk
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE MONTH(pj.tanggal) = ? AND YEAR(pj.tanggal) = ?
        GROUP BY dp.id_produk
        ORDER BY total_terjual DESC";

        $stmt = $koneksi->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $koneksi->error);
        }
        
        $stmt->bind_param("ii", $bulan, $tahun);
        if (!$stmt->execute()) {
            throw new Exception("Error executing statement: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $row['total_pendapatan_formatted'] = number_format($row['total_pendapatan'], 0, ',', '.');
            $data[] = $row;
        }

        echo json_encode($data);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// bulan dan tahun saat ini
$currentMonth = date('n');
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekap Penjualan Prouk</title>
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

    
    .rekap-container {
      width: 90%;
      max-width: 1000px;
      margin: 50px auto 50px 250px;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .rekap-container h2 {
      text-align: center;
      color: #081F5C;
      margin-bottom: 25px;
    }

    .filter-form {
      text-align: center;
      margin-bottom: 20px;
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
    }

    .filter-form label {
      margin-right: 5px;
      font-weight: 500;
    }

    select, button {
      padding: 8px 12px;
      margin: 0 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    button {
      background-color: #081F5C;
      color: white;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #0a2a80;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 14px;
    }

    th {
      background-color: #081F5C;
      color: white;
      padding: 12px;
      text-align: left;
    }

    td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }

    .right-align {
      text-align: right;
    }

    .center-align {
      text-align: center;
    }

    tr:nth-child(even) {
      background-color: #f8f8f8;
    }

    .total-row {
      background-color: #e0e0e0 !important;
      font-weight: bold;
    }

    .loading {
      text-align: center;
      color: #666;
      font-style: italic;
      padding: 20px;
    }

    .error-message {
      color: #d32f2f;
      text-align: center;
      padding: 20px;
      font-weight: bold;
    }

    @media (max-width: 768px) {
      .rekap-container {
        margin-left: 20px;
        width: 95%;
        padding: 15px;
      }
      
      .sidebar {
        width: 60px;
        overflow: hidden;
      }
      
      .sidebar .logo img {
        width: 40px;
      }
      
      .sidebar nav ul li a span {
        display: none;
      }
      
      table {
        font-size: 12px;
      }
      
      .filter-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }
      
      select, button {
        width: 100%;
        margin: 0;
      }
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

  <div class="rekap-container">
    <h2>REKAP PENJUALAN PRODUK</h2>
    
    <form id="filterForm" class="filter-form">
      <label for="bulan">Bulan:</label>
      <select name="bulan" id="bulan">
        <?php 
        for ($i = 1; $i <= 12; $i++): 
          $selected = ($i == $currentMonth) ? 'selected' : '';
        ?>
          <option value="<?= $i ?>" <?= $selected ?>>
            <?= sprintf('%02d', $i) ?>
          </option>
        <?php endfor; ?>
      </select>

      <label for="tahun">Tahun:</label>
      <select name="tahun" id="tahun">
        <?php 
        for ($y = 2022; $y <= $currentYear; $y++): 
          $selected = ($y == $currentYear) ? 'selected' : '';
        ?>
          <option value="<?= $y ?>" <?= $selected ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>

      <button type="submit">Tampilkan</button>
    </form>

    <table id="rekapTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Produk</th>
          <th>Merk</th>
          <th>Kategori</th>
          <th>Jumlah Terjual</th>
          <th>Total Pendapatan</th>
        </tr>
      </thead>
      <tbody id="rekapBody">
        <tr><td colspan="6" class="loading">Silakan pilih bulan dan tahun terlebih dahulu.</td></tr>
      </tbody>
    </table>
  </div>

  <script>
    document.getElementById('filterForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const bulan = document.getElementById('bulan').value;
      const tahun = document.getElementById('tahun').value;
      
      const tbody = document.getElementById('rekapBody');
      tbody.innerHTML = '<tr><td colspan="6" class="loading">Memuat data...</td></tr>';
      
      fetch('admin_rekap.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `bulan=${bulan}&tahun=${tahun}`
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        tbody.innerHTML = '';

        if (data.error) {
          tbody.innerHTML = `<tr><td colspan="6" class="error-message">${data.error}</td></tr>`;
          return;
        }

        if (data.length === 0) {
          tbody.innerHTML = `<tr><td colspan="6">Tidak ada data penjualan untuk periode yang dipilih.</td></tr>`;
          return;
        }

        let totalPendapatan = 0;
        let totalTerjual = 0;

        data.forEach((item, index) => {
          totalPendapatan += parseInt(item.total_pendapatan);
          totalTerjual += parseInt(item.total_terjual);
          
          tbody.innerHTML += `
            <tr>
              <td class="center-align">${index + 1}</td>
              <td>${item.nama_produk}</td>
              <td>${item.nama_merk || '-'}</td>
              <td>${item.nama_kategori || '-'}</td>
              <td class="right-align">${item.total_terjual}</td>
              <td class="right-align">Rp ${item.total_pendapatan_formatted || parseInt(item.total_pendapatan).toLocaleString('id-ID')}</td>
            </tr>
          `;
        });
    
        tbody.innerHTML += `
          <tr class="total-row">
            <td colspan="4"><strong>Total</strong></td>
            <td class="right-align"><strong>${totalTerjual}</strong></td>
            <td class="right-align"><strong>Rp ${totalPendapatan.toLocaleString('id-ID')}</strong></td>
          </tr>
        `;
      })
      .catch(err => {
        console.error('Gagal ambil data:', err);
        tbody.innerHTML = `<tr><td colspan="6" class="error-message">Terjadi kesalahan saat memuat data: ${err.message}</td></tr>`;
      });
    });
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('filterForm').dispatchEvent(new Event('submit'));
    });
  </script>
</body>
</html>