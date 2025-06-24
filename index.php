<?php
include 'koneksi.php';
session_start();

// Query produk dengan informasi stok
$brands = ['Baby Alive', 'Playdoh', 'Hot Wheels', 'Lego'];
$products = [];

foreach ($brands as $brand) {
    $query = $koneksi->prepare("SELECT p.id_produk, p.nama_produk, p.harga, p.nama_file, p.stok, m.nama_merk 
                               FROM produk p 
                               JOIN merk m ON p.id_merk = m.id_merk 
                               WHERE m.nama_merk = ? 
                               ORDER BY p.id_produk DESC 
                               LIMIT 1");

    $query->bind_param("s", $brand);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $query->close();
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
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
    /* Styles for stock display */
    .stock-info {
      font-size: 12px;
      margin: 5px 0;
      color: #666;
    }
    .stock-out {
      color: red;
      font-weight: bold;
    }
    .stock-low {
      color: orange;
    }
    .disabled-btn {
      opacity: 0.6;
      cursor: not-allowed;
    }
    
    /* Existing styles */
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
                      <?php if (isset($_SESSION['id_pembeli'])): ?>
                        <a href="user.php">Profil</a>
                        <a href="pesanan.php">Pesanan Saya</a>
                        <a href="logout.php">Logout</a>
                      <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php">Register</a>
                      <?php endif; ?>
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
                          <button class="btn-continue">Lanjutkan belanja</button>
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
        
        <!-- Slider -->
        <div class="slider">
            <figure>
                <div class="slide">
                    <img src="gambar/slide3.jpg" alt="lego">
                </div>
                <div class="slide">
                    <img src="gambar/slide2.jpg" alt="car">
                </div>
                <div class="slide">
                    <img src="gambar\bg1.jpg" alt="baby">
                </div>
            </figure>
            </div>

            <section class="brands-section">
                <div class="container">
                    <h2 class="section-title">Our Featured Brands</h2>
                    <div class="brands-grid">
                            <div class="brand-item">
                                <a href="playdoh.php">
                                <img src="gambar/playdohhhhhhh.png" alt="Baby Alive">
                              </a>
                          </div>

                          <div class="brand-item">
                              <a href="lego.php">
                                <img src="gambar/LEGO-Logo-1972-1998.png" alt="LEGO" width="200px">
                              </a>
                            </div>
                          
                            <div class="brand-item">
                              <a href="hotwheels.php">
                                <img src="gambar/hwlogo.png" alt="NERF">
                              </a>
                            </div>
                          
                            <div class="brand-item">
                              <a href="bebyalive.php">
                                <img src="gambar/baby-alive-logo-467633A3DB-seeklogo.com.png" alt="Baby Alive">
                              </a>
                          </div>
                          
                    </div>
                </div>
            </section>


            <div class="hero-content-text-box">
                <div class="hero-content-wrapper">
                    <div class="hero-image">
                        <img src="gambar/hero ba.jpg" alt="gambar">
                    </div>
                    <div class="hero-text">
                        <h1>Welcome to ToyToy Kids Store!</h1>
                        <p>ToyToy is the most explorative toy store that offers a unique experience for play where parents & kids can team up together. Let your imagination run as you embark on a new adventure filled with thrills and excitement. Welcome to ToyToy Imagine Play</p>
                        <a href="allproduct.php" class="hero-button">Explore Now</a>
                    </div>
                </div>
            </div>
            

            <section class="category-section">
              <h2 class="section-title">Shop By Category</h2>
              <div class="categories">
                <div class="category">
                  <a href="toys-cars.php">
                    <i data-lucide="car" class="icon"></i>
                    <p class="blue">Toys Cars</p>
                  </a>
                </div>
                <div class="category">
                  <a href="figurines.php">
                    <i data-lucide="smile" class="icon"></i>
                    <p class="brown">FIGURINES</p>
                  </a>
                </div>
                <div class="category">
                  <a href="puzzles.php">
                    <i data-lucide="puzzle" class="icon"></i>
                    <p class="green">PUZZLES</p>
                  </a>
                </div>
                <div class="category">
                  <a href="video-games.php">
                    <i data-lucide="gamepad" class="icon"></i>
                    <p class="purple">VIDEO GAMES</p>
                  </a>
                </div>
                <div class="category">
                  <a href="dolls.php">
                    <i data-lucide="baby" class="icon"></i>
                    <p class="brown">DOLLS</p>
                  </a>
                </div>
              </div>
            </section>
                        

            <div class="gambarr">
                <img src="gambar/slide2.jpg" alt="gambar">                
            </div>
                    

 <section class="products-showcase">
  <div class="products-header">
    <a href="allproduct.php" class="see-all-btn">
      See All Products <i data-lucide="arrow-up-right"></i>
    </a>
  </div>
  <div class="product-list">
    <?php foreach ($products as $produk): ?>
      <div class="product-card"
     data-name="<?= htmlspecialchars($produk['nama_produk']) ?>"
     data-price="<?= htmlspecialchars($produk['harga']) ?>"
     data-image="gambar/<?= htmlspecialchars($produk['nama_file']) ?>"
     data-product-id="<?= $produk['id_produk'] ?>"
     data-merk="<?= htmlspecialchars($produk['nama_merk']) ?>"
     data-stock="<?= $produk['stok'] ?>">

    <button class="wishlist-btn" onclick="toggleWishlist(event)">
        <i class="lucide-icon" data-lucide="heart"></i>
    </button>

    <a href="detail_produk.php?id=<?= $produk['id_produk'] ?>" class="product-link">
        <img src="gambar/<?= htmlspecialchars($produk['nama_file']) ?>" 
             alt="<?= htmlspecialchars($produk['nama_produk']) ?>" width="100%">
    </a>

    <div class="info">
        <a href="detail_produk.php?id=<?= $produk['id_produk'] ?>" class="product-title-link">
            <p class="product-title"><?= htmlspecialchars($produk['nama_produk']) ?></p>
        </a>
        <p class="product-price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
        
        <!-- Stock Information -->
        <p class="stock-info <?= ($produk['stok'] <= 0) ? 'stock-out' : (($produk['stok'] < 5) ? 'stock-low' : '') ?>">
            Stok: <?= $produk['stok'] ?>
        </p>
        
        <!-- Modified Add to Cart Button -->
        <button class="add-to-cart <?= ($produk['stok'] <= 0) ? 'disabled-btn' : '' ?>" 
                onclick="<?= ($produk['stok'] > 0) ? 'addToCart(event)' : 'return false' ?>"
                <?= ($produk['stok'] <= 0) ? 'disabled' : '' ?>>
            <?= ($produk['stok'] <= 0) ? 'Stok Habis' : 'Add to cart' ?>
        </button>
    </div>
</div>
    <?php endforeach; ?>
  </div>
</section>


            <footer class="main-footer">
                <div class="footer-container">
                    <div class="footer-column">
                        <h3 class="footer-title">TENTANG</h3>
                        <p>Toko mainan berkualitas sejak 2025. Menyediakan berbagai mainan edukatif dan koleksi eksklusif.</p>
                        <a href="#about" class="footer-link">Tentang Kami</a>
                    </div>
                    
                    <div class="footer-column">
                        <h3 class="footer-title">LAYANAN PELANGGAN</h3>
                        <ul class="footer-links">
                            <li><a href="#help">Bantuan</a></li>
                            <li><a href="#payment">Metode Pembayaran</a></li>
                            <li><a href="#shipping">Gratis Ongkir</a></li>
                            <li><a href="#contact">Hubungi Kami</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h3 class="footer-title">PEMBAYARAN</h3>
                        <div class="payment-methods">
                            <img src="gambar/gopay.png" alt="Gopay">
                            <img src="gambar/dana.png" alt="dana">
                        </div>
                        
                        <h3 class="social-title">IKUTI KAMI</h3>
                        <div class="social-media">
                            <a href="#facebook"><i class="fa fa-facebook"></i></a>
                            <a href="https://www.instagram.com/toytoy_kidsstore?igsh=MWNybDZkemhtdGx0dA=="><i class="fa fa-instagram"></i></a>
                            <a href="https://wa.me/nomor_anda"><i class="fa fa-whatsapp"></i></a>
                            <a href="https://youtube.com/@toytoyshop?si=yNXNYKnuUr-zLk1G"><i class="fa fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; 2025 ToyToyShop. All Rights Reserved.</p>
                </div>
            </footer>

            <script>
// User dropdown functionality
const userToggle = document.getElementById("user-toggle");
const userMenu = document.getElementById("user-menu");

userToggle.addEventListener("click", function (e) {
  e.stopPropagation();
  userMenu.style.display = userMenu.style.display === "block" ? "none" : "block";
});

// Close dropdown when clicking outside
document.addEventListener("click", function (e) {
  if (!userMenu.contains(e.target)) {
    userMenu.style.display = "none";
  }
});

// Cart functionality
const cart = JSON.parse(localStorage.getItem('cart')) || [];
const cartListNavbar = document.getElementById('cart-list-navbar');
const cartCount = document.querySelector('.cart-count');
const cartIcon = document.getElementById('cart-icon');
const cartPopup = document.getElementById('cart-popup');
const cartTotal = document.getElementById('cart-total');
const emptyCartMessage = document.getElementById('empty-cart');

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
  updateCartUI();
  lucide.createIcons();
});

// Update cart UI
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
    cartCount.textContent = '0';
    return;
  }

  cart.forEach((item, index) => {
    const li = document.createElement('li');
    const itemTotal = parseInt(item.price.replace(/[^\d]/g, ''), 10) * item.quantity;
    total += itemTotal;
    
    li.innerHTML = `
      <img src="${item.image}" alt="${item.title}" style="width: 40px; height: auto; vertical-align: middle; margin-right: 8px;">
      ${item.title} - ${item.price}
      <div>
        <button class="qty-btn minus" data-index="${index}">-</button>
        <span>${item.quantity}</span>
        <button class="qty-btn plus" data-index="${index}" ${item.quantity >= item.maxStock ? 'disabled' : ''}>+</button>
      </div>
      <button class="remove-item" data-index="${index}">&times;</button>
    `;
    cartListNavbar.appendChild(li);
  });

  if (emptyCartMessage) emptyCartMessage.style.display = 'none';
  cartTotal.textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
  cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
}

// Save cart to localStorage
function saveCartToLocalStorage() {
  localStorage.setItem('cart', JSON.stringify(cart));
}

// Add to cart function with stock check
function addToCart(event) {
  event.preventDefault();
  event.stopPropagation();
  
  const productCard = event.target.closest('.product-card');
  const productId = productCard.dataset.productId;
  const productName = productCard.dataset.name;
  const productPrice = productCard.querySelector('.product-price').textContent;
  const productImage = productCard.dataset.image;
  const productStock = parseInt(productCard.dataset.stock);
  
  // Check if product is out of stock
  if (productStock <= 0) {
    showNotification('Maaf, produk ini sudah habis');
    return;
  }
  
  // Find existing item in cart
  const existingItem = cart.find(item => item.id === productId);
  
  if (existingItem) {
    // Check if we can add more
    if (existingItem.quantity >= productStock) {
      showNotification('Maaf, stok tidak mencukupi');
      return;
    }
    existingItem.quantity += 1;
    existingItem.maxStock = productStock;
  } else {
    cart.push({ 
      id: productId,
      title: productName, 
      price: productPrice, 
      image: productImage, 
      quantity: 1,
      maxStock: productStock
    });
  }

  updateCartUI();
  saveCartToLocalStorage();
  showNotification(`${productName} ditambahkan ke keranjang`);
}

// Handle cart quantity changes
cartListNavbar?.addEventListener('click', function(e) {
  // Quantity decrease
  if (e.target.classList.contains('minus')) {
    const index = parseInt(e.target.dataset.index, 10);
    if (cart[index].quantity > 1) {
      cart[index].quantity--;
    } else {
      cart.splice(index, 1);
    }
    updateCartUI();
    saveCartToLocalStorage();
  }
  
  // Quantity increase
  if (e.target.classList.contains('plus')) {
    const index = parseInt(e.target.dataset.index, 10);
    if (cart[index].quantity < cart[index].maxStock) {
      cart[index].quantity++;
      updateCartUI();
      saveCartToLocalStorage();
    } else {
      showNotification('Tidak bisa menambah, stok terbatas');
    }
  }
  
  // Remove item
  if (e.target.classList.contains('remove-item')) {
    const index = parseInt(e.target.dataset.index, 10);
    const removedItem = cart.splice(index, 1)[0];
    updateCartUI();
    saveCartToLocalStorage();
    showNotification(`${removedItem.title} dihapus dari keranjang`);
  }
});

// Cart popup toggle
cartIcon?.addEventListener('click', function(e) {
  e.preventDefault();
  if (cartPopup) {
    cartPopup.style.display = cartPopup.style.display === 'block' ? 'none' : 'block';
  }
});

// Close cart when clicking outside
document.addEventListener('click', function(e) {
  if (!cartIcon?.contains(e.target) && !cartPopup?.contains(e.target)) {
    if (cartPopup) cartPopup.style.display = 'none';
  }
});

// Wishlist functionality
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

// Search functionality
function searchProducts(event) {
  event.preventDefault();
  
  const searchTerm = document.querySelector('.search-input').value.toLowerCase();
  const productCards = document.querySelectorAll('.product-card');

  productCards.forEach(card => {
    const productName = card.getAttribute('data-name').toLowerCase();
    
    if (searchTerm === "" || productName.includes(searchTerm)) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
  
  return false;
}

// Notification function
function showNotification(message) {
  alert(message); 
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  initializeWishlistButtons();
  updateWishlistDisplay();
});
</script>

<script>
  lucide.createIcons();
</script>
</body>
</html>