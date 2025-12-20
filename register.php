<?php
include 'config.php';
include 'auth.php'; // session, helpers (not enforcing login)

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $err = 'Username and password are required.';
    } else {
        // Default to guest
        $role = 'guest';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (Username, PasswordHash, Role) VALUES (?,?,?)");
        $stmt->bind_param("sss", $username, $hash, $role);

        if (!$stmt->execute()) {
            if ($conn->errno === 1062) {
                $err = 'That username is already taken.';
            } else {
                $err = 'Registration failed: ' . $stmt->error;
            }
        } else {
            $msg = 'Guest account created ✔ You can now log in.';
        }
    }
}
?>
<?php include 'header.php'; ?>
<div class="card" style="max-width:560px;margin:40px auto;">
  <h2>Register as Guest</h2>

  <?php if ($msg) { ?>
    <div class="alert"><?php echo h($msg); ?></div>
  <?php } ?>

  <?php if ($err) { ?>
    <div class="alert" style="background:#fff1f2;border-color:#fecdd3;color:#7f1d1d;">
      <?php echo h($err); ?>
    </div>
  <?php } ?>

  <form method="post" class="grid" style="grid-template-columns:1fr;">
    <input name="username" placeholder="Choose a username" required>
    <input type="password" name="password" placeholder="Choose a password" required>
    <button type="submit">Register</button>
  </form>

  <p class="muted">
    Already have an account? <a href="login.php">Log in here</a>.
  </p>
</div>
<?php include 'footer.php'; ?>
