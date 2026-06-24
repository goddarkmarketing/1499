<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
start_session(app_config('session.admin_key'));

if (admin_user()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE email = ? AND status = "active" LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        unset($user['password_hash']);
        $_SESSION['admin'] = $user;
        db()->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
        log_activity((int) $user['id'], 'login');
        header('Location: index.php');
        exit;
    }
    $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>เข้าสู่ระบบ | BOYINSURE Admin</title>
  <link rel="stylesheet" href="../assets/css/fonts.css" />
  <link rel="stylesheet" href="assets/admin.css" />
  <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>
</head>
<body>
  <div class="admin-login">
    <div class="admin-login__panel">
      <div class="admin-login__card">
        <div class="admin-login__card-header">
          <div class="admin-login__card-mark">B</div>
          <h1>BOYINSURE Admin</h1>
          <p class="admin-login__card-desc">เข้าสู่ระบบหลังบ้าน</p>
        </div>
        <?php if ($error): ?>
          <div class="admin-login__error">
            <i data-lucide="alert-circle" aria-hidden="true"></i>
            <?= h($error) ?>
          </div>
        <?php endif; ?>
        <form method="post" class="admin-form admin-form--wide">
          <label for="email">อีเมล</label>
          <input type="email" id="email" name="email" required value="<?= h($_POST['email'] ?? '') ?>" autocomplete="username" />
          <label for="password">รหัสผ่าน</label>
          <input type="password" id="password" name="password" required autocomplete="current-password" />
          <button type="submit" class="admin-btn admin-btn--primary admin-btn--block">เข้าสู่ระบบ</button>
        </form>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide?.createIcons) lucide.createIcons();
    });
  </script>
</body>
</html>
