<?php
require_once __DIR__ . '/_init.php';

$db = getDB();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$errors = [];

$product = ['name' => '', 'price' => '', 'short_desc' => '', 'description' => '', 'image' => '', 'badge' => '', 'category' => 'Classic', 'is_active' => 1];

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        setFlash('error', 'Produk tidak ditemukan.');
        redirect('products.php');
    }
    $product = $found;
}

/** Simpan gambar unggahan. Mengembalikan path relatif (images/upload_xxx.ext) atau null. */
function saveUpload(array $file, array &$errors): ?string
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Gagal mengunggah gambar.';
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        $errors[] = 'Ukuran gambar maksimal 2 MB.';
        return null;
    }
    $info = @getimagesize($file['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($types[$info[2]])) {
        $errors[] = 'Gambar harus berformat JPG, PNG, atau WEBP.';
        return null;
    }
    $name = 'upload_' . bin2hex(random_bytes(6)) . '.' . $types[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../images/' . $name)) {
        $errors[] = 'Gambar tidak dapat disimpan. Cek izin tulis folder images/.';
        return null;
    }
    return 'images/' . $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $clean = fn($s) => trim(str_replace('\\', '', (string)$s));
    $product['name']        = mb_substr($clean($_POST['name'] ?? ''), 0, 100);
    $product['category']    = mb_substr($clean($_POST['category'] ?? ''), 0, 50);
    $product['badge']       = mb_substr($clean($_POST['badge'] ?? ''), 0, 30);
    $product['short_desc']  = mb_substr($clean($_POST['short_desc'] ?? ''), 0, 255);
    $product['description'] = $clean($_POST['description'] ?? '');
    $product['price']       = (int)($_POST['price'] ?? 0);
    $product['is_active']   = isset($_POST['is_active']) ? 1 : 0;

    if ($product['name'] === '')        { $errors[] = 'Nama produk harus diisi.'; }
    if ($product['category'] === '')    { $errors[] = 'Kategori harus diisi.'; }
    if ($product['price'] < 1000)       { $errors[] = 'Harga minimal Rp 1.000.'; }
    if ($product['description'] === '') { $errors[] = 'Deskripsi harus diisi.'; }

    $oldImage = $product['image'];
    $newImage = saveUpload($_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE], $errors);
    if ($id === 0 && $newImage === null && !$errors) {
        $errors[] = 'Foto produk harus diunggah.';
    }

    if (!$errors) {
        if ($newImage !== null) {
            $product['image'] = $newImage;
        }
        $params = [$product['name'], $product['price'], $product['short_desc'], $product['description'],
                   $product['image'], $product['badge'], $product['category'], $product['is_active']];

        if ($id > 0) {
            $db->prepare('UPDATE products SET name=?, price=?, short_desc=?, description=?, image=?, badge=?, category=?, is_active=? WHERE id=?')
               ->execute(array_merge($params, [$id]));
            // Hapus foto lama hanya kalau itu hasil unggahan admin
            if ($newImage !== null && strpos(basename($oldImage), 'upload_') === 0) {
                $path = __DIR__ . '/../images/' . basename($oldImage);
                if (is_file($path)) {
                    @unlink($path);
                }
            }
            setFlash('success', 'Produk diperbarui.');
        } else {
            $db->prepare('INSERT INTO products (name, price, short_desc, description, image, badge, category, is_active) VALUES (?,?,?,?,?,?,?,?)')
               ->execute($params);
            setFlash('success', 'Produk ditambahkan.');
        }
        redirect('products.php');
    }
}

$categories = $db->query('SELECT DISTINCT category FROM products ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
foreach (['Classic', 'Fruit'] as $c) {
    if (!in_array($c, $categories, true)) {
        $categories[] = $c;
    }
}

adminHeader($id ? 'Edit Produk' : 'Tambah Produk', 'products');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><?php echo $id ? 'Edit Produk' : 'Tambah Produk'; ?></h1>
    <a href="products.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger mb-3"><ul class="mb-0">
        <?php foreach ($errors as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?>
    </ul></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card-box p-4">
    <?php echo csrfField(); ?>
    <input type="hidden" name="id" value="<?php echo (int)$id; ?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama produk</label>
            <input type="text" name="name" class="form-control" maxlength="100" value="<?php echo e($product['name']); ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="price" class="form-control" min="1000" step="500" value="<?php echo e($product['price']); ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Kategori</label>
            <input type="text" name="category" class="form-control" list="cats" maxlength="50" value="<?php echo e($product['category']); ?>" required>
            <datalist id="cats"><?php foreach ($categories as $c): ?><option value="<?php echo e($c); ?>"><?php endforeach; ?></datalist>
        </div>
        <div class="col-md-4">
            <label class="form-label">Label</label>
            <select name="badge" class="form-select">
                <?php foreach (['' => '(tanpa label)', 'Best Seller' => 'Best Seller', 'Promo' => 'Promo', 'New' => 'New'] as $val => $label): ?>
                    <option value="<?php echo e($val); ?>" <?php echo $product['badge'] === $val ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label">Deskripsi singkat <small class="text-muted">(untuk kartu di beranda)</small></label>
            <input type="text" name="short_desc" class="form-control" maxlength="255" value="<?php echo e($product['short_desc']); ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Deskripsi lengkap</label>
            <textarea name="description" rows="4" class="form-control" required><?php echo e($product['description']); ?></textarea>
        </div>
        <div class="col-md-8">
            <label class="form-label">Foto <small class="text-muted">(JPG/PNG/WEBP, maks 2 MB<?php echo $id ? ', kosongkan jika tidak diganti' : ''; ?>)</small></label>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp" <?php echo $id ? '' : 'required'; ?>>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <?php if ($product['image']): ?>
                <img class="thumb" style="width:72px;height:72px" src="../<?php echo e($product['image']); ?>" alt="">
            <?php endif; ?>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_active">Tampilkan di situs</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-pink"><i class="fas fa-floppy-disk"></i> Simpan</button>
    </div>
</form>
<?php adminFooter();
