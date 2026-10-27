<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

$products = require __DIR__ . '/products.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!isset($products[$id])) {
    header('Location: produk.php');
    exit;
}
$product = $products[$id];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - MochiLicious</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--mochi);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 900;
            color: var(--pink-dark) !important;
            letter-spacing: -1px;
        }

        .detail-container {
            padding: 80px 0;
        }

        .product-detail {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }

        .btn-close-detail {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 45px;
            height: 45px;
            background: rgba(240, 184, 200, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pink-dark);
            font-size: 24px;
            text-decoration: none;
            transition: all 0.3s;
            z-index: 10;
        }

        .btn-close-detail:hover {
            background: rgba(240, 184, 200, 0.3);
            transform: scale(1.1);
        }

        .product-image {
            position: relative;
            height: 450px;
            background: linear-gradient(135deg, #f8d8e0 0%, #f0b8c8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 30px;
            border-radius: 20px;
            margin: 20px;
        }

        .product-image img,
        .product-image svg {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: all 0.4s ease;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.1));
        }

        .product-detail:hover .product-image img,
        .product-detail:hover .product-image svg {
            transform: scale(1.03) rotate(1deg);
        }

        .product-image i {
            font-size: 120px;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2));
        }

        .product-detail:hover .product-image i {
            transform: scale(1.05) rotate(2deg);
        }

        .product-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ff6b8f 0%, #ff4d7a 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            z-index: 3;
            box-shadow: 0 5px 15px rgba(255, 77, 122, 0.4);
        }

        .product-info {
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 900;
            color: var(--text);
            margin-bottom: 15px;
        }

        .product-price {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            color: var(--pink-dark);
            margin-bottom: 25px;
        }

        .product-description {
            font-size: 18px;
            line-height: 1.7;
            color: #666;
            margin-bottom: 40px;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            margin-top: 40px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--pink) 0%, var(--pink-dark) 100%);
            border: none;
            border-radius: 50px;
            padding: 18px 40px;
            font-weight: 600;
            font-size: 18px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: white;
            white-space: nowrap;
            min-width: 200px;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(240, 184, 200, 0.4);
            color: white;
            text-decoration: none;
        }

        .features {
            background: var(--mochi);
            padding: 60px 0;
            margin-top: 60px;
        }

        .feature-item {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.4s;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(240, 184, 200, 0.2);
        }

        .feature-item i {
            font-size: 48px;
            color: var(--pink-dark);
            margin-bottom: 20px;
        }

        .feature-item h4 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
        }

        .feature-item p {
            color: var(--brown);
            line-height: 1.6;
        }

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

        @media (max-width: 768px) {
            .product-image {
                height: 350px;
                padding: 20px;
                margin: 10px;
            }

            .product-info {
                padding: 30px;
            }

            .product-name {
                font-size: 28px;
            }

            .product-price {
                font-size: 32px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-primary {
                justify-content: center;
                min-width: auto;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php $activeNav = 'produk'; include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Product Detail -->
    <div class="container detail-container">
        <div class="product-detail">
            <a href="index.php" class="btn-close-detail">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="product-image">
                        <?php if ($product['badge']): ?>
                            <span class="product-badge"><?php echo $product['badge']; ?></span>
                        <?php endif; ?>
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-image\'></i><p style=\'color: white; margin-top: 20px;\'>Gambar tidak dapat dimuat</p>';" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="product-info">
                        <h1 class="product-name"><?php echo $product['name']; ?></h1>
                        <div class="product-price"><?php echo $product['price']; ?></div>
                        <div class="product-description"><?php echo $product['desc']; ?></div>

                        <div class="action-buttons">
                            <button class="btn-primary" onclick="addToCart(<?php echo $id; ?>)">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <h4>Pesan 24 Jam</h4>
                        <p>Pesan kapan saja melalui website kami</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="fas fa-truck"></i>
                        <h4>Pengiriman Aman</h4>
                        <p>Kemasan khusus menjaga kesegaran mochi</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="fas fa-heart"></i>
                        <h4>Dibuat dengan Cinta</h4>
                        <p>Setiap mochi dibuat secara handmade</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(productId) {
            fetch('cart.php?action=add&id=' + productId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    document.querySelector('.cart-badge .badge').textContent = data.cart_count;

                    // Show success message
                    showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
        }

        function showNotification(message, type) {
            // Remove existing notification
            const existing = document.querySelector('.notification');
            if (existing) existing.remove();

            // Create notification
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: ${type === 'success' ? '#e58fa8' : '#ff4757'};
                color: white;
                padding: 15px 25px;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                z-index: 1000;
                font-weight: 600;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Add notification animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>