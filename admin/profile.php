<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $stmt = db()->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
    $stmt->execute([$admin['id']]);
    $hash = $stmt->fetchColumn();
    if (!password_verify($current, $hash)) {
        $error = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
    } elseif (strlen($new) < 6) {
        $error = 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัว';
    } elseif ($new !== $confirm) {
        $error = 'รหัสผ่านใหม่ไม่ตรงกัน';
    } else {
        db()->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
        log_activity((int) $admin['id'], 'change_password');
        flash_set('success', 'เปลี่ยนรหัสผ่านแล้ว');
        header('Location: profile.php');
        exit;
    }
}

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>โปรไฟล์ของฉัน</h2></div>
  <div class="admin-card__body">
    <p style="margin:0 0 20px;"><strong><?= h($admin['name']) ?></strong><br><span class="text-muted"><?= h($admin['email']) ?> · <?= h($admin['role']) ?></span></p>
    <?php if ($error): ?><div class="admin-login__error" style="margin-bottom:16px;"><?= h($error) ?></div><?php endif; ?>
    <form method="post" class="admin-form">
      <label>รหัสผ่านปัจจุบัน</label>
      <input type="password" name="current_password" required autocomplete="current-password" />
      <label>รหัสผ่านใหม่</label>
      <input type="password" name="new_password" required minlength="6" autocomplete="new-password" />
      <label>ยืนยันรหัสผ่านใหม่</label>
      <input type="password" name="confirm_password" required minlength="6" autocomplete="new-password" />
      <button type="submit" class="admin-btn admin-btn--primary">เปลี่ยนรหัสผ่าน</button>
    </form>
  </div>
</div>
<?php
render_layout('โปรไฟล์', ob_get_clean(), 'profile');
