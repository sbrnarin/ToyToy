<?php
session_start();
include 'koneksi.php';

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$userLoggedIn = isset($_SESSION['id_pembeli']);
$id_pembeli = $userLoggedIn ? $_SESSION['id_pembeli'] : null;

$dbCart = [];
if ($userLoggedIn && $id_pembeli) {
    $query = "SELECT p.id_produk as id, p.nama_produk as name, p.harga as price, 
                     p.nama_file as image, k.jumlah as quantity
              FROM keranjang k 
              JOIN produk p ON k.id_produk = p.id_produk 
              WHERE k.id_pembeli = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_pembeli);
        if (mysqli_stmt_execute($stmt)) {
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
    }
    header a::before {
      content: "← ";
    }
    h1 {
      padding: 20px;
      text-align: center;
      color: #0a1c4c;
    }
    .cart-container {
      max-width: 900px;
      margin: 20px auto;
      padding: 0 20px;
    }
    .cart-item {
      display: flex;
      align-items: center;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 15px;
      margin-bottom: 15px;
      gap: 20px;
    }
    .cart-left {
      display: flex;
      align-items: center;
      gap: 15px;
      position: relative;
    }
    .item-checkbox {
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: #0a1c4c;
      position: relative;
      z-index: 2;
      margin: 0;
    }
    .cart-left img {
      width: 100px;
      height: 100px;
      border-radius: 8px;
      object-fit: contain;
      background: #f0f0f0;
      z-index: 1;
    }
    .cart-item-details {
      flex: 1;
    }
    .cart-item-name {
      font-weight: 600;
      font-size: 16px;
      margin-bottom: 5px;
    }
    .item-price {
      font-size: 14px;
      margin-bottom: 5px;
      color: #555;
    }
    .quantity-control {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 8px;
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
    }
    .quantity-control button:hover {
      background: #e0e0e0;
    }
    .quantity-control span {
      min-width: 20px;
      text-align: center;
    }
    .delete-btn {
      color: #ff4444;
      font-size: 18px;
      margin-left: 10px;
      cursor: pointer;
      background: none;
      border: none;
    }
    .cart-summary {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
    }
    .summary-label {
      color: #666;
    }
    .summary-value {
      font-weight: 600;
      color: #0a1c4c;
    }
    .checkout-btn {
      background: #0a1c4c;
      color: #fff;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 10px;
    }
    .checkout-btn:hover {
      background: #0c2461;
    }
    .checkout-btn:disabled {
      background: #cccccc;
      cursor: not-allowed;
    }
    .empty-cart {
      text-align: center;
      padding: 40px;
      background: white;
      border-radius: 8px;
      margin-top: 40px;
    }
    @media (max-width: 600px) {
      .cart-item {
        flex-direction: column;
        align-items: flex-start;
      }
      .cart-left {
        width: 100%;
        margin-bottom: 10px;
      }
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
    document.addEventListener("DOMContentLoaded", () => {
      const initialCart = <?php echo json_encode($initialCart); ?>;
      const localCart = JSON.parse(localStorage.getItem("cart")) || [];
      const mergedCart = initialCart.length > 0 ? initialCart : localCart;

      const cart = mergedCart.map(item => ({
        id: item.id,
        name: item.name,
        price: parseFloat(item.price),
        image: item.image,
        quantity: parseInt(item.quantity) || 1,
        selected: item.selected !== false
      }));

      const cartContainer = document.getElementById("cart-container");
      const checkoutBtn = document.getElementById("checkout-btn");

      renderCart();
      setupEvents();

      function renderCart() {
        if (cart.length === 0) {
          cartContainer.innerHTML = `
            <div class="empty-cart">
              <p>Keranjang Anda kosong.</p>
              <p><a href="index.php">Kembali belanja</a></p>
            </div>`;
          document.querySelector(".cart-summary").style.display = "none";
          return;
        }

        document.querySelector(".cart-summary").style.display = "block";
        cartContainer.innerHTML = cart.map((item, index) => `
          <div class="cart-item" data-index="${index}">
            <div class="cart-left">
              <input type="checkbox" class="item-checkbox" data-index="${index}" ${item.selected ? "checked" : ""}>
              <img src="${item.image}" alt="${item.name}" onerror="this.src='gambar/default.png'">
            </div>
            <div class="cart-item-details">
              <div class="cart-item-name">${item.name}</div>
              <div class="item-subtotal"> ${formatRupiah(item.price * item.quantity)}</div>
              <div class="quantity-control">
                <button class="decrease" data-index="${index}">−</button>
                <span>${item.quantity}</span>
                <button class="increase" data-index="${index}">+</button>
                <button class="delete-btn" data-index="${index}">🗑️</button>
              </div>
            </div>
          </div>
        `).join("");
        updateSummary();
      }

      function setupEvents() {
        cartContainer.addEventListener("click", e => {
          const index = e.target.dataset.index;
          if (index === undefined) return;

          if (e.target.classList.contains("decrease")) {
            cart[index].quantity = Math.max(1, cart[index].quantity - 1);
            updateCartItem(index);
          }
          else if (e.target.classList.contains("increase")) {
            cart[index].quantity += 1;
            updateCartItem(index);
          }
          else if (e.target.classList.contains("delete-btn")) {
            if (confirm("Hapus produk ini dari keranjang?")) {
              cart.splice(index, 1);
              renderCart();
            }
          }
          
          localStorage.setItem("cart", JSON.stringify(cart));
        });

        cartContainer.addEventListener("change", e => {
          if (e.target.classList.contains("item-checkbox")) {
            const index = e.target.dataset.index;
            if (index !== undefined) {
              cart[index].selected = e.target.checked;
              localStorage.setItem("cart", JSON.stringify(cart));
              updateSummary();
            }
          }
        });

        checkoutBtn.addEventListener("click", () => {
          const selectedItems = cart.filter(item => item.selected);
          if (selectedItems.length === 0) {
            alert("Silakan pilih minimal satu produk untuk checkout.");
            return;
          }
          localStorage.setItem("checkoutItems", JSON.stringify(selectedItems));
          window.location.href = "cekot.php";
        });
      }

      function updateCartItem(index) {
        const itemElement = document.querySelector(`.cart-item[data-index="${index}"]`);
        if (itemElement) {
          itemElement.querySelector('.item-subtotal').textContent = 
            ` ${formatRupiah(cart[index].price * cart[index].quantity)}`;
          itemElement.querySelector('.quantity-control span').textContent = 
            cart[index].quantity;
        }
        updateSummary();
      }

      function updateSummary() {
        const selected = cart.filter(i => i.selected);
        const totalQty = selected.reduce((sum, i) => sum + i.quantity, 0);
        const totalPrice = selected.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        document.getElementById("total-items").textContent = `${totalQty} Item`;
        document.getElementById("total-price").textContent = formatRupiah(totalPrice);
        checkoutBtn.disabled = selected.length === 0;
      }

      function formatRupiah(angka) {
        return "Rp " + angka.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }
    });
  </script>
</body>
</html>