<?php
$page_title = 'Shop';
require_once __DIR__ . '/config.php';

// Add to cart (session-based, no login required to browse/add)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int)$_POST['product_id'];
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $qty;
    } else {
        $_SESSION['cart'][$productId] = ['product_id' => $productId, 'quantity' => $qty];
    }
    header('Location: shop.php' . (!empty($_GET['category']) ? '?category=' . urlencode($_GET['category']) : ''));
    exit;
}

$category = $_GET['category'] ?? null;
$endpoint = '/products' . ($category ? '?category=' . urlencode($category) : '');
$result = api_request('GET', $endpoint, null, false);
$products = $result['status'] === 200 ? $result['body'] : [];

require_once __DIR__ . '/includes/header.php';
?>
<section class="shop-header">
    <h1>Shop</h1>
    <div class="filters">
        <a href="shop.php" class="<?= !$category ? 'active' : '' ?>">All</a>
        <a href="shop.php?category=merchandise" class="<?= $category === 'merchandise' ? 'active' : '' ?>">Merchandise</a>
        <a href="shop.php?category=phone" class="<?= $category === 'phone' ? 'active' : '' ?>">Phones</a>
        <a href="shop.php?category=case" class="<?= $category === 'case' ? 'active' : '' ?>">Cases</a>
    </div>
</section>

<section class="product-grid">
    <?php if (empty($products)): ?>
        <p>No products found. (Run seed.py on the backend to load sample products.)</p>
    <?php endif; ?>
    <?php foreach ($products as $p): ?>
        <div class="product-card">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p class="category-tag"><?= htmlspecialchars($p['category']) ?></p>
            <p><?= htmlspecialchars($p['description']) ?></p>
            <p class="price">$<?= number_format($p['price'], 2) ?></p>
            <p class="stock">In stock: <?= (int)$p['stock'] ?></p>
            <form method="post">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?= (int)$p['stock'] ?>">
                <button type="submit" name="add_to_cart" value="1" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
                    Add to Cart
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
