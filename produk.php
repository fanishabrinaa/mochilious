<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

$products = require __DIR__ . '/products.php';

// Hitung produk per kategori untuk tombol filter
$categoryCounts = [];
foreach ($products as $p) {
    $categoryCounts[$p['category']] = ($categoryCounts[$p['category']] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - MochiLicious</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --pink: #f0b8c8;
            --pink-dark: #e8a8b8;
            --pink-light: #f8d8e0;
            --accent: #e58fa8;
            --accent-deep: #d9798f;
            --mochi: #f8f5f0;
            --brown: #8b6f5c;
            --text: #3d3d3d;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--mochi);
            overflow-x: hidden;
        }

        /* ===== Hero (sama seperti beranda) ===== */
        .hero-mochio {
            position: relative;
            width: 100%;
            min-height: 66vh;
            padding: 80px 20px 130px;
            background-image: url("images/mochilanscape.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-gradient {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to bottom, rgba(248,245,240,0) 65%, var(--mochi) 100%),
                radial-gradient(ellipse at center, rgba(255,255,255,0) 50%, rgba(255,255,255,0.95) 100%);
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 720px;
            padding: 0 20px;
            text-align: center;
        }

        .hero-inner h1 {
            font-family: 'Playfair Display', serif;
            font-size: 56px;
            font-weight: 700;
            color: #3a3a3a;
            margin-bottom: 18px;
        }

        .hero-inner p {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            line-height: 1.6;
            color: #6b6b6b;
            margin-bottom: 36px;
        }

        .hero-btn {
            display: inline-block;
            padding: 14px 36px;
            background: #f3a6b7;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            border-radius: 999px;
            transition: all 0.3s ease;
        }

        .hero-btn:hover {
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(243, 166, 183, 0.5);
        }

        /* ===== Panel filter ===== */
        #menu { scroll-margin-top: 90px; }

        .shop-toolbar {
            position: relative;
            z-index: 5;
            margin: -64px 0 34px;
            padding: 20px 24px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 18px 50px rgba(61, 61, 61, 0.10);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
        }

        .chips { display: flex; flex-wrap: wrap; gap: 10px; }

        .filter-btn {
            padding: 9px 20px;
            border: 2px solid var(--pink);
            background: transparent;
            color: var(--accent-deep);
            border-radius: 999px;
            font-family: inherit;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.25s;
        }

        .filter-btn .n { margin-left: 6px; font-size: 12px; opacity: .7; }

        .filter-btn:hover,
        .filter-btn.active {
            background: linear-gradient(135deg, var(--pink) 0%, var(--accent) 100%);
            border-color: transparent;
            color: #fff;
        }

        .toolbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .search-wrap { position: relative; }

        .search-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            pointer-events: none;
        }

        .search-input {
            width: 240px;
            padding: 11px 18px 11px 42px;
            border: 2px solid var(--pink);
            border-radius: 999px;
            font-family: inherit;
            font-size: 14px;
            background: #fff;
            color: var(--text);
        }

        .sort-select {
            padding: 11px 18px;
            border: 2px solid var(--pink);
            border-radius: 999px;
            font-family: inherit;
            font-size: 14px;
            background: #fff;
            color: var(--text);
            cursor: pointer;
        }

        .search-input:focus,
        .sort-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(229, 143, 168, 0.18);
        }

        .product-count {
            font-size: 15px;
            color: var(--brown);
            font-weight: 500;
            margin-bottom: 26px;
        }

        /* ===== Kartu produk ===== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 32px;
            margin-bottom: 60px;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .product-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(61, 61, 61, 0.07);
            transition: transform 0.35s, box-shadow 0.35s;
            animation: fadeUp 0.55s ease both;
            animation-delay: calc(var(--i, 0) * 70ms);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 24px 50px rgba(229, 143, 168, 0.28);
        }

        .product-image {
            position: relative;
            height: 280px;
            background: linear-gradient(135deg, #fbe3ea 0%, #f0b8c8 100%);
            overflow: hidden;
            cursor: pointer;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
            -webkit-mask-image: radial-gradient(circle, black 60%, transparent 100%);
            mask-image: radial-gradient(circle, black 60%, transparent 100%);
        }

        .product-card:hover .product-image img { transform: scale(1.1) rotate(2deg); }

        .product-cat {
            position: absolute;
            top: 16px;
            left: 16px;
            z-index: 2;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--accent-deep);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .3px;
        }

        .product-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            z-index: 2;
            padding: 6px 16px;
            border-radius: 999px;
            background: linear-gradient(135deg, #ff6b8f 0%, #ff4d7a 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 6px 16px rgba(255, 77, 122, 0.4);
        }

        .product-info {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 24px;
        }

        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .product-desc {
            flex: 1;
            margin-bottom: 20px;
            color: #6b6b6b;
            font-size: 14px;
            line-height: 1.65;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding-top: 18px;
            border-top: 2px dashed #f3e3e8;
        }

        .product-price {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--accent);
        }

        .product-buttons { display: flex; gap: 10px; }

        .btn-detail {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid var(--pink);
            color: var(--accent-deep);
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-detail:hover {
            background: var(--pink);
            color: #fff;
            transform: scale(1.1);
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 20px;
            height: 44px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--pink) 0%, var(--accent) 100%);
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(229, 143, 168, 0.45);
        }

        .btn-add.done { background: #3d3d3d; }

        /* ===== Kosong ===== */
        .empty-state { text-align: center; padding: 70px 20px 90px; }
        .empty-state i { font-size: 72px; color: var(--pink); margin-bottom: 20px; }
        .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 30px; margin-bottom: 12px; }
        .empty-state p { color: var(--brown); font-size: 17px; margin-bottom: 24px; }
        .btn-reset {
            padding: 11px 28px;
            border: 2px solid var(--pink);
            border-radius: 999px;
            background: transparent;
            color: var(--accent-deep);
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: all .25s;
        }
        .btn-reset:hover { background: var(--pink); color: #fff; }

        /* ===== Footer ===== */
        .footer {
            background: linear-gradient(135deg, var(--text) 0%, #2c2c2c 100%);
            color: white;
            padding: 60px 0 20px;
            margin-top: 80px;
        }

        .footer-section h4 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 600;
            color: white;
            margin-bottom: 20px;
        }

        .social-links { display: flex; gap: 15px; margin-top: 20px; }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover { background: var(--pink); transform: translateY(-3px); }

        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a { color: #ccc; text-decoration: none; transition: all 0.3s; }
        .footer-links a:hover { color: var(--pink); }

        .copyright {
            text-align: center;
            color: #999;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* ===== Responsif ===== */
        @media (max-width: 768px) {
            .hero-mochio { min-height: 58vh; padding: 60px 16px 120px; }
            .hero-inner h1 { font-size: 40px; }
            .hero-inner p { font-size: 16px; margin-bottom: 28px; }

            .shop-toolbar { margin-top: -56px; padding: 16px; border-radius: 20px; }
            .toolbar-right { order: -1; width: 100%; margin-left: 0; }
            .search-wrap { flex: 1; min-width: 0; }
            .search-input { width: 100%; }
            .chips { flex-wrap: nowrap; overflow-x: auto; width: 100%; padding-bottom: 4px; scrollbar-width: none; }
            .chips::-webkit-scrollbar { display: none; }

            .product-grid { grid-template-columns: 1fr; gap: 26px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php $activeNav = 'produk'; include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Hero -->
    <section class="hero-mochio" id="home">
        <div class="hero-gradient"></div>
        <div class="hero-inner">
            <h1>Menu Mochi Kami</h1>
            <p>
                Pilih dari berbagai varian mochi premium kami.
                Dibuat fresh setiap hari dengan bahan terbaik.
            </p>
            <a href="#menu" class="hero-btn">Lihat Menu</a>
        </div>
    </section>

    <div class="container" id="menu">

        <!-- Panel filter -->
        <div class="shop-toolbar">
            <div class="chips" role="group" aria-label="Filter kategori">
                <button type="button" class="filter-btn active" data-filter="all">Semua <span class="n"><?php echo count($products); ?></span></button>
                <?php foreach ($categoryCounts as $cat => $n): ?>
                    <button type="button" class="filter-btn" data-filter="<?php echo $cat; ?>"><?php echo $cat; ?> <span class="n"><?php echo $n; ?></span></button>
                <?php endforeach; ?>
            </div>

            <div class="toolbar-right">
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Cari mochi favoritmu..." autocomplete="off">
                </div>
                <select id="sortSelect" class="sort-select" aria-label="Urutkan">
                    <option value="default">Urutkan</option>
                    <option value="name-asc">Nama A - Z</option>
                    <option value="name-desc">Nama Z - A</option>
                    <option value="price-asc">Harga terendah</option>
                    <option value="price-desc">Harga tertinggi</option>
                </select>
            </div>
        </div>

        <div class="product-count" id="productCount"><?php echo count($products); ?> produk ditemukan</div>

        <!-- Daftar produk -->
        <div class="product-grid" id="productGrid">
            <?php $n = 0; foreach ($products as $id => $product): ?>
            <article class="product-card"
                     style="--i: <?php echo $n++; ?>"
                     data-category="<?php echo $product['category']; ?>"
                     data-name="<?php echo strtolower($product['name']); ?>"
                     data-desc="<?php echo strtolower($product['desc']); ?>"
                     data-price="<?php echo $product['price_num']; ?>">
                <div class="product-image" onclick="window.location.href='detail.php?id=<?php echo $id; ?>'">
                    <span class="product-cat"><?php echo $product['category']; ?></span>
                    <?php if ($product['badge']): ?>
                        <span class="product-badge"><?php echo $product['badge']; ?></span>
                    <?php endif; ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23f0b8c8\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'white\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' font-size=\'20\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?php echo $product['name']; ?></h3>
                    <p class="product-desc"><?php echo $product['desc']; ?></p>
                    <div class="product-footer">
                        <span class="product-price"><?php echo $product['price']; ?></span>
                        <div class="product-buttons">
                            <a href="detail.php?id=<?php echo $id; ?>" class="btn-detail" aria-label="Lihat detail <?php echo $product['name']; ?>">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn-add" onclick="addToCart(<?php echo $id; ?>, this)">
                                <i class="fas fa-cart-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Kosong (tersembunyi) -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <i class="fas fa-magnifying-glass"></i>
            <h3>Produk Tidak Ditemukan</h3>
            <p>Coba kata kunci lain atau ubah filter pencarian.</p>
            <button type="button" class="btn-reset" onclick="resetFilters()">Tampilkan semua</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h4>MochiLicious</h4>
                        <p>Mochi premium homemade dengan cita rasa autentik Jepang.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://wa.me/6212345678910"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h4>Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="index.php">Beranda</a></li>
                            <li><a href="produk.php">Produk</a></li>
                            <li><a href="tentang.php">Tentang</a></li>
                            <li><a href="cart.php">Keranjang</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h4>Kontak</h4>
                        <ul class="footer-links">
                            <li><i class="fas fa-phone"></i> +62 123-4567-8910</li>
                            <li><i class="fas fa-envelope"></i> info@mochilicious.com</li>
                            <li><i class="fas fa-map-marker-alt"></i> Jepara, Indonesia</li>
                        </ul>
                    </div>
                </div>
            </div>
            <p class="copyright">&copy; 2026 MochiLicious. Made with <i class="fas fa-heart text-danger"></i> in Jepara</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentFilter = 'all';
        let currentSearch = '';
        let currentSort = 'default';

        const searchInputEl = document.getElementById('searchInput');
        const sortSelectEl = document.getElementById('sortSelect');

        // Tombol kategori
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                filterProducts();
            });
        });

        // Kolom cari
        searchInputEl.addEventListener('input', function () {
            currentSearch = this.value.trim().toLowerCase();
            filterProducts();
        });

        // Urutkan
        sortSelectEl.addEventListener('change', function () {
            currentSort = this.value;
            filterProducts();
        });

        // Kata kunci dari kolom cari di navbar (produk.php?q=...)
        (function () {
            const q = new URLSearchParams(window.location.search).get('q');
            if (q && q.trim()) {
                searchInputEl.value = q.trim();
                currentSearch = q.trim().toLowerCase();
                filterProducts();
                document.getElementById('menu').scrollIntoView();
            }
        })();

        function resetFilters() {
            currentFilter = 'all';
            currentSearch = '';
            currentSort = 'default';
            searchInputEl.value = '';
            sortSelectEl.value = 'default';
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === 'all'));
            filterProducts();
        }

        function filterProducts() {
            const cards = Array.from(document.querySelectorAll('.product-card'));
            let visibleCount = 0;

            cards.forEach(card => {
                const matchFilter = currentFilter === 'all' || card.dataset.category === currentFilter;
                const matchSearch = card.dataset.name.includes(currentSearch) || card.dataset.desc.includes(currentSearch);

                if (matchFilter && matchSearch) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Urutkan kartu yang tampil
            if (currentSort !== 'default') {
                const grid = document.getElementById('productGrid');
                cards.filter(card => card.style.display !== 'none').sort((a, b) => {
                    if (currentSort === 'name-asc')   return a.dataset.name.localeCompare(b.dataset.name);
                    if (currentSort === 'name-desc')  return b.dataset.name.localeCompare(a.dataset.name);
                    if (currentSort === 'price-asc')  return parseInt(a.dataset.price) - parseInt(b.dataset.price);
                    if (currentSort === 'price-desc') return parseInt(b.dataset.price) - parseInt(a.dataset.price);
                    return 0;
                }).forEach(card => grid.appendChild(card));
            }

            document.getElementById('productCount').textContent = `${visibleCount} produk ditemukan`;

            const emptyEl = document.getElementById('emptyState');
            if (emptyEl) emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';
            document.getElementById('productGrid').style.display = visibleCount === 0 ? 'none' : '';
        }

        function addToCart(productId, btn) {
            fetch('cart.php?action=add&id=' + productId, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.cart-badge .badge').textContent = data.cart_count;
                    showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');

                    if (btn) {
                        const old = btn.innerHTML;
                        btn.classList.add('done');
                        btn.innerHTML = '<i class="fas fa-check"></i> Ditambah';
                        setTimeout(() => { btn.classList.remove('done'); btn.innerHTML = old; }, 1400);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
        }

        function showNotification(message, type) {
            const existing = document.querySelector('.notification');
            if (existing) existing.remove();

            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 90px;
                right: 20px;
                background: ${type === 'success' ? '#e58fa8' : '#ff4757'};
                color: white;
                padding: 15px 25px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                z-index: 2000;
                font-weight: 600;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

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