<?php
require_once __DIR__ . '/_init.php';

$stats = ['products' => 0, 'orders' => 0, 'pending' => 0, 'revenue' => 0];
$recent = [];
$error = '';

try {
    $db = getDB();
    $stats['products'] = (int)$db->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn();
    $stats['orders']   = (int)$db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $stats['pending']  = (int)$db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $stats['revenue']  = (int)$db->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status = 'selesai'")->fetchColumn();
    $recent = $db->query('SELECT * FROM orders ORDER BY id DESC LIMIT 8')->fetchAll();
} catch (Throwable $ex) {
    error_log($ex->getMessage());
    $error = 'Data tidak dapat dimuat. Pastikan database sudah diimpor (mochilicious.sql).';
}

adminHeader('Dashboard', 'dashboard');
?>
<h1 class="h4 mb-3">Dashboard</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="card-box p-3"><div class="text-muted small">Produk aktif</div><div class="stat-num"><?php echo $stats['products']; ?></div></div></div>
    <div class="col-6 col-lg-3"><div class="card-box p-3"><div class="text-muted small">Total pesanan</div><div class="stat-num"><?php echo $stats['orders']; ?></div></div></div>
    <div class="col-6 col-lg-3"><div class="card-box p-3"><div class="text-muted small">Menunggu</div><div class="stat-num"><?php echo $stats['pending']; ?></div></div></div>
    <div class="col-6 col-lg-3"><div class="card-box p-3"><div class="text-muted small">Pendapatan (selesai)</div><div class="stat-num fs-4"><?php echo formatPrice($stats['revenue']); ?></div></div></div>
</div>

<div class="card-box p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h6 mb-0">Pesanan terbaru</h2>
        <a href="orders.php" class="small">Lihat semua</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>No. Pesanan</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Waktu</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $o): ?>
                <tr>
                    <td><?php echo e($o['order_number']); ?></td>
                    <td><?php echo e($o['customer_name']); ?></td>
                    <td><?php echo formatPrice($o['total']); ?></td>
                    <td><?php echo statusBadge($o['status']); ?></td>
                    <td class="text-muted small"><?php echo e(date('d M Y H:i', strtotime($o['created_at']))); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recent): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php adminFooter();
