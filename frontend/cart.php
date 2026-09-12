<?php
$page_title = 'Cart';
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_id'])) {
        unset($_SESSION['cart'][(int)$_POST['remove_id']]);
    }
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0.0;

if (!empty($cart)) {
    $result = api_request('GET', '/products', null, false);
    $allProducts = $result['status'] === 200 ? $result['body'] : [];
    $byId = [];
    foreach ($allProducts as $p) {
        $byId[$p['id']] = $p;
    }
    foreach ($cart as $item) {
        $p = $byId[$item['product_id']] ?? null;
        if ($p) {
            $lineTotal = $p['price'] * $item['quantity'];
            $total += $lineTotal;
            $products[] = ['product' => $p, 'quantity' => $item['quantity'], 'line_total' => $lineTotal];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="list-page">
    <h1>Your Cart</h1>
    <?php if (empty($products)): ?>
        <p>Your cart is empty. <a href="shop.php">Go shopping</a>.</p>
    <?php else: ?>
        <table>
            <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th><th></th></tr>
            <?php foreach ($products as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['product']['name']) ?></td>
                    <td><?= (int)$row['quantity'] ?></td>
                    <td>$<?= number_format($row['product']['price'], 2) ?></td>
                    <td>$<?= number_format($row['line_total'], 2) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="remove_id" value="<?= (int)$row['product']['id'] ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p class="total">Total: $<?= number_format($total, 2) ?></p>
        <a class="btn" href="checkout.php">Proceed to Checkout</a>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
