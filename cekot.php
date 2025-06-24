<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CheckOut</title>
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

    /* body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: #f3f3f3;
    } */

   

    .checkout-container {
      max-width: 800px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2, h3 {
      color: #333;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }
    .form-group input, .form-group select {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    .shipping-method {
      margin-top: 30px;
    }
    .shipping-method label {
      display: block;
      margin-bottom: 10px;
      cursor: pointer;
    }
    .shipping-method input[type="radio"] {
      margin-right: 10px;
    }
    .summary {
      background: #f9f9f9;
      padding: 15px;
      border-radius: 10px;
      margin-top: 30px;
    }
    .summary p {
      display: flex;
      justify-content: space-between;
      margin: 8px 0;
    }
    .summary hr {
      margin: 10px 0;
      border: 0;
      border-top: 1px solid #ddd;
    }
    .summary strong {
      font-size: 1.1em;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #0a1c4c;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      margin-top: 20px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: #14378a;
    }
    .cart-items-container {
      margin-top: 20px;
    }
    .cart-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 15px;
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 6px;
      margin-bottom: 15px;
    }
    .cart-item img {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 5px;
    }
    .cart-details {
      display: flex;
      justify-content: space-between;
      width: 100%;
      gap: 10px;
    }
    .cart-details .left {
      flex: 1;
    }
    .cart-details .right {
      flex: 1;
      text-align: right;
    }
    .cart-details .name {
      font-weight: bold;
      font-size: 15px;
      color: #333;
      margin-bottom: 5px;
    }
    .cart-details .price {
      font-size: 14px;
      color: #555;
      margin-bottom: 5px;
    }
    .cart-details .quantity {
      font-size: 13px;
      color: #777;
    }
    .cart-details .total {
      font-weight: bold;
      font-size: 15px;
      color: #0a1c4c;
      margin-top: 5px;
    }
    @media (max-width: 600px) {
      .checkout-container {
        padding: 15px;
      }
      .cart-details {
        flex-direction: column;
      }
      .cart-details .right {
        text-align: left;
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

<div class="checkout-container">
  <h2>Informasi Pengiriman</h2>

  <form id="checkout-form" method="POST" action="proses_cekot.php" onsubmit="return validateForm()">
    <div class="form-group">
      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" required />
    </div>
    <div class="form-group">
      <label for="alamat">Alamat Lengkap</label>
      <input type="text" id="alamat" name="alamat" required />
    </div>
    <div class="form-group">
      <label for="kota">Kota</label>
      <input type="text" id="kota" name="kota" required />
    </div>
    <div class="form-group">
      <label for="provinsi">Provinsi</label>
      <select id="provinsi" name="provinsi" required onchange="updateShippingOptions()">
        <option value="">Pilih Provinsi</option>
        <option value="DKI Jakarta">DKI Jakarta</option>
        <option value="Jawa Barat">Jawa Barat</option>
        <option value="Jawa Tengah">Jawa Tengah</option>
        <option value="DI Yogyakarta">DI Yogyakarta</option>
        <option value="Jawa Timur">Jawa Timur</option>
        <option value="Banten">Banten</option>
        <option value="Bali">Bali</option>
        <option value="Sumatera Utara">Sumatera Utara</option>
        <option value="Kalimantan Barat">Kalimantan Barat</option>
      </select>
    </div>
    <div class="form-group">
      <label for="nohp">Nomor Handphone</label>
      <input type="text" id="nohp" name="nohp" required />
    </div>

    <h3>Produk</h3>
    <div class="cart-items-container" id="cart-items"></div>

    <div class="shipping-method" id="shipping-method-container">
      <h3>Metode Pengiriman</h3>
    </div>

    <div class="summary">
      <h3>Ringkasan Pesanan</h3>
      <p>Subtotal: <span id="subtotal">Rp 0</span></p>
      <p>Ongkir: <span id="shipping">FREE</span></p>
      <hr />
      <p><strong>Total: <span id="total-display">Rp 0</span></strong></p>
    </div>

    <!-- Hidden inputs -->
    <input type="hidden" id="produkData" name="produk" />
    <input type="hidden" id="total" name="total" />
    <input type="hidden" id="shippingMethod" name="shippingMethod" />
    <input type="hidden" id="shippingCost" name="shippingCost" />

    <button type="submit">Lanjut ke Pembayaran</button>
  </form>
</div>

<script>
  const javaProvinces = ["DKI Jakarta", "Jawa Barat", "Jawa Tengah", "DI Yogyakarta", "Jawa Timur", "Banten"];

  function isJavaProvince(prov) {
    return javaProvinces.includes(prov);
  }

  function updateShippingOptions() {
    const prov = document.getElementById("provinsi").value;
    const container = document.getElementById("shipping-method-container");

    if (!prov) {
      container.innerHTML = '<p>Harap pilih provinsi terlebih dahulu</p>';
      updateTotal();
      return;
    }

    if (isJavaProvince(prov)) {
      container.innerHTML = `
        <h3>Metode Pengiriman</h3>
        <label><input type="radio" name="shipping" value="0" data-name="JNE Regular" checked> JNE Regular (Gratis Ongkir)</label>
        <label><input type="radio" name="shipping" value="10000" data-name="JNE YES"> JNE Express (+Rp 10.000)</label>
      `;
    } else {
      container.innerHTML = `
        <h3>Metode Pengiriman</h3>
        <label><input type="radio" name="shipping" value="15000" data-name="JNE Regular" checked> JNE Regular (Rp 15.000)</label>
        <label><input type="radio" name="shipping" value="25000" data-name="JNE YES"> JNE Express (Rp 25.000)</label>
      `;
    }

    const radios = document.querySelectorAll('input[name="shipping"]');
    radios.forEach(r => r.addEventListener('change', updateTotal));
    updateTotal();
  }

  function formatRupiah(num) {
    return "Rp " + num.toLocaleString('id-ID');
  }

  function getCheckoutItems() {
    const items = localStorage.getItem('checkoutItems');
    return items ? JSON.parse(items) : [];
  }

  function getFormattedItems() {
    const rawItems = getCheckoutItems();
    return rawItems.map(item => ({
      id_produk: item.id,
      price: item.price,
      quantity: item.quantity
    }));
  }

  function displayCheckoutItems() {
    const container = document.getElementById('cart-items');
    const items = getCheckoutItems();
    container.innerHTML = '';

    if (items.length === 0) {
      container.innerHTML = '<p>Keranjang kosong</p>';
      return;
    }

    items.forEach(item => {
      const div = document.createElement('div');
      div.classList.add('cart-item');
      div.innerHTML = `
        <img src="${item.image}" alt="${item.name}" />
        <div class="cart-details">
          <div class="left">
            <div class="name">${item.name}</div>
            <div class="price">Harga: ${formatRupiah(item.price)}</div>
            <div class="quantity">Jumlah: ${item.quantity}</div>
          </div>
          <div class="right">
            <div class="total">Total: ${formatRupiah(item.price * item.quantity)}</div>
          </div>
        </div>
      `;
      container.appendChild(div);
    });
  }

  function calculateSubtotal() {
    const items = getCheckoutItems();
    return items.reduce((sum, item) => sum + item.price * item.quantity, 0);
  }

  function updateTotal() {
    const subtotal = calculateSubtotal();
    const shippingRadio = document.querySelector('input[name="shipping"]:checked');
    const shippingCost = shippingRadio ? parseInt(shippingRadio.value) : 0;
    const shippingName = shippingRadio ? shippingRadio.getAttribute('data-name') : 'FREE';

    document.getElementById('subtotal').textContent = formatRupiah(subtotal);
    document.getElementById('shipping').textContent = shippingCost === 0 ? 'FREE' : formatRupiah(shippingCost);
    document.getElementById('total-display').textContent = formatRupiah(subtotal + shippingCost);

    document.getElementById('produkData').value = JSON.stringify(getFormattedItems());
    document.getElementById('total').value = subtotal + shippingCost;
    document.getElementById('shippingMethod').value = shippingName;
    document.getElementById('shippingCost').value = shippingCost;
  }

  function validateForm() {
    const form = document.getElementById('checkout-form');
    if (!form.checkValidity()) {
      form.reportValidity();
      return false;
    }

    const phone = form.nohp.value.trim();
    if (!/^[0-9]{8,15}$/.test(phone)) {
      alert('Nomor handphone harus berupa angka dan 8-15 digit');
      return false;
    }

    const items = getCheckoutItems();
    if (items.length === 0) {
      alert('Keranjang kosong!');
      return false;
    }

    return true;
  }

  window.onload = () => {
    displayCheckoutItems();
    updateShippingOptions();
  };
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
