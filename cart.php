<?php
session_start();

$products = require __DIR__ . '/products.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index';

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action === 'add' && $id > 0 && isset($products[$id])) {
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = 0;
    }
    $_SESSION['cart'][$id] += $qty;

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
        exit;
    }

    // Redirect back
    if ($redirect === 'menu') {
        header('Location: index.php#menu');
    } elseif ($redirect === 'detail' && isset($_GET['detail_id'])) {
        header('Location: detail.php?id=' . (int)$_GET['detail_id']);
    } else {
        header('Location: index.php');
    }
    exit;
}

if ($action === 'remove' && $id > 0) {
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
        exit;
    }

    header('Location: cart.php');
    exit;
}

if ($action === 'update' && $id > 0 && isset($_GET['qty'])) {
    $qty = (int)$_GET['qty'];
    if ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id] = $qty;
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
        exit;
    }

    header('Location: cart.php');
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => 0]);
        exit;
    }

    header('Location: cart.php');
    exit;
}

// Calculate totals
$cart_items = [];
$total = 0;
$cart_count = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    if (isset($products[$product_id])) {
        $product = $products[$product_id];
        $subtotal = $product['price_num'] * $quantity;

        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];

        $total += $subtotal;
        $cart_count += $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Keranjang - MochiLicious</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --pink: #f0b8c8;
            --pink-dark: #e8a8b8;
            --pink-light: #f8d8e0;
            --mochi: #f8f5f0;
            --brown: #8b6f5c;
            --text: #3d3d3d;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--mochi);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 900;
            color: var(--pink-dark) !important;
            letter-spacing: -1px;
        }

        .cart-container {
            margin-top: 80px;
            padding-bottom: 80px;
        }

        .cart-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .cart-header {
            background: linear-gradient(135deg, #f0b8c8 0%, #e8a8b8 100%);
            color: white;
            padding: 20px;
        }

        .cart-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        .cart-empty {
            text-align: center;
            padding: 60px 20px;
        }

        .cart-empty i {
            font-size: 80px;
            color: var(--pink-light);
            margin-bottom: 20px;
        }

        .cart-empty h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--text);
            margin-bottom: 10px;
        }

        .cart-empty p {
            color: #666;
            margin-bottom: 30px;
        }

        .cart-table {
            width: 100%;
        }

        .cart-table th {
            background: #f8f9fa;
            padding: 15px;
            font-weight: 600;
            color: var(--text);
            border-bottom: 2px solid #dee2e6;
        }

        .cart-table td {
            padding: 20px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-details h4 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 5px 0;
        }

        .product-details .price {
            color: var(--pink-dark);
            font-weight: 600;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--text);
        }

        .qty-btn:hover {
            background: var(--pink-light);
            border-color: var(--pink);
        }

        .qty-input {
            width: 60px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 8px;
            font-weight: 600;
        }

        .subtotal {
            font-weight: 700;
            color: var(--pink-dark);
            font-size: 18px;
        }

        .remove-btn {
            color: #ff4757;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .remove-btn:hover {
            background: rgba(255, 71, 87, 0.1);
        }

        .total-section {
            background: var(--mochi);
            padding: 30px;
            border-top: 1px solid #dee2e6;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .total-label {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
        }

        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: var(--pink-dark);
        }

        .checkout-section {
            margin-top: 30px;
        }

        .btn-cart {
            background: linear-gradient(135deg, #f0b8c8 0%, #e8a8b8 100%);
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
        }

        .btn-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(240, 184, 200, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-cart.secondary {
            background: #6c757d;
        }

        .btn-cart.secondary:hover {
            background: #5a6268;
            box-shadow: 0 5px 20px rgba(108, 117, 125, 0.4);
        }

        .btn-cart.danger {
            background: #dc3545;
        }

        .btn-cart.danger:hover {
            background: #c82333;
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.4);
        }

        /* Cart Badge */
        .cart-badge {
            position: relative;
        }

        .cart-badge .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .cart-table {
                font-size: 14px;
            }

            .product-info {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .product-img {
                width: 60px;
                height: 60px;
            }

            .quantity-controls {
                justify-content: center;
            }

            .total-section {
                padding: 20px;
            }

            .checkout-section {
                text-align: center;
            }

            .btn-cart {
                margin-bottom: 10px;
            }
        
   
                gap: 10px;
            }

            .product-img {
                width: 60px;
                height: 60px;
            }

            .quantity-controls {
                justify-content: center;
            }

            .total-section {
                padding: 20px;
            }

            .checkout-section {
                text-align: center;
            }

            .btn-cart {
                margin-bottom: 10px;
            }
        
    </style>
</head>
<body>
    <?php $activeNav = ''; include __DIR__ . '/partials/navbar.php'; ?>

    <div class="container cart-container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="cart-card">
                    <div class="cart-header">
                        <h2><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h2>
                    </div>

                    <?php if (empty($cart_items)): ?>
                        <div class="cart-empty">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>Keranjang Kosong</h3>
                            <p>Belum ada produk di keranjang Anda. Yuk mulai berbelanja!</p>
                            <a href="index.php" class="btn-cart">
                                <i class="fas fa-arrow-left"></i> Lanjut Belanja
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="cart-table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="product-info">
                                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="product-img" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22 viewBox=%220 0 80 80%22><rect width=%2280%22 height=%2280%22 fill=%22%23f8f9fa%22/><text x=%2240%22 y=%2245%22 text-anchor=%22middle%22 fill=%22%23666%22 font-size=%2212%22>Image</text></svg>'">
                                                    <div class="product-details">
                                                        <h4><?php echo $item['name']; ?></h4>
                                                        <div class="price"><?php echo $item['price']; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo $item['price']; ?></td>
                                            <td>
                                                <div class="quantity-controls">
                                                    <div class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                                        <i class="fas fa-minus"></i>
                                                    </div>
                                                    <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                                                    <div class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">
                                                        <i class="fas fa-plus"></i>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="subtotal">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                            <td>
                                                <div class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="total-section">
                            <div class="total-row">
                                <span class="total-label">Total Pembayaran:</span>
                                <span class="total-amount">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                            </div>

                            <div class="checkout-section">
                                <a href="index.php" class="btn-cart secondary">
                                    <i class="fas fa-arrow-left"></i> Lanjut Belanja
                                </a>
                                <a href="#" class="btn-cart" onclick="checkout()">
                                    <i class="fab fa-whatsapp"></i> Pesan via WhatsApp
                                </a>
                                <a href="#" class="btn-cart danger" onclick="clearCart()">
                                    <i class="fas fa-trash"></i> Kosongkan Keranjang
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQuantity(productId, newQty) {
            newQty = parseInt(newQty);
            if (newQty < 0) newQty = 0;

            fetch('cart.php?action=update&id=' + productId + '&qty=' + newQty, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function removeItem(productId) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
                fetch('cart.php?action=remove&id=' + productId, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }

        function clearCart() {
            if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                fetch('cart.php?action=clear', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }

        function checkout() {
            const isGuest = <?php echo isset($_SESSION['user_id']) ? 'false' : 'true'; ?>;
            let customerName = '';
            if (isGuest) {
                customerName = prompt('Nama pemesan:');
                if (customerName === null) return;
            }

            // Buka tab WhatsApp sekarang (supaya tidak diblokir browser), isi alamatnya setelah pesanan tersimpan
            const waTab = window.open('', '_blank');

            const form = new FormData();
            form.append('name', customerName);

            fetch('checkout.php', {
                method: 'POST',
                body: form,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .catch(() => ({ success: false }))
            .then(data => {
                let message = 'Halo MochiLicious! Saya ingin memesan:%0A%0A';
                <?php foreach ($cart_items as $item): ?>
                    message += '<?php echo $item['quantity']; ?>x <?php echo $item['name']; ?> (<?php echo $item['price']; ?>)%0A';
                <?php endforeach; ?>
                message += '%0ATotal: Rp <?php echo number_format($total, 0, ',', '.'); ?>%0A';
                if (customerName) {
                    message += 'Atas nama: ' + encodeURIComponent(customerName) + '%0A';
                }
                if (data.success) {
                    message += 'No. Pesanan: ' + data.order_number + '%0A';
                }
                message += '%0AMohon konfirmasi pesanan saya. Terima kasih!';

                const whatsappUrl = 'https://wa.me/6212345678910?text=' + message;
                if (waTab) { waTab.location = whatsappUrl; } else { window.location = whatsappUrl; }

                // Pesanan tersimpan -> keranjang sudah dikosongkan di server
                if (data.success) {
                    setTimeout(() => location.reload(), 800);
                }
            });
        }
    </script>
</body>
</html>