<?php
/**
 * Navbar bersama untuk semua halaman.
 * Cara pakai di halaman:   <?php $activeNav = 'produk'; include __DIR__ . '/partials/navbar.php'; ?>
 * Nilai $activeNav: home | produk | tentang | kontak | (kosong)
 *
 * Gaya dan script-nya sudah ada di file ini (kelas diawali "mnav"),
 * jadi tampilannya sama di semua halaman dan tidak bentrok dengan CSS halaman.
 */
$activeNav = $activeNav ?? '';

if (!isset($cart_count)) {
    $cart_count = (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) ? array_sum($_SESSION['cart']) : 0;
}

$navLoggedIn = isset($_SESSION['user_id']);
$navName     = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Akun';
$navIsAdmin  = ($_SESSION['user_role'] ?? '') === 'admin';
$navInitial  = preg_match('/^./u', (string)$navName, $__m) ? strtoupper($__m[0]) : 'U';
$navH        = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

$navLinks = [
    'home'    => ['index.php',   'Beranda'],
    'produk'  => ['produk.php',  'Produk'],
    'tentang' => ['tentang.php', 'Tentang'],
    'kontak'  => ['kontak.php',  'Kontak'],
];
?>
<style>
.mnav, .mnav * { box-sizing: border-box; }
.mnav {
    position: sticky; top: 0; z-index: 1030;
    background: rgba(255, 255, 255, .92);
    -webkit-backdrop-filter: saturate(180%) blur(12px);
    backdrop-filter: saturate(180%) blur(12px);
    border-bottom: 1px solid transparent;
    transition: box-shadow .25s ease, border-color .25s ease;
    font-family: 'Poppins', sans-serif;
}
.mnav.scrolled { box-shadow: 0 8px 28px rgba(232, 168, 184, .28); border-bottom-color: rgba(240, 184, 200, .4); }
.mnav a { text-decoration: none; }
.mnav a:focus-visible, .mnav button:focus-visible { outline: 3px solid rgba(229, 143, 168, .55); outline-offset: 2px; }
.mnav-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; height: 72px; display: flex; align-items: center; gap: 18px; }

/* Brand */
.mnav-brand { display: flex; align-items: center; gap: 10px; font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 900; letter-spacing: -.5px; color: #3d3d3d; white-space: nowrap; }
.mnav-brand em { font-style: normal; color: #e58fa8; }
.mnav-brand:hover { color: #3d3d3d; }
.mnav-logo { width: 38px; height: 38px; border-radius: 50%; display: grid; place-items: center; font-size: 16px; color: #fff; background: linear-gradient(135deg, #f8d8e0, #e58fa8); box-shadow: 0 6px 14px rgba(229, 143, 168, .4); }

/* Menu */
.mnav-panel { display: flex; align-items: center; justify-content: center; flex: 1; gap: 6px; min-width: 0; }
.mnav-link { position: relative; padding: 9px 18px; border-radius: 999px; font-size: 15px; font-weight: 500; color: #3d3d3d; transition: background .2s, color .2s; }
.mnav-link:hover { background: #fdeef2; color: #d9798f; }
.mnav-link.active { background: #fdeef2; color: #d9798f; font-weight: 600; }

/* Tombol kanan */
.mnav-actions { display: flex; align-items: center; gap: 10px; }
.mnav-icon-btn { position: relative; width: 44px; height: 44px; border: 0; border-radius: 50%; display: grid; place-items: center; font-size: 17px; color: #d9798f; background: #fdeef2; cursor: pointer; transition: background .2s, color .2s, transform .2s; }
.mnav-icon-btn:hover { background: #e58fa8; color: #fff; transform: translateY(-2px); }
.mnav .cart-badge .badge { position: absolute; top: -4px; right: -4px; width: auto; min-width: 20px; height: 20px; padding: 0 5px; border: 2px solid #fff; border-radius: 999px; display: flex; align-items: center; justify-content: center; background: #d9456f; color: #fff; font-size: 11px; font-weight: 700; line-height: 1; }
.mnav-login { display: inline-flex; align-items: center; gap: 8px; height: 44px; padding: 0 20px; border-radius: 999px; font-size: 14px; font-weight: 600; color: #fff; background: linear-gradient(135deg, #f0b8c8, #e58fa8); box-shadow: 0 8px 18px rgba(229, 143, 168, .35); transition: transform .2s, box-shadow .2s; }
.mnav-login:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 12px 24px rgba(229, 143, 168, .45); }

/* Menu akun */
.mnav-user { position: relative; }
.mnav-user-btn { display: flex; align-items: center; gap: 10px; height: 44px; padding: 0 14px 0 5px; border: 1.5px solid #f3d3dc; border-radius: 999px; background: #fff; color: #3d3d3d; font: 500 14px 'Poppins', sans-serif; cursor: pointer; transition: border-color .2s; }
.mnav-user-btn:hover, .mnav-user.open .mnav-user-btn { border-color: #e58fa8; }
.mnav-avatar { width: 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; font-weight: 700; color: #fff; background: linear-gradient(135deg, #f0b8c8, #e58fa8); }
.mnav-user-name { max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mnav-user-btn .fa-chevron-down { font-size: 10px; color: #b9a3aa; transition: transform .2s; }
.mnav-user.open .fa-chevron-down { transform: rotate(180deg); }
.mnav-menu { position: absolute; right: 0; top: calc(100% + 10px); min-width: 220px; padding: 8px; border-radius: 16px; background: #fff; box-shadow: 0 18px 50px rgba(61, 61, 61, .18); opacity: 0; visibility: hidden; transform: translateY(-6px); transition: opacity .2s, transform .2s, visibility .2s; }
.mnav-user.open .mnav-menu { opacity: 1; visibility: visible; transform: none; }
.mnav-menu-head { padding: 8px 12px 10px; font-size: 12px; color: #b9a3aa; }
.mnav-menu-head strong { display: block; font-size: 14px; color: #3d3d3d; }
.mnav-menu a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 14px; color: #3d3d3d; transition: background .15s, color .15s; }
.mnav-menu a i { width: 16px; text-align: center; color: #e58fa8; }
.mnav-menu a:hover { background: #fdeef2; color: #d9798f; }
.mnav-menu hr { height: 1px; margin: 6px 4px; border: 0; background: #f5e3e8; opacity: 1; }
.mnav-menu a.danger:hover { background: #fff0f0; color: #d63031; }
.mnav-menu a.danger:hover i { color: #d63031; }

/* Hamburger (HP) */
.mnav-burger { display: none; position: relative; width: 44px; height: 44px; border: 0; border-radius: 50%; background: #fdeef2; cursor: pointer; }
.mnav-burger span { position: absolute; left: 12px; right: 12px; height: 2px; border-radius: 2px; background: #d9798f; transition: transform .25s, opacity .2s, top .25s; }
.mnav-burger span:nth-child(1) { top: 15px; }
.mnav-burger span:nth-child(2) { top: 21px; }
.mnav-burger span:nth-child(3) { top: 27px; }
.mnav.open .mnav-burger span:nth-child(1) { top: 21px; transform: rotate(45deg); }
.mnav.open .mnav-burger span:nth-child(2) { opacity: 0; }
.mnav.open .mnav-burger span:nth-child(3) { top: 21px; transform: rotate(-45deg); }

@media (max-width: 991px) {
    .mnav-inner { gap: 10px; }
    .mnav-brand { margin-right: auto; }
    .mnav-burger { display: block; }
    .mnav-panel { display: none; position: absolute; left: 0; right: 0; top: 100%; flex-direction: column; align-items: stretch; justify-content: flex-start; gap: 4px; padding: 12px 20px 20px; background: #fff; border-bottom: 1px solid #f3d3dc; box-shadow: 0 18px 30px rgba(61, 61, 61, .08); }
    .mnav.open .mnav-panel { display: flex; }
    .mnav-link { padding: 13px 18px; font-size: 16px; border-radius: 14px; }
}
@media (max-width: 575px) {
    .mnav-inner { height: 64px; padding: 0 14px; }
    .mnav-brand { font-size: 22px; }
    .mnav-logo { width: 34px; height: 34px; font-size: 14px; }
    .mnav-login { width: 44px; padding: 0; justify-content: center; }
    .mnav-login span, .mnav-user-name, .mnav-user-btn .fa-chevron-down { display: none; }
    .mnav-user-btn { padding: 0 5px; }
}
</style>

<header class="mnav" id="mnav">
    <div class="mnav-inner">
        <a class="mnav-brand" href="index.php" aria-label="MochiLicious - Beranda">
            <span class="mnav-logo"><i class="fas fa-cookie-bite"></i></span>
            <span>Mochi<em>Licious</em></span>
        </a>

        <nav class="mnav-panel" id="mnavPanel" aria-label="Menu utama">
            <?php foreach ($navLinks as $key => [$href, $label]): ?>
                <a class="mnav-link<?php echo $activeNav === $key ? ' active' : ''; ?>" href="<?php echo $href; ?>"<?php echo $activeNav === $key ? ' aria-current="page"' : ''; ?>><?php echo $label; ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="mnav-actions">
            <a href="cart.php" class="mnav-icon-btn cart-badge" aria-label="Keranjang">
                <i class="fas fa-shopping-cart"></i>
                <?php if ((int)$cart_count > 0): ?>
                    <span class="badge"><?php echo (int)$cart_count; ?></span>
                <?php endif; ?>
            </a>

            <?php if ($navLoggedIn): ?>
                <div class="mnav-user" id="mnavUser">
                    <button type="button" class="mnav-user-btn" id="mnavUserBtn" aria-haspopup="true" aria-expanded="false">
                        <span class="mnav-avatar"><?php echo $navH($navInitial); ?></span>
                        <span class="mnav-user-name"><?php echo $navH($navName); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="mnav-menu" role="menu">
                        <div class="mnav-menu-head">Masuk sebagai<strong><?php echo $navH($navName); ?></strong></div>
                        <?php if ($navIsAdmin): ?>
                            <a href="admin/dashboard.php" role="menuitem"><i class="fas fa-tachometer-alt"></i> Dashboard Admin</a>
                        <?php endif; ?>
                        <a href="pesanan.php" role="menuitem"><i class="fas fa-receipt"></i> Pesanan Saya</a>
                        <hr>
                        <a href="logout.php" class="danger" role="menuitem"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="mnav-login"><i class="fas fa-user"></i><span>Login</span></a>
            <?php endif; ?>

            <button type="button" class="mnav-burger" id="mnavBurger" aria-label="Buka menu" aria-expanded="false" aria-controls="mnavPanel">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<script>
(function () {
    var nav = document.getElementById('mnav');
    if (!nav) return;
    var burger  = document.getElementById('mnavBurger');
    var user    = document.getElementById('mnavUser');
    var userBtn = document.getElementById('mnavUserBtn');

    function onScroll() { nav.classList.toggle('scrolled', window.scrollY > 8); }
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    burger.addEventListener('click', function () {
        var open = nav.classList.toggle('open');
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Tutup menu HP saat link diklik
    document.querySelectorAll('#mnavPanel .mnav-link').forEach(function (a) {
        a.addEventListener('click', function () {
            nav.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
        });
    });

    if (user && userBtn) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = user.classList.toggle('open');
            userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    document.addEventListener('click', function (e) {
        if (user && !user.contains(e.target)) {
            user.classList.remove('open');
            userBtn.setAttribute('aria-expanded', 'false');
        }
        if (!nav.contains(e.target)) {
            nav.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            nav.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
            if (user) { user.classList.remove('open'); userBtn.setAttribute('aria-expanded', 'false'); }
        }
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) nav.classList.remove('open');
    });
})();
</script>