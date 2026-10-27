<?php
/**
 * Data produk untuk semua halaman.
 *
 * Sumber utama : tabel `products` di database (dikelola lewat admin/products.php).
 * Cadangan     : daftar tetap di bawah, dipakai kalau database tidak bisa dihubungi,
 *                supaya etalase tetap tampil.
 *
 * Setiap produk: name, price (teks), price_num (angka), image, short, desc, badge, category.
 * Semua teks dari database sudah di-escape HTML, jadi aman di-echo langsung.
 */
require_once __DIR__ . '/config.php';

if (!function_exists('mochiFallbackProducts')) {

    function mochiFallbackProducts(): array
    {
        return [
        1 => ['name' => 'Mochi Strawberry', 'price' => '35K', 'price_num' => 35000, 'image' => 'images/mochi_strawberry.png', 'short' => 'Mochi dengan isian selai stroberi segar, dilapisi tepung ketan premium.', 'desc' => 'Mochi dengan isian selai stroberi segar, dilapisi tepung ketan premium. Rasanya manis dan segar dengan tekstur mochi yang lembut.', 'badge' => 'Best Seller', 'category' => 'Fruit'],
        2 => ['name' => 'Mochi Chocolate', 'price' => '38K', 'price_num' => 38000, 'image' => 'images/mochi_chocolate.png', 'short' => 'Mochi dengan coklat premium dan kacang almond, kombinasi sempurna.', 'desc' => 'Mochi dengan coklat premium dan kacang almond, kombinasi sempurna antara manisnya coklat dan renyahnya kacang.', 'badge' => '', 'category' => 'Classic'],
        3 => ['name' => 'Mochi Matcha', 'price' => '42K', 'price_num' => 42000, 'image' => 'images/mochi_matcha.png', 'short' => 'Mochi dengan matcha premium dari Jepang, cita rasa autentik.', 'desc' => 'Mochi dengan matcha premium dari Jepang, cita rasa autentik dengan aroma teh hijau yang khas dan manis alami.', 'badge' => '', 'category' => 'Classic'],
        4 => ['name' => 'Mochi Mango', 'price' => '42K', 'price_num' => 42000, 'image' => 'images/mochi_mango.png', 'short' => 'Mochi dengan mangga manis segar, rasa tropis yang menyegarkan.', 'desc' => 'Mochi dengan mangga manis segar, rasa tropis yang menyegarkan dengan kombinasi manis dan sedikit asam.', 'badge' => '', 'category' => 'Fruit'],
        5 => ['name' => 'Mochi Red Bean', 'price' => '40K', 'price_num' => 40000, 'image' => 'images/mochi_redbean.png', 'short' => 'Mochi tradisional dengan kacang merah manis, cita rasa klasik.', 'desc' => 'Mochi tradisional dengan kacang merah manis, cita rasa klasik Jepang yang legit dan tidak terlalu manis.', 'badge' => 'Promo', 'category' => 'Classic'],
        6 => ['name' => 'Mochi Vanilla', 'price' => '36K', 'price_num' => 36000, 'image' => 'images/mochi_vanilla.png', 'short' => 'Mochi dengan vanilla bean premium, lembut dan harum.', 'desc' => 'Mochi dengan vanilla bean premium, lembut dan harum dengan aroma vanilla yang alami dan manis.', 'badge' => '', 'category' => 'Classic'],
        ];
    }

    function mochiPriceLabel(int $n): string
    {
        if ($n % 1000 === 0) {
            return ($n / 1000) . 'K';
        }
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    function mochiLoadProducts(): array
    {
        try {
            $rows = getDB()->query('SELECT * FROM products WHERE is_active = 1 ORDER BY id')->fetchAll();
        } catch (Throwable $e) {
            error_log('products.php: ' . $e->getMessage());
            return mochiFallbackProducts();
        }

        $h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
        $out = [];
        foreach ($rows as $r) {
            $short = $r['short_desc'] !== '' ? $r['short_desc'] : mb_strimwidth($r['description'], 0, 90, '...');
            $out[(int)$r['id']] = [
                'name'      => $h($r['name']),
                'price'     => mochiPriceLabel((int)$r['price']),
                'price_num' => (int)$r['price'],
                'image'     => $h($r['image']),
                'short'     => $h($short),
                'desc'      => $h($r['description']),
                'badge'     => $h($r['badge']),
                'category'  => $h($r['category']),
            ];
        }
        return $out;
    }
}

return mochiLoadProducts();
