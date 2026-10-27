<?php
/**
 * Dipakai oleh semua halaman admin: cek akses, CSRF, dan tampilan bersama.
 */
require_once __DIR__ . '/../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

function csrfCheck(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419);
        die('Sesi tidak valid. Silakan muat ulang halaman.');
    }
}

const ORDER_STATUSES = ['pending', 'diproses', 'selesai', 'dibatalkan'];

function statusBadge(string $status): string
{
    $map = ['pending' => 'warning text-dark', 'diproses' => 'info text-dark', 'selesai' => 'success', 'dibatalkan' => 'secondary'];
    return '<span class="badge bg-' . ($map[$status] ?? 'secondary') . '">' . e(ucfirst($status)) . '</span>';
}

function adminHeader(string $title, string $active): void
{
    $flash = getFlash();
    $links = [
        'dashboard' => ['dashboard.php', 'fa-gauge', 'Dashboard'],
        'products'  => ['products.php', 'fa-cookie-bite', 'Produk'],
        'orders'    => ['orders.php', 'fa-receipt', 'Pesanan'],
    ];
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?> - Admin MochiLicious</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap');
        :root { --pink: #FFB3C6; --pink-dark: #FF8FAB; --pink-accent: #FF6B9D; --mochi: #FFF8F3; --charcoal: #2C2C2C; }
        body { font-family: 'Poppins', sans-serif; background: var(--mochi); color: var(--charcoal); }
        .brand { font-family: 'Playfair Display', serif; font-weight: 700; color: var(--pink-accent); font-size: 1.4rem; text-decoration: none; }
        .topbar { background: #fff; border-bottom: 1px solid #f3e1e7; }
        .nav-pill { border-radius: 999px; padding: .4rem 1rem; color: var(--charcoal); text-decoration: none; font-weight: 500; }
        .nav-pill:hover { background: #ffe5ec; }
        .nav-pill.active { background: var(--pink-dark); color: #fff; }
        .card-box { background: #fff; border: 0; border-radius: 16px; box-shadow: 0 6px 24px rgba(255,107,157,.10); }
        .stat-num { font-size: 1.8rem; font-weight: 600; color: var(--pink-accent); }
        .btn-pink { background: var(--pink-dark); color: #fff; border: 0; }
        .btn-pink:hover { background: var(--pink-accent); color: #fff; }
        .thumb { width: 52px; height: 52px; object-fit: cover; border-radius: 10px; background: #ffe5ec; }
        table th { font-weight: 600; color: #7a5560; white-space: nowrap; }
    </style>
</head>
<body>
<header class="topbar py-2 mb-4">
    <div class="container d-flex flex-wrap align-items-center gap-3">
        <a href="dashboard.php" class="brand">MochiLicious <small class="text-muted fs-6">Admin</small></a>
        <nav class="d-flex flex-wrap gap-1 me-auto">
            <?php foreach ($links as $key => [$href, $icon, $label]): ?>
                <a href="<?php echo $href; ?>" class="nav-pill <?php echo $key === $active ? 'active' : ''; ?>">
                    <i class="fas <?php echo $icon; ?> me-1"></i><?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <a href="../index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-store"></i> Lihat Situs</a>
        <a href="../logout.php" class="btn btn-sm btn-outline-danger"><i class="fas fa-right-from-bracket"></i> Keluar</a>
    </div>
</header>
<main class="container pb-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
            <?php echo e($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php
}

function adminFooter(): void
{
    ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
}
