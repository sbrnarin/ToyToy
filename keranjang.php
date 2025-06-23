<?php
session_start();
include 'koneksi.php';

$userLoggedIn = isset($_SESSION['id_pembeli']);
$id_pembeli = $userLoggedIn ? $_SESSION['id_pembeli'] : null;

$dbCart = [];
if ($userLoggedIn) {
    $query = "SELECT p.id_produk as id, p.nama_produk as name, p.harga as price, 
                     p.nama_file as image, k.jumlah as quantity
              FROM keranjang k 
              JOIN produk p ON k.id_produk = p.id_produk 
              WHERE k.id_pembeli = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_pembeli);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $dbCart[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => 'gambar/' . $row['image'],
            'quantity' => $row['quantity'],
            'selected' => true
        ];
    }
}

$initialCart = !empty($dbCart) ? $dbCart : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - ToyToyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: #f8f8f8;
      color: #222;
      line-height: 1.6;
    }
    header {
      padding: 20px;
      background-color: #0a1c4c;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    header a {
      text-decoration: none;
      font-weight: bold;
      color: #fff;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    header a::before {
      content: "←";
    }
    h1 {
      padding: 20px;
      font-size: 28px;
      text-align: center;
      color: #0a1c4c;
      margin: 0;
    }
    .cart-container {
      max-width: 900px;
      margin: 20px auto;
      padding: 0 20px;
    }
    .cart-item {
      display: flex;
      align-items: center;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      padding: 15px;
      margin-bottom: 15px;
      gap: 15px;
      transition: transform 0.2s ease;
    }
    .cart-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .cart-item img {
      width: 100px;
      height: 100px;
      border-radius: 8px;
      object-fit: contain;
      background: #f0f0f0;
    }
    .cart-item-details {
      flex: 1;
      min-width: 0;
    }
    .cart-item-name {
      font-weight: 600;
      margin-bottom: 5px;
      font-size: 16px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .item-price {
      font-size: 14px;
      color: #333;
      margin-bottom: 8px;
    }
    .quantity-control {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 10px 0;
    }
    .quantity-control button {
      background: #f0f0f0;
      border: none;
      width: 28px;
      height: 28px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }
    .quantity-control button:hover {
      background: #e0e0e0;
    }
    .delete-btn {
      cursor: pointer;
      color: #ff4444;
      font-size: 18px;
      margin-left: 10px;
      transition: transform 0.2s;
    }
    .delete-btn:hover {
      transform: scale(1.1);
    }
    .checkout-btn {
      background-color: #0a1c4c;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 16px;
      width: 100%;
      cursor: pointer;
      margin-top: 20px;
      border: none;
      transition: background 0.2s;
    }
    .checkout-btn:hover {
      background-color: #1a2c5c;
    }
    .checkout-btn:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
    }
    .empty-cart {
      text-align: center;
      margin: 50px 0;
      font-size: 18px;
      color: #888;
      padding: 40px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .cart-summary {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
      font-size: 16px;
    }
    .summary-label {
      color: #666;
    }
    .summary-value {
      font-weight: 600;
      color: #0a1c4c;
    }
    .item-checkbox {
      margin-right: 12px;
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: #0a1c4c;
    }
    </style>
</head>
<body>
    <header>
        <a href="index.php">Kembali ke Toko</a>
    </header>

    <h1>Keranjang Belanja</h1>

    <div class="cart-container" id="cart-container"></div>

    <div class="cart-summary">
        <div class="summary-row">
            <span class="summary-label">Total Produk:</span>
            <span class="summary-value" id="total-items">0 Item</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Harga:</span>
            <span class="summary-value" id="total-price">Rp 0</span>
        </div>
        <button class="checkout-btn" id="checkout-btn">Proses Checkout</button>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const initialCart = <?php echo json_encode($initialCart); ?>;
        const localCart = JSON.parse(localStorage.getItem("cart")) || [];
        const mergedCart = initialCart.length > 0 ? initialCart : localCart;

        const cart = mergedCart.map(item => ({
            id: item.id || 'unknown',
            name: item.name || item.title || 'Nama Produk',
            price: parseFloat(item.price) || 0,
            image: item.image || 'gambar/default.png',
            quantity: Math.max(1, parseInt(item.quantity) || 1),
            selected: item.selected !== false
        }));

        const cartContainer = document.getElementById("cart-container");
        const checkoutBtn = document.getElementById("checkout-btn");

        renderCart(cart);
        setupEventListeners();

        function renderCart(cart) {
            if (cart.length === 0) {
                cartContainer.innerHTML = `
                    <div class="empty-cart">
                        <p>Keranjang belanja Anda kosong</p>
                        <p><a href="index.php">Kembali berbelanja</a></p>
                    </div>
                `;
                document.querySelector('.cart-summary').style.display = 'none';
                return;
            }

            document.querySelector('.cart-summary').style.display = 'block';
            cartContainer.innerHTML = cart.map((item, index) => `
                <div class="cart-item" data-id="${item.id}">
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" class="item-checkbox" data-index="${index}" ${item.selected ? 'checked' : ''}>
                        <img src="${item.image}" alt="${item.name}" onerror="this.src='gambar/default.png'">
                    </div>
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="item-price">Harga: ${formatCurrency(item.price)}</div>
                        <div class="quantity-control">
                            <button class="quantity-btn decrease" data-index="${index}">−</button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="quantity-btn increase" data-index="${index}">+</button>
                            <button class="delete-btn" data-index="${index}">🗑️</button>
                        </div>
                    </div>
                </div>
            `).join('');
            updateCartSummary(cart);
        }

        function updateCartSummary(cart) {
            const selectedItems = cart.filter(item => item.selected);
            const totalItems = selectedItems.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = selectedItems.reduce((sum, item) => sum + item.price * item.quantity, 0);

            document.getElementById("total-items").textContent = `${totalItems} Item`;
            document.getElementById("total-price").textContent = formatCurrency(totalPrice);
            checkoutBtn.disabled = selectedItems.length === 0;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        function setupEventListeners() {
            cartContainer.addEventListener('click', function(e) {
                const index = e.target.dataset.index;
                if (index === undefined) return;

                if (e.target.classList.contains('decrease')) {
                    cart[index].quantity = Math.max(1, cart[index].quantity - 1);
                } else if (e.target.classList.contains('increase')) {
                    cart[index].quantity += 1;
                } else if (e.target.classList.contains('delete-btn')) {
                    if (confirm(`Hapus ${cart[index].name} dari keranjang?`)) {
                        cart.splice(index, 1);
                    }
                }

                localStorage.setItem("cart", JSON.stringify(cart));
                renderCart(cart);
            });

            cartContainer.addEventListener("change", function(e) {
                if (e.target.classList.contains("item-checkbox")) {
                    const index = e.target.dataset.index;
                    cart[index].selected = e.target.checked;
                    localStorage.setItem("cart", JSON.stringify(cart));
                    updateCartSummary(cart);
                }
            });

            checkoutBtn.addEventListener("click", function() {
                const selectedItems = cart.filter(item => item.selected);
                if (selectedItems.length === 0) {
                    alert("Pilih produk terlebih dahulu.");
                    return;
                }
                localStorage.setItem("checkoutItems", JSON.stringify(selectedItems));
                window.location.href = "cekot.php";
            });
        }
    });
    </script>
</body>
</html>
