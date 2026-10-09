<?php
$pageTitle = 'Products | GadgetKart';
$basePath  = '';

require_once 'includes/functions.php';

$search   = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$sub      = trim($_GET['sub'] ?? '');
$sort     = $_GET['sort'] ?? 'newest';

$where  = ['is_active = 1'];
$params = [];
$types  = '';

if ($search !== '') {
    $where[] = '(name LIKE ? OR short_description LIKE ? OR description LIKE ? OR subcategory LIKE ?)';
    $q = "%$search%";
    array_push($params, $q, $q, $q, $q);
    $types .= 'ssss';
}

if ($category !== '') {
    $where[]  = 'category = ?';
    $params[] = $category;
    $types   .= 's';
}

if ($sub !== '') {
    $where[]  = 'subcategory = ?';
    $params[] = $sub;
    $types   .= 's';
}

$order = match ($sort) {
    'price_low'  => 'price ASC',
    'price_high' => 'price DESC',
    'name'       => 'name ASC',
    default      => 'id DESC',
};

$sql  = 'SELECT * FROM products WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order;
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$products = $stmt->get_result();

$categories = $conn->query(
    "SELECT DISTINCT category FROM products WHERE is_active = 1 ORDER BY category"
);

require 'includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <span class="eyebrow">GADGETKART CATALOGUE</span>
        <h1>Browse products</h1>
        <p>Find accessories for your phone, PC and gaming setup.</p>
    </div>
</section>

<!-- Products Catalogue Section -->
<section class="section">
    <div class="container">
        <!-- Search and Filter Bar -->
        <form class="filter-bar" method="get">
            <div class="search-field">
                <span>⌕</span>
                <input 
                    id="productSearch" 
                    type="search" 
                    name="q" 
                    value="<?= e($search) ?>" 
                    placeholder="Search gadgets..."
                >
            </div>

            <select name="category">
                <option value="">All categories</option>
                <?php while ($c = $categories->fetch_assoc()): ?>
                    <option value="<?= e($c['category']) ?>" <?= $category === $c['category'] ? 'selected' : '' ?>>
                        <?= e($c['category']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="sort">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
            </select>

            <button class="btn" type="submit">Apply</button>
        </form>

        <!-- Results Metadata -->
        <div class="results-meta">
            <span><?= (int)$products->num_rows ?> product(s) found</span>
            <?php if ($search !== '' || $category !== '' || $sub !== ''): ?>
                <a href="products.php">Clear filters</a>
            <?php endif; ?>
        </div>

        <!-- Products Grid -->
        <div class="product-grid">
            <?php while ($p = $products->fetch_assoc()): ?>
                <article class="product-card">
                    <a href="product.php?id=<?= (int)$p['id'] ?>" class="product-image">
                        <img src="assets/images/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                        <span><?= e($p['subcategory']) ?></span>
                    </a>
                    <div class="product-body">
                        <small><?= e($p['category']) ?></small>
                        <h3>
                            <a href="product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a>
                        </h3>
                        <p><?= e($p['short_description']) ?></p>
                        <div class="price-row">
                            <strong>₹<?= number_format($p['price'], 2) ?></strong>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button class="icon-btn" title="Add to cart" type="submit">+</button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Empty State -->
        <?php if ($products->num_rows === 0): ?>
            <div class="empty-state">
                <div>🔎</div>
                <h2>No products found</h2>
                <p>Try a different search term or category.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
