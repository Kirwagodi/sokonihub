<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$username' LIMIT 1");
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
            header("Location: $redirect");
            exit();
        } else {
            $error = 'Invalid username or password. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="form-page">
    <div class="form-card">
        <div style="text-align:center;margin-bottom:28px;">
            <div style="font-size:2.5rem;margin-bottom:8px;"></div>
            <h2>Welcome Back Kirwa</h2>
            <p class="subtitle">Login to your Sokoni Hub account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"> <?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">✅ Account created! Login below.</div>
        <?php endif; ?>

        <form method="POST" action="login.php<?= isset($_GET['redirect']) ? '?redirect='.urlencode($_GET['redirect']) : '' ?>">
            <div class="form-group">
                <label>Username or Email <span style="color:var(--danger)">*</span></label>
                <input type="text" name="username" placeholder="your username or email" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password <span style="color:var(--danger)">*</span></label>
                <input type="password" name="password" placeholder="your password" required>
            </div>

            <button type="submit" class="btn-submit">Login →</button>
        </form>

        <p class="form-footer">Don't have an account? <a href="register.php">Register for free</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
