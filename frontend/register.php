<?php
$page_title = 'Register';
require_once __DIR__ . '/config.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = api_request('POST', '/auth/register', [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'password' => $_POST['password'] ?? '',
    ], false);

    if ($result['status'] === 201) {
        $_SESSION['token'] = $result['body']['token'];
        $_SESSION['user'] = $result['body']['user'];
        header('Location: index.php');
        exit;
    }
    $error = $result['body']['error'] ?? 'Registration failed.';
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page">
    <h1>Create an account</h1>
    <?php if ($error): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <label>Full name<input type="text" name="name" required></label>
        <label>Email<input type="email" name="email" required></label>
        <label>Phone<input type="text" name="phone"></label>
        <label>Password<input type="password" name="password" required minlength="6"></label>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
