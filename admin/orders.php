<?php
require_once __DIR__ . '/_init.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();
    $id     = (int)($_POST['id'] ?? 0);
    $status = (string)($_POST['status'] ?? '');
    if ($id > 0 && in_array($status, ORDER_STATUSES, true)) {
        $db->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $id]);
        setFlash('success', 'Status pesanan diperbarui.');
    }
    redirect('orders.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
}

$filter = $_GET['status'] ?? '';
if ($filter !== '' && in_array($filter, ORDER_STATUSES, true)) {
    $stmt = $db->prepare('SELECT * FROM orders WHERE status = ? ORDER BY id DESC');
    $stmt->execute([$filter]);
    $orders = $stmt->fetchAll();
} else {
    $filter = '';
    $orders = $db->query('SELECT * FROM orders ORDER BY id DESC')->fetchAll();
}

$items = [];
if ($orders) {
    $ids  = array_column($orders, 'id');
    $in   = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id IN ($in) ORDER BY id");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $it) {
        $items[$it['order_id']][] = $it;
    }
}

adminHeader('Pesanan', 'orders');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Pesanan</h1>
    <div class="d-flex flex-wrap gap-1">
        <a href="orders.php" class="btn btn-sm <?php echo $filter === '' ? 'btn-pink' : 'btn-outline-secondary'; ?>">Semua</a>
        <?php foreach (ORDER_STATUSES as $s): ?>
            <a href="orders.php?status=<?php echo $s; ?>" class="btn btn-sm <?php echo $filter === $s ? 'btn-pink' : 'btn-outline-secondary'; ?>"><?php echo e(ucfirst($s)); ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php foreach ($orders as $o): ?>
    <div class="card-box p-3 mb-3">
        <div class="d-flex flex-wrap justify-content-between gap-2">
            <div>
                <strong><?php echo e($o['order_number']); ?></strong>
                <span class="ms-2"><?php echo statusBadge($o['status']); ?></span><br>
                <small class="text-muted"><?php echo e($o['customer_name']); ?> &middot; <?php echo e(date('d M Y H:i', strtotime($o['created_at']))); ?></small>
            </div>
            <form method="post" class="d-flex gap-2 align-items-start">
                <?php echo csrfField(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$o['id']; ?>">
                <select name="status" class="form-select form-select-sm">
                    <?php foreach (ORDER_STATUSES as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $o['status'] === $s ? 'selected' : ''; ?>><?php echo e(ucfirst($s)); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-pink">Simpan</button>
            </form>
        </div>
        <ul class="list-unstyled mt-3 mb-2">
            <?php foreach ($items[$o['id']] ?? [] as $it): ?>
                <li class="d-flex justify-content-between">
                    <span><?php echo (int)$it['quantity']; ?>x <?php echo e($it['product_name']); ?></span>
                    <span><?php echo formatPrice($it['price'] * $it['quantity']); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="d-flex justify-content-between border-top pt-2"><span>Total</span><strong><?php echo formatPrice($o['total']); ?></strong></div>
    </div>
<?php endforeach; ?>

<?php if (!$orders): ?>
    <div class="card-box p-4 text-center text-muted">Belum ada pesanan.</div>
<?php endif; ?>
<?php adminFooter();
