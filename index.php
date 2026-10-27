<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

$products = require __DIR__ . '/products.php';
$e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MochiLicious - Mochi Premium Homemade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink: #f0b8c8;
            --pink-dark: #e58fa8;
            --pink-light: #f8d8e0;
            --mochi: #f8f5f0;
            --brown: #8b6f5c;
            --text: #3d3d3d;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        section[id] { scroll-margin-top: 72px; }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--mochi);
            overflow-x: hidden;
        }

        /* ---------- Hero ---------- */
        .hero-mochio {
            position: relative;
            min-height: calc(100vh - 72px);
            padding: 70px 0;
            background: url("images/mochilanscape.jpg") center / cover no-repeat;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .hero-gradient {
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at center, rgba(255,255,255,.35) 0%, rgba(255,255,255,.6) 45%, rgba(255,255,255,.96) 100%);
        }
        .hero-inner { position: relative; z-index: 2; max-width: 720px; padding: 0 20px; text-align: center; }
        .hero-inner h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(40px, 6vw, 64px); font-weight: 900; line-height: 1.1;
            color: var(--text); margin-bottom: 18px;
        }
        .hero-inner p { font-size: clamp(16px, 2vw, 19px); line-height: 1.7; color: #5f5f5f; margin-bottom: 34px; }
        .hero-btn, .btn-highlight {
            display: inline-block; padding: 15px 40px; border-radius: 999px;
            color: #fff; font-weight: 600; text-decoration: none;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            box-shadow: 0 10px 24px rgba(229,143,168,.4);
            transition: transform .25s, box-shadow .25s;
        }
        .hero-btn:hover, .btn-highlight:hover { color: #fff; transform: translateY(-3px); box-shadow: 0 16px 32px rgba(229,143,168,.5); }

        /* ---------- Features ---------- */
        .features { padding: 70px 0; background: #fff; }
        .feature-card {
            height: 100%; text-align: center; padding: 40px 30px;
            background: var(--mochi); border: 2px solid transparent; border-radius: 25px;
            transition: transform .4s, border-color .4s;
        }
        .feature-card:hover { transform: translateY(-10px); border-color: var(--pink); }
        .feature-card i { font-size: 44px; color: var(--pink-dark); margin-bottom: 20px; }
        .feature-card h3 { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; margin-bottom: 12px; }
        .feature-card p { color: var(--brown); line-height: 1.7; margin: 0; }

        /* ---------- Highlight + slider ---------- */
        .mochi-highlight { padding: 90px 0; background: #fff7f9; }
        .highlight-wrap { display: flex; align-items: center; gap: 60px; }
        .highlight-left, .highlight-right { flex: 1; min-width: 0; }
        .highlight-tag { color: var(--pink-dark); font-weight: 600; letter-spacing: 2px; font-size: 13px; }
        .highlight-left h2 { font-family: 'Playfair Display', serif; font-size: clamp(30px, 4vw, 44px); font-weight: 700; line-height: 1.2; margin: 14px 0; }
        .highlight-left p { color: #666; line-height: 1.8; margin-bottom: 28px; max-width: 500px; }
        .btn-highlight { padding: 14px 32px; }

        .slider {
            position: relative; overflow: hidden; aspect-ratio: 1 / 1; max-height: 440px; margin: 0 auto;
            border-radius: 28px; background: linear-gradient(135deg, var(--pink-light), var(--pink));
            box-shadow: 0 24px 50px rgba(240,184,200,.4);
        }
        .slider img { position: absolute; inset: 0; width: 100%; height: 100%; padding: 24px; object-fit: contain; opacity: 0; transition: opacity .6s ease; }
        .slider img.active { opacity: 1; }
        .slider-btn {
            position: absolute; top: 50%; transform: translateY(-50%); width: 42px; height: 42px;
            border: 0; border-radius: 50%; display: grid; place-items: center;
            font-size: 16px; color: #d9798f; background: rgba(255,255,255,.85); cursor: pointer;
            transition: background .2s, color .2s;
        }
        .slider-btn:hover { background: var(--pink-dark); color: #fff; }
        .slider-btn.prev { left: 14px; }
        .slider-btn.next { right: 14px; }
        .dots { display: flex; justify-content: center; gap: 8px; margin-top: 18px; }
        .dot { width: 9px; height: 9px; padding: 0; border: 0; border-radius: 999px; background: #ecd2da; cursor: pointer; transition: width .3s, background .3s; }
        .dot.active { width: 26px; background: var(--pink-dark); }

        /* ---------- Products ---------- */
        .products { padding: 100px 0; background: linear-gradient(180deg, #fff 0%, var(--mochi) 100%); }
        .section-title { font-family: 'Playfair Display', serif; font-size: clamp(34px, 5vw, 52px); font-weight: 900; text-align: center; margin-bottom: 14px; }
        .section-subtitle { font-size: 18px; color: var(--brown); text-align: center; margin-bottom: 10px; }
        .product-count { text-align: center; font-size: 15px; font-weight: 500; margin-bottom: 20px; color: #8a8a8a; }

        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 36px; margin-top: 50px; }
        .product-link { display: block; color: inherit; text-decoration: none; }
        .product-link:hover { color: inherit; text-decoration: none; }
        .product-card {
            height: 100%; display: flex; flex-direction: column; overflow: hidden;
            background: #fff; border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,.08);
            transition: transform .4s, box-shadow .4s;
        }
        .product-card:hover { transform: translateY(-12px); box-shadow: 0 20px 60px rgba(240,184,200,.35); }
        .product-image {
            position: relative; height: 280px; overflow: hidden;
            background: linear-gradient(135deg, var(--pink-light), var(--pink));
            display: flex; align-items: center; justify-content: center;
        }
        .product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
        .product-card:hover .product-image img { transform: scale(1.08); }
        .product-fallback { display: none; font-size: 64px; color: #fff; opacity: .8; }
        .product-badge {
            position: absolute; top: 18px; right: 18px; z-index: 3; padding: 7px 18px; border-radius: 50px;
            font-size: 13px; font-weight: 600; color: #fff;
            background: linear-gradient(135deg, #ff6b8f, #ff4d7a); box-shadow: 0 5px 15px rgba(255,77,122,.4);
        }
        .product-info { flex: 1; display: flex; flex-direction: column; padding: 26px 28px 28px; }
        .product-name { font-family: 'Playfair Display', serif; font-size: 23px; font-weight: 700; margin-bottom: 8px; }
        .product-desc { flex: 1; color: #666; line-height: 1.6; margin-bottom: 18px; }
        .product-footer { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding-top: 18px; border-top: 2px solid var(--mochi); }
        .product-price { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: var(--pink-dark); white-space: nowrap; }
        .btn-add {
            padding: 11px 20px; border: 0; border-radius: 50px; cursor: pointer; white-space: nowrap;
            font-weight: 600; font-size: 14px; color: #fff;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            transition: transform .3s, box-shadow .3s;
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(240,184,200,.5); }

        /* ---------- About ---------- */
        .about { padding: 100px 0; background: #fff; }
        .about h2, .contact h2 { font-family: 'Playfair Display', serif; font-size: clamp(32px, 4.5vw, 46px); font-weight: 700; margin-bottom: 24px; }
        .about p { font-size: 17px; line-height: 1.85; color: var(--brown); margin-bottom: 18px; }
        .about-stats { display: flex; flex-wrap: wrap; gap: 40px; margin-top: 36px; }
        .stat h3 { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 700; color: var(--pink-dark); margin-bottom: 4px; }
        .stat p { font-size: 15px; font-weight: 500; margin: 0; }
        .mochi-stack { position: relative; height: 400px; }
        .mochi-item {
            position: absolute; width: 150px; height: 150px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--pink-light), var(--pink));
            box-shadow: 0 15px 35px rgba(240,184,200,.3); animation: float 6s ease-in-out infinite;
        }
        .mochi-item img { width: 100px; height: 100px; object-fit: contain; }
        .mochi-1 { top: 0; left: 50%; margin-left: -75px; }
        .mochi-2 { top: 120px; left: 12%; animation-delay: 2s; }
        .mochi-3 { top: 220px; right: 12%; animation-delay: 4s; }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0); } 50% { transform: translateY(-18px) rotate(5deg); } }

        /* ---------- Contact ---------- */
        .contact { padding: 100px 0; background: linear-gradient(135deg, var(--mochi), var(--pink-light)); }
        .contact p.lead-text { font-size: 18px; color: var(--brown); margin-bottom: 46px; }
        .contact-cards { display: flex; gap: 26px; justify-content: center; }
        .contact-card {
            flex: 1; max-width: 300px; padding: 38px 28px; text-align: center;
            background: #fff; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,.08);
            transition: transform .3s, box-shadow .3s;
        }
        .contact-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,.14); }
        .contact-card i { font-size: 44px; color: var(--pink-dark); margin-bottom: 18px; }
        .contact-card h4 { font-family: 'Playfair Display', serif; font-size: 23px; font-weight: 600; margin-bottom: 8px; }
        .contact-card p { color: var(--brown); margin-bottom: 22px; font-size: 15px; word-break: break-word; }
        .btn-contact {
            display: inline-block; padding: 11px 28px; border-radius: 25px; color: #fff; font-weight: 600; text-decoration: none;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark)); transition: transform .3s, box-shadow .3s;
        }
        .btn-contact:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(240,184,200,.5); }

        /* ---------- Footer ---------- */
        .footer { margin-top: 0; padding: 80px 0 20px; color: #fff; background: linear-gradient(135deg, var(--text), #2c2c2c); }
        .newsletter-section { margin-bottom: 60px; padding: 40px; border-radius: 20px; background: rgba(240,184,200,.1); border: 1px solid rgba(240,184,200,.2); }
        .newsletter-section h3 { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; margin-bottom: 10px; }
        .newsletter-section p { color: #ccc; margin: 0; }
        .newsletter-form .form-control { padding: 14px 24px; border: 2px solid var(--pink); border-right: 0; border-radius: 50px 0 0 50px; }
        .newsletter-form .form-control:focus { box-shadow: 0 0 0 .2rem rgba(240,184,200,.25); border-color: var(--pink); }
        .btn-newsletter {
            padding: 14px 28px; border: 2px solid var(--pink); border-radius: 0 50px 50px 0;
            font-weight: 600; color: #fff; background: linear-gradient(135deg, var(--pink), var(--pink-dark)); transition: box-shadow .3s;
        }
        .btn-newsletter:hover { color: #fff; box-shadow: 0 8px 25px rgba(240,184,200,.4); }
        .footer-section h4 { position: relative; font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 600; margin-bottom: 22px; }
        .footer-section h4::after { content: ''; position: absolute; left: 0; bottom: -6px; width: 30px; height: 2px; border-radius: 2px; background: var(--pink); }
        .footer-section { margin-bottom: 30px; }
        .footer-section p { color: #ccc; line-height: 1.7; }
        .social-links { display: flex; gap: 12px; margin-top: 20px; }
        .social-links a {
            width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff; text-decoration: none; background: rgba(255,255,255,.1); transition: background .3s, transform .3s;
        }
        .social-links a:hover { background: var(--pink-dark); transform: translateY(-3px); }
        .footer-links, .contact-info { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a { display: inline-block; color: #ccc; text-decoration: none; transition: color .3s, transform .3s; }
        .footer-links a:hover { color: var(--pink); transform: translateX(5px); }
        .contact-info li { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; color: #ccc; }
        .contact-info i { width: 16px; margin-top: 5px; color: var(--pink); }
        .featured-item { display: flex; align-items: center; gap: 12px; padding: 9px 0; border-bottom: 1px solid rgba(255,255,255,.1); }
        .featured-item:last-child { border-bottom: 0; }
        .featured-item img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; background: var(--pink-light); }
        .featured-item span { font-size: 14px; font-weight: 500; color: #ccc; }
        .footer-divider { margin: 30px 0 0; border-color: rgba(255,255,255,.1); }
        .footer-bottom { padding-top: 20px; }
        .copyright { margin: 0; font-size: 14px; color: #999; }
        .footer-bottom-links { display: flex; gap: 20px; justify-content: flex-end; }
        .footer-bottom-links a { font-size: 14px; color: #999; text-decoration: none; transition: color .3s; }
        .footer-bottom-links a:hover { color: var(--pink); }

        /* ---------- Popup Pengumuman ---------- */
#demoModal .modal-content {
    border: 0; border-radius: 25px; overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,.25);
}
#demoModal .modal-top {
    padding: 34px 30px 20px; text-align: center;
    background: linear-gradient(135deg, var(--pink-light), var(--pink));
}
#demoModal .modal-top i { font-size: 46px; color: #fff; margin-bottom: 10px; }
#demoModal .modal-top h5 {
    font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700;
    color: var(--text); margin: 0;
}
#demoModal .modal-body { padding: 26px 30px 10px; text-align: center; color: var(--brown); line-height: 1.7; }
#demoModal .modal-body ul { text-align: left; display: inline-block; margin: 12px 0 0; padding-left: 20px; }
#demoModal .modal-footer { border: 0; justify-content: center; padding: 10px 30px 30px; }
        /* ---------- Notifikasi ---------- */
        .notification {
            position: fixed; top: 90px; right: 20px; z-index: 2000; max-width: calc(100vw - 40px);
            padding: 14px 24px; border-radius: 12px; font-weight: 600; color: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,.2); animation: slideIn .3s ease;
        }
        .notification.success { background: var(--pink-dark); }
        .notification.error { background: #ff4757; }
        .notification.hide { animation: slideOut .3s ease forwards; }
        @keyframes slideIn { from { transform: translateX(110%); opacity: 0; } to { transform: none; opacity: 1; } }
        @keyframes slideOut { from { transform: none; opacity: 1; } to { transform: translateX(110%); opacity: 0; } }

        /* ---------- Responsive ---------- */
        @media (max-width: 991px) {
            .mochi-stack { height: 360px; }
            .contact-cards { flex-wrap: wrap; }
        }
        @media (max-width: 768px) {
            .features, .about, .contact { padding: 70px 0; }
            .products { padding: 70px 0; }
            .mochi-highlight { padding: 70px 0; }
            .highlight-wrap { flex-direction: column; text-align: center; gap: 36px; }
            .highlight-left p { margin-left: auto; margin-right: auto; }
            .slider { max-width: 340px; }
            .product-grid { grid-template-columns: 1fr; gap: 28px; }
            .about-content { text-align: center; }
            .about-stats { justify-content: center; gap: 28px; }
            .mochi-stack { height: 320px; margin-top: 30px; }
            .mochi-item { width: 120px; height: 120px; }
            .mochi-item img { width: 80px; height: 80px; }
            .mochi-1 { margin-left: -60px; }
            .contact-cards { flex-direction: column; align-items: center; }
            .contact-card { width: 100%; max-width: 100%; }
            .newsletter-section { padding: 28px 20px; margin-bottom: 40px; }
            .newsletter-section h3 { font-size: 24px; }
            .newsletter-form { margin-top: 20px; }
            .footer { padding: 60px 0 20px; }
            .footer-bottom-links { justify-content: center; margin-top: 12px; }
            .copyright { text-align: center; }
        }
        @media (max-width: 480px) {
            .product-footer { flex-direction: column; align-items: stretch; text-align: center; }
            .btn-add { width: 100%; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php $activeNav = 'home'; include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Hero -->
    <section class="hero-mochio" id="home">
        <div class="hero-gradient"></div>
        <div class="hero-inner">
            <h1>Mochi Premium</h1>
            <p>Kelembutan mochi homemade dengan isian premium pilihan. Dibuat fresh setiap hari dengan bahan terbaik.</p>
            <a href="#products" class="hero-btn">Lihat Menu</a>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-leaf"></i>
                        <h3>Bahan Premium</h3>
                        <p>Menggunakan bahan-bahan berkualitas tinggi dan alami untuk cita rasa terbaik.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-heart"></i>
                        <h3>Dibuat dengan Cinta</h3>
                        <p>Setiap mochi dibuat secara handmade dengan perhatian khusus pada detail.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-shipping-fast"></i>
                        <h3>Pengiriman Cepat</h3>
                        <p>Pesanan Anda akan sampai dalam kondisi segar dan siap disantap.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Highlight -->
    <section class="mochi-highlight">
        <div class="container highlight-wrap">
            <div class="highlight-left">
                <span class="highlight-tag">PRODUK MOCHI KAMI</span>
                <h2>Healthy &amp; Certified Mochi</h2>
                <p>Produk dari MochiLicious telah bersertifikat BPOM &amp; Halal. Terjamin kesehatannya dan aman dikonsumsi oleh semua kalangan.</p>
                <a href="#products" class="btn-highlight">Lihat semua Mochi</a>
            </div>

            <div class="highlight-right">
                <div class="slider">
                    <img src="images/mochi_matcha.png" alt="Mochi Matcha" class="active">
                    <img src="images/mochi_strawberry.png" alt="Mochi Strawberry">
                    <img src="images/mochi_chocolate.png" alt="Mochi Chocolate">
                    <button type="button" class="slider-btn prev" aria-label="Sebelumnya"><i class="fas fa-chevron-left"></i></button>
                    <button type="button" class="slider-btn next" aria-label="Berikutnya"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="dots">
                    <button type="button" class="dot active" aria-label="Slide 1"></button>
                    <button type="button" class="dot" aria-label="Slide 2"></button>
                    <button type="button" class="dot" aria-label="Slide 3"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section id="products" class="products">
        <div class="container">
            <h2 class="section-title">Menu Mochi Kami</h2>
            <p class="section-subtitle">Pilih varian mochi favorit Anda</p>
            <div class="product-count"><?php echo count($products); ?> produk tersedia</div>

            <div class="product-grid" id="productGrid">
                <?php foreach ($products as $id => $product): ?>
                <a href="detail.php?id=<?php echo (int)$id; ?>" class="product-link">
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['badge'])): ?>
                                <span class="product-badge"><?php echo $e($product['badge']); ?></span>
                            <?php endif; ?>
                            <img src="<?php echo $e($product['image']); ?>" alt="<?php echo $e($product['name']); ?>" loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <i class="fas fa-image product-fallback"></i>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo $e($product['name']); ?></h3>
                            <p class="product-desc"><?php echo $e($product['short']); ?></p>
                            <div class="product-footer">
                                <span class="product-price"><?php echo $e($product['price']); ?></span>
                                <button type="button" class="btn-add" onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?php echo (int)$id; ?>)">
                                    <i class="fas fa-plus"></i> Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2>Tentang MochiLicious</h2>
                        <p>MochiLicious adalah tempat di mana tradisi kuliner Jepang bertemu dengan inovasi modern. Kami memproduksi mochi premium dengan bahan-bahan berkualitas tinggi dan resep tradisional yang telah diwariskan dari generasi ke generasi.</p>
                        <p>Setiap potong mochi kami dibuat dengan penuh cinta dan perhatian terhadap detail, memastikan Anda mendapatkan pengalaman kuliner yang autentik dan memuaskan.</p>
                        <div class="about-stats">
                            <div class="stat"><h3>500+</h3><p>Pelanggan Puas</p></div>
                            <div class="stat"><h3>6</h3><p>Varian Rasa</p></div>
                            <div class="stat"><h3>100%</h3><p>Bahan Premium</p></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mochi-stack">
                        <div class="mochi-item mochi-1"><img src="images/mochi_strawberry.png" alt="Mochi Strawberry"></div>
                        <div class="mochi-item mochi-2"><img src="images/mochi_matcha.png" alt="Mochi Matcha"></div>
                        <div class="mochi-item mochi-3"><img src="images/mochi_chocolate.png" alt="Mochi Chocolate"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto text-center">
                    <h2>Hubungi Kami</h2>
                    <p class="lead-text">Siap untuk menikmati mochi premium kami? Hubungi kami sekarang untuk pemesanan atau informasi lebih lanjut.</p>
                    <div class="contact-cards">
                        <div class="contact-card">
                            <i class="fas fa-phone"></i>
                            <h4>Telepon</h4>
                            <p>+62 123-4567-8910</p>
                            <a href="tel:+6212345678910" class="btn-contact">Hubungi</a>
                        </div>
                        <div class="contact-card">
                            <i class="fab fa-whatsapp"></i>
                            <h4>WhatsApp</h4>
                            <p>Pesan langsung via WhatsApp</p>
                            <a href="https://wa.me/6212345678910" class="btn-contact">Kirim Pesan</a>
                        </div>
                        <div class="contact-card">
                            <i class="fas fa-envelope"></i>
                            <h4>Email</h4>
                            <p>info@mochilicious.com</p>
                            <a href="mailto:info@mochilicious.com" class="btn-contact">Kirim Email</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="newsletter-section">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Dapatkan Update Terbaru</h3>
                        <p>Berlangganan newsletter kami untuk mendapatkan info promo dan produk terbaru!</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="newsletter-form">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Masukkan email Anda" aria-label="Email">
                                <button class="btn btn-newsletter" type="button"><i class="fas fa-paper-plane"></i> Subscribe</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h4>MochiLicious</h4>
                        <p>Mochi premium homemade dengan cita rasa autentik Jepang. Dibuat dengan bahan berkualitas dan penuh cinta untuk setiap gigitan.</p>
                        <div class="social-links">
                            <a href="#" title="Instagram" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" title="Facebook" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://wa.me/6212345678910" title="WhatsApp" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" title="TikTok" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h4>Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="#home">Beranda</a></li>
                            <li><a href="#products">Menu Mochi</a></li>
                            <li><a href="#about">Tentang Kami</a></li>
                            <li><a href="#contact">Kontak</a></li>
                            <li><a href="cart.php">Keranjang</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h4>Kontak Kami</h4>
                        <ul class="contact-info">
                            <li><i class="fas fa-phone"></i> +62 123-4567-8910</li>
                            <li><i class="fas fa-envelope"></i> info@mochilicious.com</li>
                            <li><i class="fas fa-map-marker-alt"></i> Jepara, Indonesia</li>
                            <li><i class="fas fa-clock"></i> Senin - Sabtu: 08:00 - 17:00</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h4>Produk Unggulan</h4>
                        <div class="featured-products">
                            <div class="featured-item"><img src="images/mochi_strawberry.png" alt="Mochi Strawberry"><span>Mochi Strawberry</span></div>
                            <div class="featured-item"><img src="images/mochi_matcha.png" alt="Mochi Matcha"><span>Mochi Matcha</span></div>
                            <div class="featured-item"><img src="images/mochi_chocolate.png" alt="Mochi Chocolate"><span>Mochi Chocolate</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright">&copy; 2026 MochiLicious. All rights reserved. Made with <i class="fas fa-heart text-danger"></i> in Jepara</p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-bottom-links">
                            <a href="#">Privacy Policy</a>
                            <a href="#">Terms of Service</a>
                            <a href="#">FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Popup Pengumuman -->
<div class="modal fade" id="demoModal" tabindex="-1" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-top">
                <i class="fas fa-circle-info"></i>
                <h5 id="demoModalLabel">Pengumuman</h5>
            </div>
            <div class="modal-body">
                <p class="mb-0">Website ini <strong>bukan toko online sungguhan</strong>. Ini hanya <strong>tugas sekolah</strong>.</p>
                <ul>
                    <li>Semua data produk, harga, dan kontak adalah <strong>data dummy</strong></li>
                    <li>Pesanan tidak akan diproses</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-highlight border-0" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ----- Popup pengumuman saat website dibuka -----
window.addEventListener('load', function () {
    new bootstrap.Modal(document.getElementById('demoModal')).show();
});
        // ----- Keranjang -----
        function updateCartBadge(n) {
            var cart = document.querySelector('.cart-badge');
            if (!cart) return;
            var badge = cart.querySelector('.badge');
            if (n > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge';
                    cart.appendChild(badge);
                }
                badge.textContent = n;
            } else if (badge) {
                badge.remove();
            }
        }

        function showNotification(message, type) {
            var old = document.querySelector('.notification');
            if (old) old.remove();
            var box = document.createElement('div');
            box.className = 'notification ' + type;
            box.textContent = message;
            document.body.appendChild(box);
            setTimeout(function () {
                box.classList.add('hide');
                setTimeout(function () { box.remove(); }, 300);
            }, 3000);
        }

        function addToCart(productId) {
            fetch('cart.php?action=add&id=' + productId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        updateCartBadge(data.cart_count);
                        showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
                    }
                })
                .catch(function () { showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error'); });
        }

        // ----- Smooth scroll untuk link #anchor -----
        document.querySelectorAll('a[href^="#"]').forEach(function (a) {
            a.addEventListener('click', function (e) {
                var id = this.getAttribute('href');
                if (id.length < 2) return;
                var target = document.querySelector(id);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // ----- Slider -----
        (function () {
            var slides = document.querySelectorAll('.slider img');
            var dots = document.querySelectorAll('.dots .dot');
            if (!slides.length) return;
            var index = 0, timer;

            function show(i) {
                index = (i + slides.length) % slides.length;
                slides.forEach(function (img, n) { img.classList.toggle('active', n === index); });
                dots.forEach(function (d, n) { d.classList.toggle('active', n === index); });
            }
            function restart() {
                clearInterval(timer);
                timer = setInterval(function () { show(index + 1); }, 4000);
            }

            document.querySelector('.slider-btn.next').addEventListener('click', function () { show(index + 1); restart(); });
            document.querySelector('.slider-btn.prev').addEventListener('click', function () { show(index - 1); restart(); });
            dots.forEach(function (d, n) { d.addEventListener('click', function () { show(n); restart(); }); });
            restart();
        })();
    </script>
</body>
</html>