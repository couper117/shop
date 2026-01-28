<?php
require 'config.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM admins WHERE username = '$username' LIMIT 1");
    if ($res && $res->num_rows === 1) {
        $admin = $res->fetch_assoc();
        if (hash('sha256', $password) === $admin['password_hash']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit;
        }
    }
    $error = "Invalid credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Newtextile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
  <div class="navbar">
    <div class="brand">Newtextile Admin</div>
    <div class="nav-links">
      <a href="index.php">Back to Shop</a>
    </div>
  </div>
</header>

<div class="admin-container">
  <h2>Admin Login</h2>
  <?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
  <?php endif; ?>
  <form method="post">
    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
  </form>
</div>
</body>
</html>
