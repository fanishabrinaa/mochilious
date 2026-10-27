<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$orders = [];
$items  = [];
$error  = '';

try {
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();

    if ($orders) {
        $ids  = array_column($orders, 'id');
        $in   = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id IN ($in) ORDER BY id");
        $stmt->execute($ids);
        foreach ($stmt->fetchAll() as $it) {
            $items[$it['order_id']][] = $it;
        }
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    $error = 'Riwayat pesanan tidak dapat dimuat saat ini.';
}

$badge = ['pending' => 'warning text-dark', 'diproses' => 'info text-dark', 'selesai' => 'success', 'dibatalkan' => 'secondary'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - MochiLicious</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap');
        body { font-family: 'Poppins', sans-serif; background: #FFF8F3; color: #2C2C2C; }
        .brand { font-family: 'Playfair Display', serif; font-weight: 700; color: #FF6B9D; text-decoration: none; font-size: 1.6rem; }
        .card-order { border: 0; border-radius: 16px; box-shadow: 0 6px 24px rgba(255,107,157,.12); }
        .btn-pink { background: #FF8FAB; color: #fff; border: 0; }
        .btn-pink:hover { background: #FF6B9D; color: #fff; }
    </style>
</head>
<body>
<?php $activeNav = ''; include __DIR__ . '/partials/navbar.php'; ?>
<div class="container py-4" style="max-width: 760px;">
    <h1 class="h4 mb-3">Pesanan Saya</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $h($error); ?></div>
    <?php elseif (!$orders): ?>
        <div class="card card-order p-4 text-center">
            <p class="mb-3">Belum ada pesanan.</p>
            <a href="produk.php" class="btn btn-pink align-self-center">Lihat Produk</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $o): ?>
            <div class="card card-order mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <strong><?php echo $h($o['order_number']); ?></strong><br>
                            <small class="text-muted"><?php echo $h(date('d M Y H:i', strtotime($o['created_at']))); ?></small>
                        </div>
                        <div><span class="badge bg-<?php echo $badge[$o['status']] ?? 'secondary'; ?>"><?php echo $h(ucfirst($o['status'])); ?></span></div>
                    </div>
                    <ul class="list-unstyled my-3 mb-2">
                        <?php foreach ($items[$o['id']] ?? [] as $it): ?>
                            <li class="d-flex justify-content-between">
                                <span><?php echo (int)$it['quantity']; ?>x <?php echo $h($it['product_name']); ?></span>
                                <span><?php echo formatPrice($it['price'] * $it['quantity']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span>Total</span><strong><?php echo formatPrice($o['total']); ?></strong>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
