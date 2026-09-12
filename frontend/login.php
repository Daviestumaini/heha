<?php
$page_title = 'Login';
require_once __DIR__ . '/config.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = api_request('POST', '/auth/login', [
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
    ], false);

    if ($result['status'] === 200) {
        $_SESSION['token'] = $result['body']['token'];
        $_SESSION['user'] = $result['body']['user'];
        header('Location: index.php');
        exit;
    }
    $error = $result['body']['error'] ?? 'Login failed.';
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page">
    <h1>Login</h1>
    <?php if ($error): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <label>Email<input type="email" name="email" required></label>
        <label>Password<input type="password" name="password" required></label>
        <button type="submit">Login</button>
    </form>
    <p>No account yet? <a href="register.php">Register</a></p>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
