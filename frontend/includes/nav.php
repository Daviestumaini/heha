<header class="site-header">
    <div class="container nav-bar">
        <a href="index.php" class="brand">HEHA Agency</a>
        <nav>
            <a href="index.php">Home</a>
            <a href="moving.php">Moving</a>
            <a href="loans.php">Loans</a>
            <a href="guidance.php">Guidance</a>
            <a href="shop.php">Shop</a>
            <a href="cart.php">Cart<?php if (!empty($_SESSION['cart'])): ?> (<?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?>)<?php endif; ?></a>
            <?php if (is_logged_in()): ?>
                <span class="user-pill">Hi, <?= htmlspecialchars(current_user()['name'] ?? 'User') ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
