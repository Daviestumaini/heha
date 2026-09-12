<?php
$page_title = 'Moving Services';
require_once __DIR__ . '/config.php';
require_login();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = api_request('POST', '/moving/requests', [
        'pickup_address' => $_POST['pickup_address'] ?? '',
        'dropoff_address' => $_POST['dropoff_address'] ?? '',
        'item_list' => $_POST['item_list'] ?? '',
        'preferred_date' => $_POST['preferred_date'] ?? '',
    ]);
    if ($result['status'] !== 201) {
        $error = $result['body']['error'] ?? 'Could not submit request.';
    }
}

$list = api_request('GET', '/moving/requests');
$requests = $list['status'] === 200 ? $list['body'] : [];

require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page">
    <h1>Request a Move</h1>
    <?php if ($error): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <label>Pickup address<input type="text" name="pickup_address" required></label>
        <label>Drop-off address<input type="text" name="dropoff_address" required></label>
        <label>Items to move<textarea name="item_list" required placeholder="e.g. sofa, fridge, 10 boxes"></textarea></label>
        <label>Preferred date<input type="date" name="preferred_date" required></label>
        <button type="submit">Submit Request</button>
    </form>
</section>

<section class="list-page">
    <h2>Your Moving Requests</h2>
    <?php if (empty($requests)): ?>
        <p>No moving requests yet.</p>
    <?php else: ?>
        <table>
            <tr><th>Date</th><th>Pickup</th><th>Drop-off</th><th>Status</th><th>Quote</th></tr>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['preferred_date']) ?></td>
                    <td><?= htmlspecialchars($r['pickup_address']) ?></td>
                    <td><?= htmlspecialchars($r['dropoff_address']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td><?= $r['quote_amount'] !== null ? '$' . number_format($r['quote_amount'], 2) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
