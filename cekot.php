<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout Toytoy</title>
  <link rel="stylesheet" href="cekot.css">
</head>
<body>

<div class="checkout-container">
  <h2>Informasi Pengiriman</h2>

  <form id="checkout-form" method="POST" action="proses_cekot.php">
    <!-- Form input tetap sama -->
    <div class="form-group">
      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" required>
    </div>
    <div class="form-group">
      <label for="alamat">Alamat Lengkap</label>
      <input type="text" id="alamat" name="alamat" required>
    </div>
    <div class="form-group">
      <label for="kota">Kota</label>
      <input type="text" id="kota" name="kota" required>
    </div>
    <div class="form-group">
      <label for="provinsi">Provinsi</label>
      <select id="provinsi" name="provinsi" required onchange="updateShippingOptions()">
        <option value="">Pilih Provinsi</option>
        <!-- Pulau Jawa -->
        <option value="DKI Jakarta">DKI Jakarta</option>
        <option value="Jawa Barat">Jawa Barat</option>
        <option value="Jawa Tengah">Jawa Tengah</option>
        <option value="DI Yogyakarta">DI Yogyakarta</option>
        <option value="Jawa Timur">Jawa Timur</option>
        <option value="Banten">Banten</option>
        <!-- Luar Jawa -->
        <option value="Bali">Bali</option>
        <option value="Sumatera Utara">Sumatera Utara</option>
        <option value="Kalimantan Barat">Kalimantan Barat</option>
        <!-- Tambahkan provinsi lainnya sesuai kebutuhan -->
      </select>
    </div>
    <div class="form-group">
      <label for="nohp">Nomor Handphone</label>
      <input type="text" id="nohp" name="nohp" required>
    </div>

    <!-- Input hidden -->
    <input type="hidden" id="produkData" name="produk">
    <input type="hidden" id="total" name="total">
    <input type="hidden" id="shippingMethod" name="shippingMethod">
    <input type="hidden" id="shippingCost" name="shippingCost">

    <h3>Produk</h3>
    <div id="cart-items"></div>

    <div class="shipping-method" id="shipping-method-container">
      <h3>Metode Pengiriman</h3>
      <!-- Opsi pengiriman akan diupdate berdasarkan provinsi -->
    </div>

    <div class="summary">
      <h3>Ringkasan Pesanan</h3>
      <p>Subtotal: <span id="subtotal">Rp 0</span></p>
      <p>Ongkir: <span id="shipping">FREE</span></p>
      <hr>
      <p><strong>Total: <span id="total-display">Rp 0</span></strong></p>
    </div>

    <button type="button" onclick="goToPayment()">Lanjut ke Pembayaran</button>
  </form>
</div>

<script>
// Daftar provinsi di Pulau Jawa
const javaProvinces = [
  "DKI Jakarta", "Jawa Barat", "Jawa Tengah", 
  "DI Yogyakarta", "Jawa Timur", "Banten"
];

// Fungsi untuk mengecek apakah provinsi termasuk Pulau Jawa
function isJavaProvince(province) {
  return javaProvinces.includes(province);
}

// Fungsi untuk mengupdate opsi pengiriman berdasarkan provinsi
function updateShippingOptions() {
  const province = document.getElementById("provinsi").value;
  const container = document.getElementById("shipping-method-container");
  
  if (!province) return;

  if (isJavaProvince(province)) {
    // Gratis ongkir untuk Pulau Jawa
    container.innerHTML = `
      <h3>Metode Pengiriman</h3>
      <label>
        <input type="radio" name="shipping" value="0" data-name="JNE Regular" checked>
        JNE Regular (Gratis Ongkir)
      </label>
      <label>
        <input type="radio" name="shipping" value="10000" data-name="JNE YES">
        JNE Express (+Rp 10.000)
      </label>
    `;
  } else {
    // Berbayar untuk luar Jawa
    container.innerHTML = `
      <h3>Metode Pengiriman</h3>
      <label>
        <input type="radio" name="shipping" value="15000" data-name="JNE Regular" checked>
        JNE Regular (Rp 15.000)
      </label>
      <label>
        <input type="radio" name="shipping" value="25000" data-name="JNE YES">
        JNE Express (Rp 25.000)
      </label>
    `;
  }
  
  // Tambahkan event listener untuk opsi baru
  document.querySelectorAll('input[name="shipping"]').forEach(radio => {
    radio.addEventListener('change', updateTotal);
  });
  
  updateTotal();
}

// Fungsi format Rupiah
function formatRupiah(num) {
  return 'Rp ' + num.toLocaleString('id-ID');
}

// Fungsi untuk menampilkan produk yang akan dibeli
function displayCheckoutItems() {
  const container = document.getElementById("cart-items");
  container.innerHTML = "";
  
  const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
  const checkoutItem = JSON.parse(localStorage.getItem("checkoutItem"));
  
  if (checkoutItem) {
    const productElement = document.createElement("div");
    productElement.classList.add("cart-item");
    
    productElement.innerHTML = `
      <img src="${checkoutItem.image}" alt="${checkoutItem.name}">
      <div class="cart-details">
        <div class="left">
          <p class="name">${checkoutItem.name}</p>
          <p class="price">${formatRupiah(checkoutItem.price)}</p>
        </div>
        <div class="right">
          <p class="quantity">Jumlah: ${checkoutItem.quantity}</p>
          <p class="total">Total: ${formatRupiah(checkoutItem.price * checkoutItem.quantity)}</p>
        </div>
      </div>
    `;
    container.appendChild(productElement);
  } else if (cartItems.length > 0) {
    cartItems.forEach(item => {
      const productElement = document.createElement("div");
      productElement.classList.add("cart-item");
      
      productElement.innerHTML = `
        <img src="${item.image}" alt="${item.name}">
        <div class="cart-details">
          <div class="left">
            <p class="name">${item.name}</p>
            <p class="price">${formatRupiah(item.price)}</p>
          </div>
          <div class="right">
            <p class="quantity">Jumlah: ${item.quantity}</p>
            <p class="total">Total: ${formatRupiah(item.price * item.quantity)}</p>
          </div>
        </div>
      `;
      container.appendChild(productElement);
    });
  } else {
    container.innerHTML = "<p>Tidak ada produk yang dipilih.</p>";
  }
}

// Fungsi untuk menghitung subtotal
function calculateSubtotal() {
  const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
  const checkoutItem = JSON.parse(localStorage.getItem("checkoutItem"));
  
  if (checkoutItem) {
    return checkoutItem.price * checkoutItem.quantity;
  } else if (cartItems.length > 0) {
    return cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
  }
  return 0;
}

// Fungsi untuk memperbarui total
function updateTotal() {
  const subtotal = calculateSubtotal();
  const selected = document.querySelector('input[name="shipping"]:checked');
  const shippingValue = selected ? parseInt(selected.value) : 0;
  const shippingMethod = selected ? selected.getAttribute('data-name') : '';

  document.getElementById('shipping').textContent = 
    shippingValue === 0 ? "FREE" : formatRupiah(shippingValue);
  document.getElementById('total-display').textContent = formatRupiah(subtotal + shippingValue);
  document.getElementById('subtotal').textContent = formatRupiah(subtotal);
  
  // Update hidden inputs
  document.getElementById('shippingMethod').value = shippingMethod;
  document.getElementById('shippingCost').value = shippingValue;
}

// Fungsi untuk mengarahkan ke halaman pembayaran
function goToPayment() {
  const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
  const checkoutItem = JSON.parse(localStorage.getItem("checkoutItem"));
  const selectedShipping = document.querySelector('input[name="shipping"]:checked');
  
  if (!selectedShipping) {
    alert("Silakan pilih metode pengiriman");
    return;
  }
  
  const shippingValue = parseInt(selectedShipping.value);
  const shippingMethod = selectedShipping.getAttribute('data-name');
  
  let products = [];
  let total = 0;
  
  if (checkoutItem) {
    products = [checkoutItem];
    total = checkoutItem.price * checkoutItem.quantity + shippingValue;
  } else if (cartItems.length > 0) {
    products = cartItems;
    total = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0) + shippingValue;
  }

  // Validasi form
  const form = document.getElementById("checkout-form");
  const inputs = form.querySelectorAll("[required]");
  let isValid = true;
  
  inputs.forEach(input => {
    if (!input.value) {
      input.style.borderColor = "red";
      isValid = false;
    } else {
      input.style.borderColor = "";
    }
  });
  
  if (!isValid) {
    alert("Harap lengkapi semua informasi pengiriman");
    return;
  }

  // Simpan data checkout
  const checkoutData = {
    shippingMethod: shippingMethod,
    shippingCost: shippingValue,
    totalCost: total,
    products: products,
    customerInfo: {
      name: document.getElementById("nama").value,
      address: document.getElementById("alamat").value,
      city: document.getElementById("kota").value,
      province: document.getElementById("provinsi").value,
      phone: document.getElementById("nohp").value
    }
  };
  
  localStorage.setItem('checkoutData', JSON.stringify(checkoutData));

  // Isi input hidden
  document.getElementById("produkData").value = JSON.stringify(products);
  document.getElementById("total").value = total;
  document.getElementById("shippingMethod").value = shippingMethod;
  document.getElementById("shippingCost").value = shippingValue;

  // Submit form
  form.submit();
}

// Inisialisasi saat halaman dibuka
document.addEventListener("DOMContentLoaded", () => {
  displayCheckoutItems();
  updateShippingOptions();
});
</script>

</body>
</html>