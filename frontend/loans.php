<?php
$page_title = 'Loans';
require_once __DIR__ . '/config.php';
require_login();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = api_request('POST', '/loans', [
        'amount_requested' => $_POST['amount_requested'] ?? '',
        'purpose' => $_POST['purpose'] ?? '',
        'term_months' => $_POST['term_months'] ?? '',
    ]);
    if ($result['status'] !== 201) {
        $error = $result['body']['error'] ?? 'Could not submit application.';
    }
}

$list = api_request('GET', '/loans');
$loans = $list['status'] === 200 ? $list['body'] : [];

require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page">
    <h1>Apply for a Loan</h1>
    <?php if ($error): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <label>Amount requested (USD)<input type="number" step="0.01" min="1" name="amount_requested" required></label>
        <label>Purpose<input type="text" name="purpose" required></label>
        <label>Term (months)<input type="number" min="1" name="term_months" required></label>
        <button type="submit">Submit Application</button>
    </form>
</section>

<section class="list-page">
    <h2>Your Loan Applications</h2>
    <?php if (empty($loans)): ?>
        <p>No loan applications yet.</p>
    <?php else: ?>
        <table>
            <tr><th>Amount</th><th>Purpose</th><th>Term</th><th>Rate</th><th>Status</th></tr>
            <?php foreach ($loans as $l): ?>
                <tr>
                    <td>$<?= number_format($l['amount_requested'], 2) ?></td>
                    <td><?= htmlspecialchars($l['purpose']) ?></td>
                    <td><?= (int)$l['term_months'] ?> mo</td>
                    <td><?= $l['interest_rate'] ?>%</td>
                    <td><?= htmlspecialchars($l['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
