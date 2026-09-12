<?php
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <h1>Welcome to HEHA Agency</h1>
    <p>Your one-stop hub for house moving, loans, guidance, and shopping. Pick a service below to get started.</p>
</section>

<section class="service-grid">
    <a class="service-card" href="moving.php">
        <h2>🚚 Moving Services</h2>
        <p>Request a quote and book professional movers for your house or office items.</p>
    </a>

    <a class="service-card" href="loans.php">
        <h2>💰 Loans</h2>
        <p>Apply for a loan and track the status of your applications.</p>
    </a>

    <a class="service-card" href="guidance.php">
        <h2>🧭 Guidance</h2>
        <p>Book a one-on-one guidance / consultation session with our advisors.</p>
    </a>

    <a class="service-card" href="shop.php">
        <h2>🛍️ Shop</h2>
        <p>Browse merchandise, phones, and phone cases.</p>
    </a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
