<?php
$page_title = 'Checkout';
require_once __DIR__ . '/config.php';
require_login(); // must be logged in to actually place an order

$cart = $_SESSION['cart'] ?? [];
$error = null;
$success = null;

if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = array_values(array_map(fn($c) => [
        'product_id' => $c['product_id'],
        'quantity' => $c['quantity'],
    ], $cart));

    $result = api_request('POST', '/orders', ['items' => $items]);

    if ($result['status'] === 201) {
        $_SESSION['cart'] = []; // clear cart
        $success = $result['body'];
    } else {
        $error = $result['body']['error'] ?? 'Checkout failed.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page">
    <h1>Checkout</h1>
    <?php if ($error): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <?php if ($success): ?>
        <p class="success">Order #<?= (int)$success['id'] ?> placed successfully!
            Total: $<?= number_format($success['total_amount'], 2) ?></p>
        <a class="btn" href="shop.php">Continue Shopping</a>
    <?php else: ?>
        <p>Review your cart, then confirm your order.</p>
        <form method="post">
            <button type="submit">Confirm &amp; Place Order</button>
        </form>
        <a href="cart.php">Back to cart</a>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
