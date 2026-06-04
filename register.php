<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Create Account';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = sanitize($_POST['username'] ?? '');
    $email     = sanitize($_POST['email'] ?? '');
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone     = sanitize($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username or email already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Username or email already exists. Please choose another.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "
                INSERT INTO users (username, email, password, full_name, phone)
                VALUES ('$username', '$email', '$hashed', '$full_name', '$phone')
            ");
            if ($insert) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="form-page">
    <div class="form-card" style="max-width:520px;">
        <div style="text-align:center;margin-bottom:28px;">
            <div style="font-size:2.5rem;margin-bottom:8px;">🛍️</div>
            <h2>Create Account</h2>
            <p class="subtitle">Join Sokoni Hub and start shopping smarter</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= $success ?> <a href="login.php" style="font-weight:700;color:var(--success)">Login →</a></div>
        <?php endif; ?>

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="e.g. John Kamau" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" form="reg-form">
        </div>

        <form id="reg-form" method="POST" action="register.php">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>Username <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="username" placeholder="johndoe" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="0712 345 678" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Email Address <span style="color:var(--danger)">*</span></label>
                <input type="email" name="email" placeholder="john@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="password" placeholder="Min. 6 characters" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="confirm_password" placeholder="Repeat password" required>
                </div>
            </div>

            <input type="hidden" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">

            <div style="background:var(--bg);padding:14px;border-radius:10px;margin-bottom:20px;font-size:0.82rem;color:var(--text-muted);">
                By registering, you agree to our Terms of Service and Privacy Policy.
            </div>

            <button type="submit" class="btn-submit">Create My Account 🚀</button>
        </form>

        <p class="form-footer">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
