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
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ToyToyShop</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <script src="https://unpkg.com/lucide@latest"></script>
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

/*    
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
    } */
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

<!-- user -->
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