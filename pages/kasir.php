<?php
// --- LOGIC ---
$page_title = 'Halaman Kasir (POS)';
require_once __DIR__ . '/../includes/header.php';
check_login();
require_once __DIR__ . '/../config/database.php';

// Handle "Selesaikan Transaksi" (untuk pembayaran tunai/non-QRIS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_items'])) {
    $cart_items = json_decode($_POST['cart_items'], true);
    $nama_customer = !empty($_POST['nama_customer']) ? $_POST['nama_customer'] : 'Pelanggan';
    $user_id = $_SESSION['user_id'];

    if (!empty($cart_items)) {
        try {
            $pdo->beginTransaction();
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, nama_customer, product_id, kuantitas, sub_total, status_pembayaran) VALUES (?, ?, ?, ?, ?, 'lunas')");
                $stmt->execute([$user_id, $nama_customer, $item['id'], $item['kuantitas'], ($item['harga'] * $item['kuantitas'])]);
                
                $stmt_stok = $pdo->prepare("UPDATE products SET stok = stok - ? WHERE id = ?");
                $stmt_stok->execute([$item['kuantitas'], $item['id']]);
            }
            $pdo->commit();
            $_SESSION['success_message'] = "Transaksi berhasil disimpan!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Transaksi gagal: " . $e->getMessage();
        }
    } else {
         $_SESSION['error_message'] = "Keranjang kosong.";
    }
    header("Location: kasir.php");
    exit();
}

$products = $pdo->query("SELECT * FROM products WHERE stok > 0 ORDER BY nama_barang ASC")->fetchAll(PDO::FETCH_ASSOC);

$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// --- TAMPILAN ---
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6"><h1 class="text-2xl font-bold text-gray-800">Halaman Kasir (Point of Sale)</h1></header>
    <main class="flex-1 p-6 bg-gray-50">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4">Daftar Produk</h2>
                <input type="text" id="searchProduct" class="w-full px-4 py-2 border rounded-lg mb-4" placeholder="Cari produk...">
                <div id="productList" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[60vh] overflow-y-auto">
                    <?php foreach ($products as $product): ?>
                        <div class="product-item border p-3 rounded-lg text-center cursor-pointer hover:bg-yellow-100" data-id="<?= $product['id'] ?>" data-nama="<?= htmlspecialchars($product['nama_barang']) ?>" data-harga="<?= $product['harga'] ?>" data-stok="<?= $product['stok'] ?>"><p class="font-semibold"><?= htmlspecialchars($product['nama_barang']) ?></p><p class="text-sm text-gray-500"><?= format_rupiah($product['harga']) ?></p><p class="text-xs text-gray-400">Stok: <?= $product['stok'] ?></p></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md sticky top-6">
                <h2 class="text-xl font-bold mb-4">Keranjang</h2>
                <?php if ($success_message): ?><div class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $success_message ?></div><?php endif; ?>
                <?php if ($error_message): ?><div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error_message ?></div><?php endif; ?>
                <form action="kasir.php" method="POST" id="checkoutForm">
                    <div class="mb-4"><label for="nama_customer" class="block text-sm font-medium">Nama Customer (Opsional)</label><input type="text" name="nama_customer" id="nama_customer" class="w-full px-3 py-2 mt-1 border rounded-md"></div>
                    <div id="cartItems" class="space-y-2 mb-4 max-h-[40vh] overflow-y-auto"></div><hr class="my-4">
                    <div class="flex justify-between font-bold text-lg"><span>Total</span><span id="cartTotal">Rp 0</span></div>
                    <input type="hidden" name="cart_items" id="cartItemsInput">
                    <button type="submit" id="checkoutButton" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 mt-4 rounded-lg" disabled>Selesaikan Transaksi (Tunai)</button>
                    <button type="button" id="bayarQRIS" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 mt-2 rounded-lg">Bayar dengan QRIS</button>
                </form>
            </div>
        </div>
    </main>
</div>

<div id="qrisModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg text-center">
        <h2 class="text-2xl font-bold mb-4">Pindai untuk Membayar</h2>
        <div id="qrisImageContainer" class="flex justify-center"></div>
        <p class="mt-4 font-semibold text-3xl">Total: <span id="qrisTotal"></span></p>
        <p id="qrisStatus" class="mt-2 text-lg text-yellow-500 font-bold">Menunggu Pembayaran...</p>
        <p class="text-xs text-gray-500 mt-2">Untuk testing, copy URL dari console log (F12) ke Simulator Midtrans.</p>
        <button id="closeModal" class="mt-6 bg-gray-300 px-4 py-2 rounded">Tutup</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let cart = [];
    let paymentCheckInterval; 

    const productList = document.getElementById('productList');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotalElement = document.getElementById('cartTotal');
    const checkoutButton = document.getElementById('checkoutButton');
    const searchProductInput = document.getElementById('searchProduct');
    const checkoutForm = document.getElementById('checkoutForm');
    const cartItemsInput = document.getElementById('cartItemsInput');
    const bayarQRISButton = document.getElementById('bayarQRIS');
    const qrisModal = document.getElementById('qrisModal');
    const qrisImageContainer = document.getElementById('qrisImageContainer');
    const qrisTotal = document.getElementById('qrisTotal');
    const qrisStatus = document.getElementById('qrisStatus');
    const closeModalButton = document.getElementById('closeModal');

    function renderCart() {
        cartItemsContainer.innerHTML = '';
        let total = cart.reduce((sum, item) => sum + (item.harga * item.kuantitas), 0);
        cart.forEach((item, index) => {
            const itemElement = document.createElement('div');
            itemElement.className = 'flex items-center justify-between border-b pb-2';
            itemElement.innerHTML = `<div><p class="font-semibold">${item.nama}</p><p class="text-sm text-gray-600">${formatRupiah(item.harga)}</p></div><div class="flex items-center"><button type="button" class="kuantitas-btn bg-gray-200 px-2 rounded-l" data-index="${index}" data-action="decrease">-</button><span class="px-3">${item.kuantitas}</span><button type="button" class="kuantitas-btn bg-gray-200 px-2 rounded-r" data-index="${index}" data-action="increase" ${item.kuantitas >= item.stok ? 'disabled' : ''}>+</button></div>`;
            cartItemsContainer.appendChild(itemElement);
        });
        cartTotalElement.textContent = formatRupiah(total);
        checkoutButton.disabled = cart.length === 0;
        cartItemsInput.value = JSON.stringify(cart);
    }

    function formatRupiah(angka) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka); }

    productList.addEventListener('click', (e) => {
        const productDiv = e.target.closest('.product-item'); if (!productDiv) return;
        const id = parseInt(productDiv.dataset.id), nama = productDiv.dataset.nama, harga = parseFloat(productDiv.dataset.harga), stok = parseInt(productDiv.dataset.stok);
        const existingItem = cart.find(item => item.id === id);
        if (existingItem) { if (existingItem.kuantitas < stok) { existingItem.kuantitas++; } else { alert('Stok tidak mencukupi!'); } } else { cart.push({ id, nama, harga, kuantitas: 1, stok }); } renderCart();
    });

    cartItemsContainer.addEventListener('click', (e) => {
        if (!e.target.classList.contains('kuantitas-btn')) return;
        const index = parseInt(e.target.dataset.index), action = e.target.dataset.action; let item = cart[index];
        if (action === 'increase' && item.kuantitas < item.stok) item.kuantitas++; else if (action === 'decrease') item.kuantitas--;
        if (item.kuantitas === 0) cart.splice(index, 1); renderCart();
    });

    searchProductInput.addEventListener('keyup', () => { document.querySelectorAll('.product-item').forEach(product => product.style.display = product.dataset.nama.toLowerCase().includes(searchProductInput.value.toLowerCase()) ? '' : 'none'); });
    checkoutForm.addEventListener('submit', (e) => { if (cart.length === 0) { e.preventDefault(); alert('Keranjang belanja kosong!'); } });

    bayarQRISButton.addEventListener('click', async function() {
        if (cart.length === 0) { alert('Keranjang kosong!'); return; }
        const totalBelanja = cart.reduce((sum, item) => sum + (item.harga * item.kuantitas), 0);
        if (totalBelanja <= 0) return;

        qrisTotal.textContent = formatRupiah(totalBelanja);
        qrisModal.classList.remove('hidden');
        qrisImageContainer.innerHTML = '<p>Membuat QR Code...</p>';
        resetStatus();

        try {
            const response = await fetch('<?= $base_url ?>/api/buat_transaksi_qris.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart_items: cart, nama_customer: document.getElementById('nama_customer').value })
            });
            const data = await response.json();
            if (!response.ok || data.status !== 'ok') { throw new Error(data.message || 'Gagal membuat transaksi.'); }

            console.log('--- UNTUK SIMULATOR ---');
            console.log('URL Gambar QR:', data.qr_image_url);
            console.log('-----------------------');

            qrisImageContainer.innerHTML = '';
            new QRCode(qrisImageContainer, { text: data.qr_string, width: 256, height: 256 });
            paymentCheckInterval = setInterval(() => { checkPaymentStatus(data.order_id); }, 3000);
        } catch (error) {
            qrisImageContainer.innerHTML = `<p class="text-red-500">${error.message}</p>`;
        }
    });

    async function checkPaymentStatus(orderId) {
        try {
            const response = await fetch(`<?= $base_url ?>/api/cek_status.php?order_id=${orderId}`);
            const data = await response.json();
            if (data.status === 'paid') {
                clearInterval(paymentCheckInterval);
                qrisStatus.textContent = "Pembayaran Berhasil! Memproses...";
                qrisStatus.className = 'mt-2 text-lg text-green-500 font-bold';
                finalizeTransaction(orderId);
            }
        } catch (error) { console.error("Gagal mengecek status: ", error); }
    }

    async function finalizeTransaction(orderId) {
        try {
            const response = await fetch('<?= $base_url ?>/api/finalisasi_transaksi.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId })
            });
            const result = await response.json();
            if (result.status === 'success') {
                qrisStatus.textContent = "Transaksi Sukses Disimpan!";
                setTimeout(() => { window.location.reload(); }, 2000);
            } else {
                qrisStatus.textContent = `Error: ${result.message}`;
                qrisStatus.className = 'mt-2 text-lg text-red-500 font-bold';
            }
        } catch (error) { qrisStatus.textContent = `Error: Gagal memfinalisasi transaksi.`; qrisStatus.className = 'mt-2 text-lg text-red-500 font-bold'; }
    }
    
    function resetStatus() {
        qrisStatus.textContent = "Menunggu Pembayaran...";
        qrisStatus.className = 'mt-2 text-lg text-yellow-500 font-bold';
    }

    closeModalButton.addEventListener('click', function() {
        qrisModal.classList.add('hidden');
        clearInterval(paymentCheckInterval);
        resetStatus();
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>