<?php
require_once __DIR__ . '/_init.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();
    $id = (int)($_POST['id'] ?? 0);

    if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
        $stmt = $db->prepare('SELECT image FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $image = $stmt->fetchColumn();

        $db->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);

        // Hanya hapus file yang diunggah lewat admin, bukan gambar bawaan
        if ($image && strpos(basename($image), 'upload_') === 0) {
            $path = __DIR__ . '/../images/' . basename($image);
            if (is_file($path)) {
                @unlink($path);
            }
        }
        setFlash('success', 'Produk dihapus.');
    } elseif (($_POST['action'] ?? '') === 'toggle' && $id > 0) {
        $db->prepare('UPDATE products SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
        setFlash('success', 'Status produk diperbarui.');
    }
    redirect('products.php');
}

$products = $db->query('SELECT * FROM products ORDER BY id')->fetchAll();

adminHeader('Produk', 'products');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Produk</h1>
    <a href="product_form.php" class="btn btn-pink"><i class="fas fa-plus"></i> Tambah Produk</a>
</div>

<div class="card-box p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Foto</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Label</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><img class="thumb" src="../<?php echo e($p['image']); ?>" alt=""></td>
                    <td><?php echo e($p['name']); ?></td>
                    <td><?php echo e($p['category']); ?></td>
                    <td><?php echo formatPrice($p['price']); ?></td>
                    <td><?php echo $p['badge'] !== '' ? '<span class="badge bg-danger">' . e($p['badge']) . '</span>' : '-'; ?></td>
                    <td><?php echo $p['is_active'] ? '<span class="badge bg-success">Tampil</span>' : '<span class="badge bg-secondary">Disembunyikan</span>'; ?></td>
                    <td class="text-end text-nowrap">
                        <a href="product_form.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                        <form method="post" class="d-inline">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                            <button name="action" value="toggle" class="btn btn-sm btn-outline-secondary" title="Tampilkan / sembunyikan"><i class="fas fa-eye"></i></button>
                            <button name="action" value="delete" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Hapus produk ini?');"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$products): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada produk.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php adminFooter();
