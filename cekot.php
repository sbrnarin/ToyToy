<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout Toytoy</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: #f3f3f3;
    }

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

    .error {
      border-color: red !important;
    }

    .error-message {
      color: red;
      font-size: 12px;
      margin-top: 5px;
      display: none;
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

<div class="checkout-container">
  <h2>Informasi Pengiriman</h2>

  <form id="checkout-form" method="POST" action="proses_cekot.php">
    <div class="form-group">
      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" required>
      <div class="error-message" id="nama-error">Harap isi nama lengkap</div>
    </div>
    <div class="form-group">
      <label for="alamat">Alamat Lengkap</label>
      <input type="text" id="alamat" name="alamat" required>
      <div class="error-message" id="alamat-error">Harap isi alamat lengkap</div>
    </div>
    <div class="form-group">
      <label for="kota">Kota</label>
      <input type="text" id="kota" name="kota" required>
      <div class="error-message" id="kota-error">Harap isi kota</div>
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
      <div class="error-message" id="provinsi-error">Harap pilih provinsi</div>
    </div>
    <div class="form-group">
      <label for="nohp">Nomor Handphone</label>
      <input type="text" id="nohp" name="nohp" required>
      <div class="error-message" id="nohp-error">Harap isi nomor handphone yang valid</div>
    </div>

    <!-- Input hidden -->
    <input type="hidden" id="produkData" name="produk">
    <input type="hidden" id="total" name="total">
    <input type="hidden" id="shippingMethod" name="shippingMethod">
    <input type="hidden" id="shippingCost" name="shippingCost">

    <h3>Produk</h3>
    <div class="cart-items-container" id="cart-items"></div>

    <div class="shipping-method" id="shipping-method-container">
      <h3>Metode Pengiriman</h3>
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
// daftar provinsi Jawa
const javaProvinces = [
  "DKI Jakarta", "Jawa Barat", "Jawa Tengah", 
  "DI Yogyakarta", "Jawa Timur", "Banten"
];

// memeriksa provinsi
function isJavaProvince(province) {
  return javaProvinces.includes(province);
}

// opsi pengiriman berdasarkan provinsi
function updateShippingOptions() {
  const province = document.getElementById("provinsi").value;
  const container = document.getElementById("shipping-method-container");
  
  // Validasi provinsi
  const provinsiError = document.getElementById("provinsi-error");
  if (!province) {
    provinsiError.style.display = 'block';
    document.getElementById("provinsi").classList.add('error');
    return;
  } else {
    provinsiError.style.display = 'none';
    document.getElementById("provinsi").classList.remove('error');
  }

  // jawa
  if (isJavaProvince(province)) {
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
  
  // listener untuk opsi baru
  document.querySelectorAll('input[name="shipping"]').forEach(radio => {
    radio.addEventListener('change', updateTotal);
  });
  
  updateTotal();
}


function formatRupiah(num) {
  return 'Rp ' + num.toLocaleString('id-ID');
}

//menampilkan item checkout
function displayCheckoutItems() {
  const container = document.getElementById("cart-items");
  container.innerHTML = "";
  
  let checkoutData = JSON.parse(localStorage.getItem("checkoutItems")) || [];
  
  // single item checkout 
  if (checkoutData.length === 0) {
    const singleItem = JSON.parse(localStorage.getItem("checkoutItem"));
    if (singleItem) {
      checkoutData = [singleItem];
    }
  }
  
  if (checkoutData.length === 0) {
    container.innerHTML = "<p>Tidak ada produk yang dipilih.</p>";
    return;
  }
  
  // semua item dalam keranjang
  checkoutData.forEach(item => {
    const productElement = document.createElement("div");
    productElement.classList.add("cart-item");
    
    productElement.innerHTML = `
      <img src="${item.image || 'placeholder.jpg'}" alt="${item.name}">
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
}

// menghitung subtotal
function calculateSubtotal() {
  let checkoutData = JSON.parse(localStorage.getItem("checkoutItems")) || [];
  
  // Fallback single item checkout 
  if (checkoutData.length === 0) {
    const singleItem = JSON.parse(localStorage.getItem("checkoutItem"));
    if (singleItem) {
      checkoutData = [singleItem];
    }
  }
  
  return checkoutData.reduce((total, item) => {
    return total + (item.price * item.quantity);
  }, 0);
}

// memperbarui total
function updateTotal() {
  const subtotal = calculateSubtotal();
  const selectedShipping = document.querySelector('input[name="shipping"]:checked');
  
  const shippingCost = selectedShipping ? parseInt(selectedShipping.value) : 0;
  const shippingMethod = selectedShipping ? selectedShipping.getAttribute('data-name') : '';
  const total = subtotal + shippingCost;

  document.getElementById('subtotal').textContent = formatRupiah(subtotal);
  document.getElementById('shipping').textContent = shippingCost === 0 ? "FREE" : formatRupiah(shippingCost);
  document.getElementById('total-display').textContent = formatRupiah(total);
  
  // Update input hidden
  document.getElementById('shippingMethod').value = shippingMethod;
  document.getElementById('shippingCost').value = shippingCost;
  document.getElementById('total').value = total;
  

  let checkoutData = JSON.parse(localStorage.getItem("checkoutItems")) || [];
  
  // Fallback singgel item
  if (checkoutData.length === 0) {
    const singleItem = JSON.parse(localStorage.getItem("checkoutItem"));
    if (singleItem) {
      checkoutData = [singleItem];
    }
  }
  
  document.getElementById("produkData").value = JSON.stringify(checkoutData);
}

// validasi form
function validateForm() {
  let isValid = true;
  
  // nama
  const nama = document.getElementById("nama");
  const namaError = document.getElementById("nama-error");
  if (!nama.value.trim()) {
    nama.classList.add('error');
    namaError.style.display = 'block';
    isValid = false;
  } else {
    nama.classList.remove('error');
    namaError.style.display = 'none';
  }
  
  // alamat
  const alamat = document.getElementById("alamat");
  const alamatError = document.getElementById("alamat-error");
  if (!alamat.value.trim()) {
    alamat.classList.add('error');
    alamatError.style.display = 'block';
    isValid = false;
  } else {
    alamat.classList.remove('error');
    alamatError.style.display = 'none';
  }
  
  // kota
  const kota = document.getElementById("kota");
  const kotaError = document.getElementById("kota-error");
  if (!kota.value.trim()) {
    kota.classList.add('error');
    kotaError.style.display = 'block';
    isValid = false;
  } else {
    kota.classList.remove('error');
    kotaError.style.display = 'none';
  }
  
  // provinsi
  const provinsi = document.getElementById("provinsi");
  const provinsiError = document.getElementById("provinsi-error");
  if (!provinsi.value) {
    provinsi.classList.add('error');
    provinsiError.style.display = 'block';
    isValid = false;
  } else {
    provinsi.classList.remove('error');
    provinsiError.style.display = 'none';
  }
  
  // no HP
  const nohp = document.getElementById("nohp");
  const nohpError = document.getElementById("nohp-error");
  const phoneRegex = /^[0-9]{10,13}$/;
  if (!phoneRegex.test(nohp.value)) {
    nohp.classList.add('error');
    nohpError.style.display = 'block';
    isValid = false;
  } else {
    nohp.classList.remove('error');
    nohpError.style.display = 'none';
  }
  
  return isValid;
}

// proses pembayaran
function goToPayment() {
  let checkoutData = JSON.parse(localStorage.getItem("checkoutItems")) || [];
  
  // untuk single item checkout 
  if (checkoutData.length === 0) {
    const singleItem = JSON.parse(localStorage.getItem("checkoutItem"));
    if (singleItem) {
      checkoutData = [singleItem];
    }
  }
  
  if (checkoutData.length === 0) {
    alert("Tidak ada produk yang dipilih untuk checkout");
    return;
  }
  
  // Validasi form
  if (!validateForm()) {
    return;
  }
  
  const selectedShipping = document.querySelector('input[name="shipping"]:checked');
  
  if (!selectedShipping) {
    alert("Silakan pilih metode pengiriman");
    return;
  }
  
  // Submit form
  document.getElementById("checkout-form").submit();
}

// Inisialisasi halaman dimuat
document.addEventListener("DOMContentLoaded", () => {
  // Tampilkan item checkout
  displayCheckoutItems();
  
  // opsi pengiriman
  updateShippingOptions();
  
  // validasi real-time
  document.getElementById("nama").addEventListener('input', function() {
    if (this.value.trim()) {
      this.classList.remove('error');
      document.getElementById("nama-error").style.display = 'none';
    }
  });
  
  document.getElementById("alamat").addEventListener('input', function() {
    if (this.value.trim()) {
      this.classList.remove('error');
      document.getElementById("alamat-error").style.display = 'none';
    }
  });
  
  document.getElementById("kota").addEventListener('input', function() {
    if (this.value.trim()) {
      this.classList.remove('error');
      document.getElementById("kota-error").style.display = 'none';
    }
  });
  
  document.getElementById("provinsi").addEventListener('change', function() {
    if (this.value) {
      this.classList.remove('error');
      document.getElementById("provinsi-error").style.display = 'none';
    }
  });
  
  document.getElementById("nohp").addEventListener('input', function() {
    const phoneRegex = /^[0-9]{10,13}$/;
    if (phoneRegex.test(this.value)) {
      this.classList.remove('error');
      document.getElementById("nohp-error").style.display = 'none';
    }
  });
});
</script>

</body>
</html>