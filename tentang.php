<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - MochiLicious</title>
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

        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; color: var(--text); background: var(--mochi); overflow-x: hidden; }

        .section-title {
            font-family: 'Playfair Display', serif; font-size: clamp(30px, 4.5vw, 42px); font-weight: 700;
            text-align: center; margin-bottom: 12px;
        }
        .section-sub { text-align: center; color: var(--brown); margin-bottom: 50px; }

        /* ---------- Header ---------- */
        .page-header {
            position: relative; overflow: hidden; padding: 90px 0 80px; text-align: center; color: #fff;
            background: linear-gradient(135deg, var(--pink) 0%, var(--pink-dark) 100%);
        }
        .page-header::before, .page-header::after { content: ''; position: absolute; border-radius: 50%; background: rgba(255,255,255,.12); }
        .page-header::before { width: 460px; height: 460px; top: -200px; right: -120px; }
        .page-header::after { width: 280px; height: 280px; bottom: -150px; left: -80px; }
        .page-header .container { position: relative; z-index: 1; }
        .page-header h1 { font-family: 'Playfair Display', serif; font-size: clamp(36px, 6vw, 56px); font-weight: 900; margin-bottom: 12px; }
        .page-header p { font-size: clamp(16px, 2.2vw, 20px); opacity: .92; margin: 0; }

        /* ---------- Cerita ---------- */
        .about-section { padding: 80px 0; }
        .about-content { background: #fff; border-radius: 28px; padding: 50px; box-shadow: 0 20px 60px rgba(0,0,0,.08); }
        .about-content h2 { font-family: 'Playfair Display', serif; font-size: clamp(30px, 4vw, 40px); font-weight: 700; margin-bottom: 22px; }
        .about-content p { font-size: 17px; line-height: 1.85; color: var(--brown); margin-bottom: 18px; }
        .about-content p:last-child { margin-bottom: 0; }
        .about-content strong { color: var(--text); }

        /* ---------- Statistik ---------- */
        .stats-section { padding: 60px 0; color: #fff; background: linear-gradient(135deg, var(--pink) 0%, var(--pink-dark) 100%); }
        .stat-card { text-align: center; padding: 16px; }
        .stat-card i { font-size: 38px; margin-bottom: 14px; opacity: .9; }
        .stat-card h3 { font-family: 'Playfair Display', serif; font-size: clamp(34px, 5vw, 46px); font-weight: 700; margin-bottom: 6px; }
        .stat-card p { font-size: 16px; opacity: .92; margin: 0; }

        /* ---------- Nilai ---------- */
        .values-section { padding: 90px 0; background: #fff; }
        .value-card {
            height: 100%; text-align: center; padding: 38px 28px; background: var(--mochi);
            border: 2px solid transparent; border-radius: 22px; transition: transform .3s, border-color .3s, box-shadow .3s;
        }
        .value-card:hover { transform: translateY(-8px); border-color: var(--pink); box-shadow: 0 15px 40px rgba(0,0,0,.08); }
        .value-icon {
            width: 68px; height: 68px; margin: 0 auto 20px; border-radius: 50%; display: grid; place-items: center;
            font-size: 26px; color: #fff; background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            box-shadow: 0 10px 22px rgba(229,143,168,.35);
        }
        .value-card h4 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; margin-bottom: 10px; }
        .value-card p { color: var(--brown); line-height: 1.7; margin: 0; font-size: 15px; }

        /* ---------- Timeline ---------- */
        .timeline-section { padding: 90px 0; background: var(--mochi); }
        .timeline { position: relative; max-width: 960px; margin: 0 auto; padding: 10px 0; }
        .timeline::before { content: ''; position: absolute; left: 50%; top: 0; bottom: 0; width: 4px; background: var(--pink-light); transform: translateX(-50%); border-radius: 4px; }
        .timeline-item { position: relative; margin-bottom: 36px; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-item:nth-child(odd) .timeline-content { margin-right: calc(50% + 34px); text-align: right; }
        .timeline-item:nth-child(even) .timeline-content { margin-left: calc(50% + 34px); }
        .timeline-dot {
            position: absolute; left: 50%; top: 26px; width: 22px; height: 22px; transform: translateX(-50%); z-index: 2;
            background: var(--pink-dark); border: 4px solid var(--mochi); border-radius: 50%; box-shadow: 0 4px 14px rgba(229,143,168,.5);
        }
        .timeline-content { background: #fff; padding: 26px 28px; border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,.06); }
        .timeline-year { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; color: var(--pink-dark); margin-bottom: 6px; }
        .timeline-content h5 { font-weight: 600; font-size: 17px; margin-bottom: 8px; }
        .timeline-content p { color: var(--brown); margin: 0; font-size: 15px; line-height: 1.7; }

        /* ---------- Tim ---------- */
        .team-section { padding: 90px 0; background: #fff; }
        .team-card {
            height: 100%; text-align: center; padding: 38px 28px; background: var(--mochi);
            border-radius: 22px; transition: transform .3s, box-shadow .3s;
        }
        .team-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,0,0,.08); }
        .team-avatar {
            width: 92px; height: 92px; margin: 0 auto 18px; border-radius: 50%; display: grid; place-items: center;
            font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 700; color: #fff;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark)); box-shadow: 0 12px 26px rgba(229,143,168,.4);
        }
        .team-card h5 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .team-card .position {
            display: inline-block; padding: 4px 14px; margin-bottom: 14px; border-radius: 999px;
            font-size: 13px; font-weight: 600; color: var(--pink-dark); background: #fdeef2;
        }
        .team-card p { color: var(--brown); line-height: 1.7; margin: 0; font-size: 15px; }

        /* ---------- Footer ---------- */
        .footer { padding: 70px 0 20px; color: #fff; background: linear-gradient(135deg, var(--text) 0%, #2c2c2c 100%); }
        .footer h5 { position: relative; font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 600; margin-bottom: 22px; }
        .footer h5::after { content: ''; position: absolute; left: 0; bottom: -6px; width: 30px; height: 2px; border-radius: 2px; background: var(--pink); }
        .footer p { color: #ccc; line-height: 1.7; }
        .footer-col { margin-bottom: 30px; }
        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 10px; color: #ccc; }
        .footer-links li i { width: 18px; color: var(--pink); }
        .footer-links a { display: inline-block; color: #ccc; text-decoration: none; transition: color .3s, transform .3s; }
        .footer-links a:hover { color: var(--pink); transform: translateX(5px); }
        .social-links { display: flex; gap: 12px; margin-top: 18px; }
        .social-links a {
            width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #fff; text-decoration: none; background: rgba(255,255,255,.1); transition: background .3s, transform .3s;
        }
        .social-links a:hover { background: var(--pink-dark); transform: translateY(-3px); }
        .copyright { margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,.1); text-align: center; font-size: 14px; color: #999; }

        /* ---------- Responsive ---------- */
        @media (max-width: 768px) {
            .page-header { padding: 70px 0 60px; }
            .about-section, .values-section, .timeline-section, .team-section { padding: 65px 0; }
            .about-content { padding: 30px 22px; }
            .section-sub { margin-bottom: 36px; }
            .stat-card { padding: 12px 6px; }
            .stat-card i { font-size: 30px; }

            .timeline::before { left: 16px; transform: none; }
            .timeline-dot { left: 16px; }
            .timeline-item:nth-child(odd) .timeline-content,
            .timeline-item:nth-child(even) .timeline-content { margin-left: 46px; margin-right: 0; text-align: left; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php $activeNav = 'tentang'; include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Header -->
    <section class="page-header">
        <div class="container">
            <h1>Tentang MochiLicious</h1>
            <p>Kisah di Balik Kelembutan Setiap Gigitan</p>
        </div>
    </section>

    <!-- Cerita -->
    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="about-content">
                        <h2>Cerita Kami</h2>
                        <p>MochiLicious lahir dari kecintaan mendalam terhadap mochi tradisional Jepang dan keinginan untuk berbagi kelezatan ini dengan masyarakat Indonesia. Dimulai pada tahun 2020, kami memulai perjalanan dari dapur rumah dengan tekad menciptakan mochi premium yang autentik namun terjangkau.</p>
                        <p>Setiap potong mochi kami dibuat dengan tangan menggunakan resep tradisional, dikombinasikan dengan sentuhan inovasi modern untuk menciptakan varian rasa yang unik dan memikat. Kami hanya menggunakan bahan berkualitas tinggi, mulai dari tepung ketan premium hingga isian yang dipilih dengan cermat.</p>
                        <p>Filosofi kami sederhana: <strong>kualitas tanpa kompromi, cita rasa yang autentik, dan pelayanan yang ramah</strong>. Kami percaya setiap pelanggan layak mendapatkan pengalaman kuliner yang istimewa.</p>
                        <p>Dari 6 varian rasa pilihan hingga lebih dari 500 pelanggan yang puas, kami terus berkembang sambil mempertahankan nilai yang menjadi fondasi kami: <strong>integritas, kualitas, dan dedikasi</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="stat-card"><i class="fas fa-calendar-alt"></i><h3><?php echo max(1, (int)date('Y') - 2020); ?>+</h3><p>Tahun Pengalaman</p></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card"><i class="fas fa-users"></i><h3>500+</h3><p>Pelanggan Puas</p></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card"><i class="fas fa-cookie-bite"></i><h3>6</h3><p>Varian Rasa</p></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card"><i class="fas fa-star"></i><h3>4.9</h3><p>Rating Pelanggan</p></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nilai -->
    <section class="values-section">
        <div class="container">
            <h2 class="section-title">Nilai-Nilai Kami</h2>
            <p class="section-sub">Hal-hal yang kami pegang di setiap mochi yang kami buat</p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-award"></i></div>
                        <h4>Kualitas Premium</h4>
                        <p>Kami berkomitmen menggunakan bahan-bahan berkualitas tinggi untuk setiap produk mochi kami.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-heart"></i></div>
                        <h4>Dibuat dengan Cinta</h4>
                        <p>Setiap mochi dibuat dengan tangan dan penuh perhatian untuk memberikan pengalaman terbaik.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-leaf"></i></div>
                        <h4>Bahan Alami</h4>
                        <p>Kami mengutamakan bahan-bahan alami tanpa pengawet untuk kesehatan Anda.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-smile"></i></div>
                        <h4>Kepuasan Pelanggan</h4>
                        <p>Kepuasan Anda adalah prioritas utama kami. Kami selalu siap mendengarkan masukan Anda.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-shipping-fast"></i></div>
                        <h4>Pengiriman Cepat</h4>
                        <p>Produk kami dikemas dengan baik dan dikirim dengan cepat agar tetap segar.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-lightbulb"></i></div>
                        <h4>Inovasi Berkelanjutan</h4>
                        <p>Kami terus berinovasi untuk menghadirkan varian rasa baru yang menarik.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline -->
    <section class="timeline-section">
        <div class="container">
            <h2 class="section-title">Perjalanan Kami</h2>
            <p class="section-sub">Dari dapur rumah hingga pelanggan di seluruh Indonesia</p>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2020</div>
                        <h5>Awal Perjalanan</h5>
                        <p>MochiLicious didirikan dengan visi membawa cita rasa mochi autentik ke Indonesia.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2021</div>
                        <h5>Peluncuran Produk Pertama</h5>
                        <p>Kami meluncurkan 3 varian rasa pertama: Strawberry, Chocolate, dan Matcha.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2022</div>
                        <h5>Ekspansi Produk</h5>
                        <p>Menambahkan varian Mango, Red Bean, dan Vanilla berdasarkan permintaan pelanggan.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2023</div>
                        <h5>Jangkauan Lebih Luas</h5>
                        <p>Membuka layanan pengiriman ke seluruh Indonesia dan mencapai 500+ pelanggan.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2024</div>
                        <h5>Penghargaan &amp; Pengakuan</h5>
                        <p>Menerima penghargaan sebagai Mochi Premium Terbaik dan rating 4.9 dari pelanggan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tim -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title">Tim Kami</h2>
            <p class="section-sub">Orang-orang di balik setiap gigitan mochi</p>
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="team-card">
                        <div class="team-avatar">F</div>
                        <h5>Fani Shabrina R. A.</h5>
                        <span class="position">Founder</span>
                        <p>Pendiri MochiLicious dengan passion mendalam terhadap kuliner Jepang.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="team-card">
                        <div class="team-avatar">R</div>
                        <h5>Chef Rayhanna</h5>
                        <span class="position">Head Chef</span>
                        <p>Chef berpengalaman yang ahli dalam pembuatan mochi tradisional.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="team-card">
                        <div class="team-avatar">A</div>
                        <h5>Angen Surya S</h5>
                        <span class="position">Customer Service</span>
                        <p>Siap melayani dengan ramah dan membantu kebutuhan pelanggan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-col">
                    <h5>MochiLicious</h5>
                    <p>Mochi premium homemade dengan cita rasa autentik Jepang.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://wa.me/6212345678910" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-4 footer-col">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="produk.php">Produk</a></li>
                        <li><a href="tentang.php">Tentang</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-col">
                    <h5>Kontak</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone"></i> +62 123-4567-8910</li>
                        <li><i class="fas fa-envelope"></i> info@mochilicious.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> Jepara, Indonesia</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; 2026 MochiLicious. All rights reserved. Made with <i class="fas fa-heart text-danger"></i> in Jepara
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>