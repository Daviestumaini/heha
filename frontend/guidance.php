<?php
$page_title = 'Guidance';
require_once __DIR__ . '/config.php';
require_login();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = api_request('POST', '/guidance', [
        'topic' => $_POST['topic'] ?? '',
        'preferred_date' => $_POST['preferred_date'] ?? '',
        'notes' => $_POST['notes'] ?? '',
    ]);
    if ($result['status'] !== 201) {
        $error = $result['body']['error'] ?? 'Could not submit request.';
    }
}

$list = api_request('GET', '/guidance');
$sessions = $list['status'] === 200 ? $list['body'] : [];

require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page">
    <h1>Book a Guidance Session</h1>
    <?php if ($error): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <label>Topic<input type="text" name="topic" required placeholder="e.g. budgeting, relocation planning"></label>
        <label>Preferred date<input type="date" name="preferred_date" required></label>
        <label>Notes<textarea name="notes"></textarea></label>
        <button type="submit">Book Session</button>
    </form>
</section>

<section class="list-page">
    <h2>Your Guidance Sessions</h2>
    <?php if (empty($sessions)): ?>
        <p>No guidance sessions booked yet.</p>
    <?php else: ?>
        <table>
            <tr><th>Date</th><th>Topic</th><th>Status</th></tr>
            <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['preferred_date']) ?></td>
                    <td><?= htmlspecialchars($s['topic']) ?></td>
                    <td><?= htmlspecialchars($s['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
