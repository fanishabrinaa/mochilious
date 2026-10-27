<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

$e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

$success = '';
$error   = '';
$empty   = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
$old     = $empty;

$subjects = [
    'Pemesanan'  => 'Pemesanan Produk',
    'Pertanyaan' => 'Pertanyaan Umum',
    'Saran'      => 'Saran & Kritik',
    'Kerjasama'  => 'Kerjasama Bisnis',
    'Lainnya'    => 'Lainnya',
];

// Pesan sukses dari redirect sebelumnya (supaya refresh tidak kirim ulang form)
if (!empty($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) {
        $old[$k] = trim((string)($_POST[$k] ?? ''));
    }

    if (in_array('', $old, true)) {
        $error = 'Semua field harus diisi!';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (!array_key_exists($old['subject'], $subjects)) {
        $error = 'Subjek yang dipilih tidak valid.';
    } else {
        // Proses pengiriman pesan (demo)
        $_SESSION['flash_success'] = 'Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.';
        header('Location: kontak.php#form-kirim');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - MochiLicious</title>
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
            --pink-text: #c9617e;
            --mochi: #f8f5f0;
            --brown: #8b6f5c;
            --text: #3d3d3d;
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; color: var(--text); background: var(--mochi); overflow-x: hidden; }

        /* ---------- Header ---------- */
        .page-header {
            position: relative; overflow: hidden; padding: 90px 0 120px; text-align: center; color: #fff;
            background: linear-gradient(135deg, var(--pink) 0%, var(--pink-dark) 100%);
        }
        .page-header::before, .page-header::after { content: ''; position: absolute; border-radius: 50%; background: rgba(255,255,255,.12); }
        .page-header::before { width: 460px; height: 460px; top: -200px; right: -120px; }
        .page-header::after { width: 280px; height: 280px; bottom: -150px; left: -80px; }
        .page-header .container { position: relative; z-index: 1; }
        .page-header h1 { font-family: 'Playfair Display', serif; font-size: clamp(36px, 6vw, 56px); font-weight: 900; margin-bottom: 12px; text-shadow: 0 4px 18px rgba(0,0,0,.08); }
        .page-header p { font-size: clamp(16px, 2.2vw, 20px); opacity: .92; margin: 0 auto; max-width: 560px; }
        .header-wave { position: absolute; left: 0; bottom: -1px; width: 100%; height: 56px; z-index: 1; display: block; }

        .contact-section { padding: 30px 0 90px; }

        /* ---------- Kartu info ---------- */
        .info-card {
            height: 100%; display: flex; flex-direction: column; align-items: center; text-align: center;
            padding: 36px 28px; background: #fff;
            border: 2px solid transparent; border-radius: 22px; box-shadow: 0 10px 30px rgba(0,0,0,.06);
            transition: transform .3s, border-color .3s, box-shadow .3s;
        }
        .info-card:hover { transform: translateY(-8px); border-color: var(--pink); box-shadow: 0 20px 50px rgba(0,0,0,.1); }
        .info-icon {
            width: 68px; height: 68px; margin: 0 auto 18px; border-radius: 50%; display: grid; place-items: center;
            font-size: 26px; color: #fff; background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            box-shadow: 0 10px 22px rgba(229,143,168,.35);
        }
        .info-card h4 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; margin-bottom: 10px; }
        .info-card p { color: var(--brown); line-height: 1.6; margin-bottom: 18px; font-size: 15px; }
        .info-link {
            margin-top: auto; display: inline-block; padding: 9px 22px; border-radius: 50px;
            font-size: 14px; font-weight: 600; color: var(--pink-text); text-decoration: none; word-break: break-word;
            background: var(--pink-light); transition: background .3s, color .3s, transform .3s;
        }
        .info-link:hover { color: #fff; background: var(--pink-dark); transform: translateY(-2px); }
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
        /* ---------- Form ---------- */
        .contact-main { margin-top: 56px; }
        #form-kirim { scroll-margin-top: 100px; }
        .contact-form-wrapper { height: 100%; background: #fff; border-radius: 28px; padding: 46px; box-shadow: 0 20px 60px rgba(0,0,0,.08); }
        .contact-form-wrapper h3 { font-family: 'Playfair Display', serif; font-size: clamp(28px, 4vw, 34px); font-weight: 700; margin-bottom: 10px; }
        .contact-form-wrapper .lead { font-size: 16px; font-weight: 400; color: var(--brown); margin-bottom: 30px; }
        .form-label { font-size: 14px; font-weight: 600; margin-bottom: 8px; }
        .form-control, .form-select {
            padding: 13px 18px; border: 2px solid #ece7e2; border-radius: 14px; font-size: 15px; background-color: #fdfbf9;
            transition: border-color .3s, box-shadow .3s, background-color .3s;
        }
        .form-control::placeholder { color: #b9aea6; }
        .form-control:focus, .form-select:focus { background-color: #fff; border-color: var(--pink-dark); box-shadow: 0 0 0 .2rem rgba(229,143,168,.2); }
        textarea.form-control { min-height: 150px; resize: vertical; }
        .btn-submit {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            padding: 14px 38px; border: 0; border-radius: 50px; font-size: 16px; font-weight: 600; color: #fff; cursor: pointer;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark)); box-shadow: 0 10px 24px rgba(229,143,168,.35);
            transition: transform .25s, box-shadow .25s, opacity .25s;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(229,143,168,.45); }
        .btn-submit:disabled { opacity: .7; cursor: not-allowed; transform: none; }
        .form-note { margin: 14px 0 0; font-size: 13px; color: #a0928a; }
        .alert { display: flex; align-items: flex-start; gap: 12px; border: 0; border-radius: 14px; padding: 16px 20px; margin-bottom: 24px; font-size: 15px; }
        .alert i { margin-top: 3px; }
        .alert-success { background: #e8f6ee; color: #1f6b42; }
        .alert-danger { background: #fdecee; color: #a62b3d; }

        /* ---------- Panel samping ---------- */
        .side-card {
            position: relative; overflow: hidden; height: 100%; display: flex; flex-direction: column;
            padding: 40px 32px; border-radius: 28px; color: #fff;
            background: linear-gradient(135deg, var(--pink) 0%, var(--pink-dark) 100%); box-shadow: 0 20px 50px rgba(229,143,168,.35);
        }
        .side-card::after { content: ''; position: absolute; width: 220px; height: 220px; right: -90px; bottom: -90px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .side-card > * { position: relative; z-index: 1; }
        .side-card h4 { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; margin-bottom: 24px; }
        .side-item { display: flex; gap: 14px; margin-bottom: 22px; }
        .side-item .ico { flex: 0 0 42px; width: 42px; height: 42px; border-radius: 50%; display: grid; place-items: center; background: rgba(255,255,255,.22); }
        .side-item strong { display: block; font-size: 15px; margin-bottom: 2px; }
        .side-item span { font-size: 14px; opacity: .95; line-height: 1.6; display: block; }
        .btn-wa {
            margin-top: auto; display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            padding: 13px 24px; border-radius: 50px; font-weight: 600; font-size: 15px; text-decoration: none;
            color: var(--pink-text); background: #fff; box-shadow: 0 10px 24px rgba(0,0,0,.1); transition: transform .25s, box-shadow .25s;
        }
        .btn-wa:hover { color: var(--pink-text); transform: translateY(-2px); box-shadow: 0 14px 30px rgba(0,0,0,.15); }
        .btn-wa i { font-size: 20px; }

        /* ---------- Peta ---------- */
        .map-title { margin-top: 56px; margin-bottom: 20px; text-align: center; font-family: 'Playfair Display', serif; font-size: clamp(26px, 4vw, 32px); font-weight: 700; }
        .map-section { border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,.1); line-height: 0; border: 4px solid #fff; }
        .map-section iframe { width: 100%; height: 420px; border: 0; }

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
        .social-links a { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; background: rgba(255,255,255,.1); transition: background .3s, transform .3s; }
        .social-links a:hover { background: var(--pink-dark); transform: translateY(-3px); }
        .copyright { margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,.1); text-align: center; font-size: 14px; color: #999; }

        @media (max-width: 768px) {
            .page-header { padding: 70px 0 100px; }
            .contact-section { padding: 20px 0 70px; }
            .contact-main { margin-top: 40px; }
            .contact-form-wrapper { padding: 30px 20px; }
            .side-card { padding: 32px 24px; }
            .map-title { margin-top: 40px; }
            .map-section iframe { height: 320px; }
            .btn-submit { width: 100%; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .info-card, .info-link, .btn-submit, .btn-wa { transition: none; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php $activeNav = 'kontak'; include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Header -->
    <section class="page-header">
        <div class="container">
            <h1>Hubungi Kami</h1>
            <p>Kami siap membantu Anda. Jangan ragu untuk menghubungi kami!</p>
        </div>
        <svg class="header-wave" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
            <path fill="#f8f5f0" d="M0,40 C240,90 480,0 720,30 C960,60 1200,90 1440,40 L1440,80 L0,80 Z"/>
        </svg>
    </section>

    <section class="contact-section">
        <div class="container">

            <!-- Kartu info -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <h4>Telepon</h4>
                        <p>Hubungi kami untuk pemesanan atau pertanyaan</p>
                        <a class="info-link" href="tel:+6212345678910">+62 123-4567-8910</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon"><i class="fab fa-whatsapp"></i></div>
                        <h4>WhatsApp</h4>
                        <p>Chat langsung dengan customer service kami</p>
                        <a class="info-link" href="https://wa.me/6212345678910" target="_blank" rel="noopener">Kirim Pesan</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <h4>Email</h4>
                        <p>Kirim email untuk pertanyaan detail</p>
                        <a class="info-link" href="mailto:info@mochilicious.com">info@mochilicious.com</a>
                    </div>
                </div>
            </div>

            <!-- Form + info toko -->
            <div class="row g-4 contact-main">
                <div class="col-lg-7">
                    <div class="contact-form-wrapper" id="form-kirim">
                        <h3>Kirim Pesan</h3>
                        <p class="lead">Isi form di bawah ini dan kami akan segera menghubungi Anda kembali.</p>

                        <?php if ($success): ?>
                            <div class="alert alert-success" role="status">
                                <i class="fas fa-check-circle"></i> <span><?php echo $e($success); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <span><?php echo $e($error); ?></span>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="#form-kirim" id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nama Anda" autocomplete="name" maxlength="100" value="<?php echo $e($old['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" autocomplete="email" maxlength="150" value="<?php echo $e($old['email']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="subject">Subjek *</label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Pilih subjek pesan</option>
                                        <?php foreach ($subjects as $val => $label): ?>
                                            <option value="<?php echo $e($val); ?>"<?php echo $old['subject'] === $val ? ' selected' : ''; ?>><?php echo $e($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="message">Pesan *</label>
                                    <textarea class="form-control" id="message" name="message" placeholder="Tulis pesan Anda di sini..." maxlength="2000" required><?php echo $e($old['message']); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-submit" id="submitBtn"><i class="fas fa-paper-plane"></i> <span>Kirim Pesan</span></button>
                                    <p class="form-note"><i class="fas fa-lock"></i> Data Anda hanya kami gunakan untuk membalas pesan ini.</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="side-card">
                        <h4>Informasi Toko</h4>
                        <div class="side-item">
                            <div class="ico"><i class="fas fa-clock"></i></div>
                            <div>
                                <strong>Jam Operasional</strong>
                                <span>Senin - Sabtu: 08:00 - 17:00 WIB</span>
                                <span>Minggu &amp; hari libur: Tutup</span>
                            </div>
                        </div>
                        <div class="side-item">
                            <div class="ico"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <strong>Alamat</strong>
                                <span>Jl. Raya Jepara No. 123, Jepara, Jawa Tengah, Indonesia</span>
                            </div>
                        </div>
                        <div class="side-item">
                            <div class="ico"><i class="fas fa-headset"></i></div>
                            <div>
                                <strong>Respon Cepat</strong>
                                <span>Pesan lewat WhatsApp biasanya dibalas lebih cepat pada jam operasional.</span>
                            </div>
                        </div>
                        <a class="btn-wa" href="https://wa.me/6212345678910" target="_blank" rel="noopener">
                            <i class="fab fa-whatsapp"></i> Chat via WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Peta -->
            <h3 class="map-title">Lokasi Kami</h3>
            <div class="map-section">
                <iframe title="Lokasi MochiLicious di Jepara" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126748.56211042916!2d110.63108!3d-6.5889!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e711f0e4e5f0f67%3A0x5027a76e355ebb0!2sJepara%2C%20Jepara%20Regency%2C%20Central%20Java!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid"></iframe>
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
    <script>
        // Cegah klik ganda saat form dikirim
        document.getElementById('contactForm').addEventListener('submit', function () {
            var btn = document.getElementById('submitBtn');
            if (this.checkValidity()) {
                btn.disabled = true;
                btn.querySelector('i').className = 'fas fa-spinner fa-spin';
                btn.querySelector('span').textContent = 'Mengirim...';
            }
        });
    </script>
</body>
</html>