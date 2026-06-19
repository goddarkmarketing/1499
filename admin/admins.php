<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $admin['role'] === 'super_admin') {
    if (($_POST['action'] ?? '') === 'create') {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        db()->prepare('INSERT INTO admin_users (email, password_hash, name, role, status) VALUES (?,?,?,?,?)')
            ->execute([trim($_POST['email']), $hash, trim($_POST['name']), $_POST['role'], 'active']);
        flash_set('success', 'เพิ่มผู้ดูแลแล้ว');
    }
    header('Location: admins.php');
    exit;
}

$rows = db()->query('SELECT id, email, name, role, status, last_login_at, created_at FROM admin_users ORDER BY id')->fetchAll();

ob_start();
?>
<?php if ($admin['role'] === 'super_admin'): ?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มผู้ดูแลระบบ</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form">
      <input type="hidden" name="action" value="create" />
      <label>ชื่อ</label><input name="name" required />
      <label>อีเมล</label><input type="email" name="email" required />
      <label>รหัสผ่าน</label><input type="password" name="password" required minlength="6" />
      <label>บทบาท</label>
      <select name="role">
        <option value="ops">ops — ของรางวัล/โปร</option>
        <option value="hr">hr — สมัครตัวแทน</option>
        <option value="sales_manager">sales_manager</option>
        <option value="support">support — Lead</option>
        <option value="super_admin">super_admin</option>
      </select>
      <button class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>
<?php endif; ?>
<div class="admin-card">
  <div class="admin-card__head"><h2>ผู้ดูแลระบบ</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>ชื่อ</th><th>อีเมล</th><th>บทบาท</th><th>เข้าล่าสุด</th><th>สถานะ</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['name']) ?></td>
            <td><?= h($r['email']) ?></td>
            <td><?= h($r['role']) ?></td>
            <td><small><?= h($r['last_login_at'] ?? '-') ?></small></td>
            <td><?= h($r['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php render_layout('ผู้ดูแลระบบ', ob_get_clean(), 'admins');
