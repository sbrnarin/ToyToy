<?php
$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$pesanan_id = isset($_GET['pesanan_id']) ? (int)$_GET['pesanan_id'] : 0;
if ($pesanan_id <= 0) {
    header("Location: pesanan.php");
    exit();
}

// Ambil data pesanan
$stmt = $conn->prepare("
    SELECT p.id_pesanan, p.total_harga,
           p.metode_pembayaran, p.tanggal_pesan, p.ongkir, 
           p.metode_pengiriman, p.kode_pembayaran,
           pb.nama_pembeli, pb.alamat, pb.kota, pb.provinsi, pb.no_telp 
    FROM pesanan p
    JOIN pembeli pb ON p.id_pembeli = pb.id_pembeli 
    WHERE p.id_pesanan = ?
");
$stmt->bind_param("i", $pesanan_id);
$stmt->execute();
$result = $stmt->get_result();
$dataPesanan = $result->fetch_assoc();

if (!$dataPesanan) {
    header("Location: pesanan.php");
    exit();
}


$waktu_expired = date('Y-m-d H:i:s', strtotime($dataPesanan['tanggal_pesan'] . ' 23:59:59'));
$stmt2 = $conn->prepare("
    SELECT p.nama_produk, dp.harga_saat_beli, dp.jumlah 
    FROM detail_pesanan dp
    JOIN produk p ON dp.id_produk = p.id_produk
    WHERE dp.id_pesanan = ?
");
$stmt2->bind_param("i", $pesanan_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$produk = [];
while ($row = $result2->fetch_assoc()) {
    $produk[] = $row;
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Hitung waktu tersisa untuk pembayaran
$waktu_sekarang = new DateTime();
$waktu_expired_obj = new DateTime($waktu_expired);
$interval = $waktu_sekarang->diff($waktu_expired_obj);
$jam_tersisa = $interval->h;
$menit_tersisa = $interval->i;
$detik_tersisa = $interval->s;
$total_detik_tersisa = ($jam_tersisa * 3600) + ($menit_tersisa * 60) + $detik_tersisa;

// Generate kode pembayaran
$kode_gopay = "GOPAY-" . date('Ymd') . "-" . strtoupper(substr(md5('gopay' . $pesanan_id), 0, 6));
$kode_dana = "DANA-" . date('Ymd') . "-" . strtoupper(substr(md5('dana' . $pesanan_id), 0, 6));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - ToyToy</title>
     <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
.dropdown-user {
  position: relative;
  display: inline-block;
  cursor: pointer;
}

.dropdown-user .material-symbols-outlined {
  font-size: 24px;
  color: #333;
}

.dropdown-user-menu {
  display: none;
  position: absolute;
  right: 0;
  top: 130%;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  z-index: 1000;
  min-width: 160px;
}

.dropdown-user-menu a {
  display: block;
  padding: 12px 16px;
  text-decoration: none;
  color: #333;
  font-size: 14px;
}

.dropdown-user-menu a:hover {
  background-color: #f4f6fc;
  color: #081F5C;
}

        :root {
        --primary: #0a1c4c;
        --primary-light: #14378a;
        --secondary: #f8f9fa;
        --danger: #e74c3c;
        --success: #2ecc71;
        --dark: #2c3e50;
        --light: #ffffff;
        --gray: #6c757d;
        --border: #e0e0e0;
    }

        /* body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        } */

        .container {
            max-width: 700px;
            margin: auto;
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .payment-header {
            background: var(--primary);
            color: var(--light);
            padding: 16px 24px;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
        }

        .section {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .customer-info .info-item {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .customer-info .info-item strong {
            font-weight: 500;
            color: var(--dark);
        }

        .order-summary {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .order-summary th, 
        .order-summary td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .text-right {
            text-align: right;
        }

        .order-total {
            font-weight: 600;
            color: var(--primary);
        }

.payment-methods {
    display: flex;
    gap: 16px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.payment-method {
    border: 2px solid var(--border);
    border-radius: 8px;
    padding: 12px;
    width: 140px;
    text-align: center;
    cursor: pointer;
    transition: 0.3s ease;
}

.payment-method.selected {
    border-color: var(--primary);
    background-color: #f0f4ff;
}

.payment-method img {
    width: 60px;
    height: 40px;
    object-fit: contain;
    margin-bottom: 5px;
}

.payment-method input {
    display: none;
}


        .payment-code {
            font-size: 15px;
            font-weight: bold;
            color: var(--primary);
            padding: 10px;
            background: #eef3fb;
            text-align: center;
            border: 1px dashed var(--primary);
            border-radius: 6px;
            margin: 10px 0;
        }

        ol, ul {
            list-style: none;
            padding-left: 0;
        }

        .payment-instruction {
            display: none;
            background: #f0f2f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            animation: fadeIn 0.3s ease;
            font-size: 14px;
        }

        .payment-instruction.active {
            display: block;
        }

        .timer {
            text-align: center;
            margin-top: 10px;
            font-weight: 500;
            color: var(--danger);
            font-size: 14px;
        }

        .btn-confirm {
            margin: 20px 0;
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-confirm:hover {
            background: var(--primary-light);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="login">
          <p style="color: #000080; text-align: center; margin: 0;">.</p>
        </div>
        
        <header>
            <div class="header-container">
                <div class="logo_toko">
                    <img src="gambar/Kids Toys Logo (1).png" alt="Toy logo" class="nav_logo"/>
                </div>
          
                <form class="search-form" onsubmit="return searchProducts(event)">
                  <div class="search">
                    <span class="search-icon material-symbols-outlined">search</span>
                    <input class="search-input" type="search" placeholder="Search" />
                  </div>
                </form>

                    
                <div class="header-icons">
                  <div class="wishlist-link" id="wishlist-toggle">
                    <span class="material-symbols-outlined">favorite</span>
                    
                    <!-- Dropdown Wishlist -->
                    <div class="wishlist-dropdown">
                      <div class="wishlist-header">
                        <h3>Wishlist Saya</h3>
                        <button class="close-wishlist" aria-label="Close wishlist">
                          <span class="material-symbols-outlined">close</span>
                        </button>
                      </div>
                      <div class="wishlist-items">
                        <div class="empty-message">
                          <span class="material-symbols-outlined">favorite</span>
                          <p>Wishlist Anda kosong</p>
                        </div>
                      </div>
                      <div class="wishlist-footer">
                        <a href="wishlist.html" class="btn-view-full-wishlist" onclick="window.location.href='wishlist.html'">Lihat wishlist Lengkap</a>
                      </div>
                    </div>
                  </div>

                  <div class="dropdown-user">
                    <span class="material-symbols-outlined" id="user-toggle">person</span>
                    <div class="dropdown-user-menu" id="user-menu">
                        <a href="user.php">Profil</a>
                        <a href="pesanan.php">Pesanan Saya</a>
                        <a href="logout.php">Logout</a>
                    </div>
                  </div>

                    <div class="cart-icon-wrapper">
                      <a href="#" id="cart-icon" class="icon-link">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span class="cart-count">0</span> 
                      </a>
                      <div id="cart-popup" class="cart-popup">
                        <h4>Keranjang</h4>
                        <ul id="cart-list-navbar">
                          <li id="empty-cart" class="empty-cart">
                            <h4>Keranjang Anda Kosong</h4>
                            <button onclick="window.location.href='#'" class="btn-continue">Lanjutkan belanja</button>
                          </li>
                        </ul>
                        <p id="cart-total" class="cart-total">Total: Rp 0</p>
                        <a href="keranjang.php" class="btn-view-full-cart">Lihat Keranjang Lengkap</a>
                      </div>
                    </div>

                </div>
            </div>
 
            <nav class="nav_link">
              <a href="index.php">Home</a>
                <div class="nav-item dropdown">
                    <a href="video-games.php" class="dropdown-toggle">Toys & Games 
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="puzzles.php">Building Blocks</a>
                        <a href="figurines.php">Action Figures</a>
                        <a href="playdoh.php">Toy Clay</a>
                        <a href="dolls.php">Dolls & Accessories</a>
                    </div>
                </div>

                <div class="nav-item dropdown">
                    <a href="video-games.php" class="dropdown-toggle">Video Games & Consoles
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="video-games.php">Console Accessories</a>
                        <a href="video-games.php">Game Consoles</a>
                        <a href="video-games.php">Video Game</a>
                    </div>
                </div>

                <div class="nav-item dropdown">
                    <a href="#brands" class="dropdown-toggle">Brands
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="playdoh.php">Play-Doh</a>
                        <a href="lego.php">Lego</a>
                        <a href="hotwheels.php">Hot Wheels</a>
                        <a href="bebyalive.php">Beby Alive</a>
                    </div>
                </div>

            </nav>
        </header>

    <div class="container">
        <div class="payment-card">
            <div class="payment-header">
                <h2>Detail Pembayaran</h2>
            </div>
                
                <div class="section">
                    <div class="section-title">Informasi Pelanggan</div>
                    <div class="customer-info">
                        <div class="info-item"><strong>Nama:</strong> <?= htmlspecialchars($dataPesanan['nama_pembeli']) ?></div>
                        <div class="info-item"><strong>Alamat:</strong> <?= htmlspecialchars($dataPesanan['alamat']) ?>, <?= htmlspecialchars($dataPesanan['kota']) ?>, <?= htmlspecialchars($dataPesanan['provinsi']) ?></div>
                        <div class="info-item"><strong>No. HP:</strong> <?= htmlspecialchars($dataPesanan['no_telp']) ?></div>
                        <div class="info-item"><strong>Tanggal Pesanan:</strong> <?= date('d/m/Y', strtotime($dataPesanan['tanggal_pesan'])) ?></div>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">Ringkasan Pesanan</div>
                    <table class="order-summary">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Jumlah</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produk as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                <td class="text-right"><?= formatRupiah($item['harga_saat_beli']) ?></td>
                                <td class="text-right"><?= (int)$item['jumlah'] ?></td>
                                <td class="text-right"><?= formatRupiah($item['harga_saat_beli'] * $item['jumlah']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Ongkos Kirim (<?= htmlspecialchars($dataPesanan['metode_pengiriman']) ?>)</td>
                                <td class="text-right"><?= formatRupiah($dataPesanan['ongkir']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="order-total">Total Pembayaran</td>
                                <td class="text-right order-total"><?= formatRupiah($dataPesanan['total_harga']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
<div class="section">
    <div class="section-title">Metode Pembayaran</div>
    <div class="payment-methods">
        <div class="payment-method" onclick="selectPayment('gopay')">
            <input type="radio" name="payment" value="gopay" id="gopay" hidden>
            <img src="gambar/gopay.png" onerror="this.src='gambar/placeholder.png'" alt="GoPay">
            <div>GoPay</div>
        </div>
        <div class="payment-method" onclick="selectPayment('dana')">
            <input type="radio" name="payment" value="dana" id="dana" hidden>
            <img src="gambar/dana.png" onerror="this.src='gambar/placeholder.png'" alt="DANA">
            <div>DANA</div>
        </div>
    </div>
</div>

                    </div>
                    
                    <div id="gopay-instruction" class="payment-instruction">
                        <h3>Instruksi Pembayaran GoPay</h3>
                        <div class="payment-code"><?= htmlspecialchars($kode_gopay) ?></div>
                        <ol>
                            <li>Buka aplikasi Gojek dan pilih <strong>Bayar</strong></li>
                            <li>Masukkan kode pembayaran di atas atau scan QR code</li>
                            <li>Pastikan nominal pembayaran sesuai</li>
                            <li>Selesaikan pembayaran sebelum waktu habis</li>
                        </ol>
                        <div class="timer">Batas waktu pembayaran: <span id="gopay-timer"><?= date('H:i', strtotime($waktu_expired)) ?></span></div>
                    </div>
                    
                    <div id="dana-instruction" class="payment-instruction">
                        <h3>Instruksi Pembayaran DANA</h3>
                        <div class="payment-code"><?= htmlspecialchars($kode_dana) ?></div>
                        <ol>
                            <li>Buka aplikasi DANA dan pilih <strong>Bayar</strong></li>
                            <li>Masukkan kode pembayaran di atas</li>
                            <li>Pastikan nominal pembayaran sesuai</li>
                            <li>Selesaikan pembayaran sebelum waktu habis</li>
                        </ol>
                        <div class="timer">Batas waktu pembayaran: <span id="dana-timer"><?= date('H:i', strtotime($waktu_expired)) ?></span></div>
                    </div>
                    
                    <button class="btn-confirm" onclick="confirmPayment()">Konfirmasi Pembayaran</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timerInterval;
        const totalDetikTersisa = <?= $total_detik_tersisa ?>;

        function startTimer(duration, displayId) {
            let timer = duration;
            clearInterval(timerInterval);
            

            updateTimerDisplay(timer, displayId);
            
            timerInterval = setInterval(() => {
                timer--;
                updateTimerDisplay(timer, displayId);
                
                if (timer <= 0) {
                    clearInterval(timerInterval);
                    alert("Waktu pembayaran telah habis. Silakan ulangi proses checkout.");
                    window.location.href = "pesanan.php";
                }
            }, 1000);
        }
        
        function updateTimerDisplay(timer, displayId) {
            let hours = Math.floor(timer / 3600);
            let minutes = Math.floor((timer % 3600) / 60);
            let seconds = timer % 60;
            
            hours = String(hours).padStart(2, '0');
            minutes = String(minutes).padStart(2, '0');
            seconds = String(seconds).padStart(2, '0');
            
            document.getElementById(displayId).textContent = `${hours}:${minutes}:${seconds}`;
        }

        function selectPayment(method) {
            document.querySelectorAll('.payment-instruction').forEach(el => {
                el.classList.remove('active');
            });
            

            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            

            const instruction = document.getElementById(`${method}-instruction`);
            if (instruction) {
                instruction.classList.add('active');
                startTimer(totalDetikTersisa, `${method}-timer`);
            }
            
            event.currentTarget.classList.add('selected');
        }

        function confirmPayment() {
            const selectedMethod = document.querySelector('.payment-method.selected');
            
            if (!selectedMethod) {
                alert('Silakan pilih metode pembayaran terlebih dahulu.');
                return;
            }
            
            const method = selectedMethod.querySelector('input').value;
            window.location.href = `upload_bukti.php?pesanan_id=<?= $pesanan_id ?>&method=${method}`;
        }
        
        // otomatis pilih
        document.addEventListener('DOMContentLoaded', function() {
            const firstMethod = document.querySelector('.payment-method');
            if (firstMethod) {
                firstMethod.click();
            }
        });
    </script>

    <script>
const userToggle = document.getElementById("user-toggle");
  const userMenu = document.getElementById("user-menu");

  userToggle.addEventListener("click", function (e) {
    e.stopPropagation();
    userMenu.style.display = userMenu.style.display === "block" ? "none" : "block";
  });

  // Tutup dropdown kalau klik di luar
  document.addEventListener("click", function (e) {
    if (!userMenu.contains(e.target)) {
      userMenu.style.display = "none";
    }
  });
  </script>


              <!-- search -->
              <script>
                function searchProducts(event) {
                  event.preventDefault(); 
              
                  //case-insensitive
                  const query = document.querySelector('.search-input').value.toLowerCase();
                  
                  // Ambil semua produk
                  const products = document.querySelectorAll('.product-card');
              
                  // produk query
                  products.forEach(product => {
                    const name = product.getAttribute('data-name').toLowerCase();
              
                    // Jika input kosong
                    if (query === "") {
                      product.style.display = "block";
                    } else {
                      product.style.display = name.includes(query) ? "block" : "none";
                    }
                  });
                  
                  return false;
                }
            
// keranjang
const cart = [];
const cartListNavbar = document.getElementById('cart-list-navbar');
const cartCount = document.querySelector('.cart-count');
const cartIcon = document.getElementById('cart-icon');
const cartPopup = document.getElementById('cart-popup');
const cartTotal = document.getElementById('cart-total');
const emptyCartMessage = document.getElementById('empty-cart');

function updateCartUI() {
    if (!cartListNavbar || !cartTotal) return;

    cartListNavbar.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
        if (emptyCartMessage) {
            cartListNavbar.appendChild(emptyCartMessage);
            emptyCartMessage.style.display = 'block';
        }
        cartTotal.textContent = 'Total: Rp 0';
        return;
    }

    cart.forEach((item, index) => {
        const li = document.createElement('li');
        li.innerHTML = `
            <img src="${item.image}" alt="${item.title}" style="width: 40px; height: auto; vertical-align: middle; margin-right: 8px;">
            ${item.title} - ${item.price}
            <button class="remove-item" data-index="${index}">&times;</button>
        `;
        cartListNavbar.appendChild(li);

        const priceNum = parseInt(item.price.replace(/[^\d]/g, ''), 10);
        total += priceNum * item.quantity;
    });

    if (emptyCartMessage) emptyCartMessage.style.display = 'none';
    cartTotal.textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
}

function saveCartToLocalStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart.push(...JSON.parse(savedCart));
        cartCount.textContent = cart.length;
        updateCartUI();
    }
}

document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productCard = button.closest('.product-card');
        const title = productCard.querySelector('.product-title')?.textContent?.trim() || 'Unknown Product';
        const price = productCard.querySelector('.product-price')?.textContent?.trim() || 'N/A';
        const image = productCard.querySelector('img')?.src || 'default-image.jpg';
        const productId = productCard.dataset.productId || generateUniqueId(); // Add data-product-id to your HTML

        // Find existing item by ID if available, otherwise by title+price
        const existingItem = productId 
            ? cart.find(item => item.id === productId)
            : cart.find(item => item.title === title && item.price === price);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({ 
                id: productId,
                title, 
                price, 
                image, 
                quantity: 1 
            });
        }

        cartCount.textContent = calculateTotalItems();
        updateCartUI();
        saveCartToLocalStorage();

        showNotification(`${title} ditambahkan ke keranjang`);
    });
});


function calculateTotalItems() {
    return cart.reduce((total, item) => total + item.quantity, 0);
}

function generateUniqueId() {
    return Math.random().toString(36).substring(2, 15);
}



cartListNavbar?.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        const index = parseInt(e.target.dataset.index, 10);
        const removedItem = cart[index];
        cart.splice(index, 1);
        cartCount.textContent = cart.length;
        updateCartUI();
        saveCartToLocalStorage();

        showNotification(`${removedItem.title} dihapus dari keranjang`);
    }
});


cartIcon?.addEventListener('click', function(e) {
    e.preventDefault();
    if (cartPopup) {
        cartPopup.style.display = cartPopup.style.display === 'block' ? 'none' : 'block';
    }
});

// keranjang
document.addEventListener('click', function(e) {
    if (!cartIcon?.contains(e.target) && !cartPopup?.contains(e.target)) {
        if (cartPopup) cartPopup.style.display = 'none';
    }
});

// WISHLIST
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
const wishlistToggle = document.getElementById('wishlist-toggle');
const wishlistDropdown = document.querySelector('.wishlist-dropdown');
const wishlistItemsContainer = document.querySelector('.wishlist-items');

function initializeWishlistButtons() {
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        const productCard = btn.closest('.product-card');
        const productId = productCard.dataset.productId || generateId(productCard);

        if (wishlist.some(item => item.id === productId)) {
            btn.classList.add('active');
        }

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleWishlistItem(productCard, btn);
        });
    });
}

function toggleWishlistItem(productCard, btn) {
    const productId = productCard.dataset.productId || generateId(productCard);
    const productName = productCard.querySelector('.product-title').textContent;
    const productPrice = productCard.querySelector('.product-price').textContent;
    const productImage = productCard.querySelector('img').src;

    const existingIndex = wishlist.findIndex(item => item.id === productId);

    if (existingIndex === -1) {
        wishlist.push({ id: productId, name: productName, price: productPrice, image: productImage });
        btn.classList.add('active');
        showNotification(`${productName} ditambahkan ke wishlist`);
    } else {
        wishlist.splice(existingIndex, 1);
        btn.classList.remove('active');
        showNotification(`${productName} dihapus dari wishlist`);
    }

    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistDisplay();
}

function generateId(element) {
    if (!element.dataset.productId) {
        element.dataset.productId = 'prod-' + Math.random().toString(36).substr(2, 9);
    }
    return element.dataset.productId;
}

function updateWishlistDisplay() {
    if (!wishlistItemsContainer) return;

    if (wishlist.length === 0) {
        wishlistItemsContainer.innerHTML = `
            <div class="empty-message">
                <span class="material-symbols-outlined">favorite</span>
                <p>Wishlist Anda kosong</p>
            </div>
        `;
        return;
    }

    let itemsHTML = '';
    wishlist.forEach(item => {
        itemsHTML += `
            <div class="wishlist-item" data-id="${item.id}">
                <img src="${item.image}" alt="${item.name}">
                <div class="wishlist-item-details">
                    <div class="wishlist-item-name">${item.name}</div>
                    <div class="wishlist-item-price">${item.price}</div>
                </div>
                <button class="remove-wishlist">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        `;
    });

    wishlistItemsContainer.innerHTML = itemsHTML;
}

wishlistItemsContainer?.addEventListener('click', function(e) {
    if (e.target.closest('.remove-wishlist')) {
        const itemElement = e.target.closest('.wishlist-item');
        const productId = itemElement.dataset.id;

        wishlist = wishlist.filter(item => item.id !== productId);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        updateWishlistDisplay();

        document.querySelectorAll(`.product-card[data-product-id="${productId}"] .wishlist-btn`)
            .forEach(btn => btn.classList.remove('active'));

        showNotification("Item dihapus dari wishlist");
    }
});

wishlistToggle?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    wishlistDropdown.classList.toggle('show');
});

document.addEventListener('click', function(e) {
    if (!wishlistToggle.contains(e.target) && !wishlistDropdown.contains(e.target)) {
        wishlistDropdown.classList.remove('show');
    }
});

document.querySelector('.close-wishlist')?.addEventListener('click', function() {
    wishlistDropdown.classList.remove('show');
});

// SEARCH
const searchInput = document.getElementById('search-input');
const searchButton = document.getElementById('search-button');
const productCards = document.querySelectorAll('.product-card');

function performSearch() {
    const searchTerm = searchInput.value.toLowerCase();

    productCards.forEach(card => {
        const productName = card.querySelector('.product-title').textContent.toLowerCase();
        const productDesc = card.querySelector('.product-desc')?.textContent.toLowerCase() || '';

        if (productName.includes(searchTerm) || productDesc.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

searchButton?.addEventListener('click', performSearch);
searchInput?.addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

//  NAFIGASI DETAIL PRODUK
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (!e.target.closest('button')) {
            const name = card.getAttribute('data-name') || card.querySelector('.product-title').textContent;
            const price = card.getAttribute('data-price') || card.querySelector('.product-price').textContent;
            const image = card.getAttribute('data-image') || card.querySelector('img').src;

            const product = { name, price, image };
            localStorage.setItem('selectedProduct', JSON.stringify(product));
            window.location.href = 'detail_produk.php';
        }
    });
});

// NOTIFICATION
function showNotification(message) {
    alert(message); 
}

document.addEventListener('DOMContentLoaded', function() {
    loadCartFromLocalStorage();
    initializeWishlistButtons();
    updateWishlistDisplay();
});
</script>

<script>
  lucide.createIcons();
</script>
</body>
</html>