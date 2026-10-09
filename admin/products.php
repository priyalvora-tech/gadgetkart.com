<?php
require_once '../includes/functions.php';

require_admin();

// Archive product handler
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $st = $conn->prepare('UPDATE products SET is_active = 0 WHERE id = ?');
    $st->bind_param('i', $id);
    $st->execute();

    flash('notice', 'Product archived.');
    redirect('products.php');
}

$products  = $conn->query('SELECT * FROM products ORDER BY id DESC');
$pageTitle = 'Manage Products | GadgetKart';
$basePath  = '../';

require '../includes/header.php';
?>

<!-- Manage Products Section -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">ADMIN</span>
                <h1>Manage Products</h1>
            </div>
            <a class="btn" href="add-product.php">+ Add Product</a>
        </div>

        <div class="admin-table-card">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $products->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="table-product">
                                        <img src="../assets/images/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                                        <strong><?= e($p['name']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <?= e($p['category']) ?>
                                    <br>
                                    <small><?= e($p['subcategory']) ?></small>
                                </td>
                                <td>₹<?= number_format($p['price'], 2) ?></td>
                                <td><?= (int)$p['stock'] ?></td>
                                <td><?= $p['is_featured'] ? 'Yes' : 'No' ?></td>
                                <td class="actions">
                                    <a href="edit-product.php?id=<?= (int)$p['id'] ?>">Edit</a>
                                    <a 
                                        class="danger" 
                                        href="products.php?delete=<?= (int)$p['id'] ?>" 
                                        data-confirm="Archive this product?"
                                    >Archive</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require '../includes/footer.php'; ?>
