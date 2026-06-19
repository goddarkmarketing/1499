<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id && ($_POST['action'] ?? '') === 'update') {
        db()->prepare('UPDATE member_tiers SET name=?, spin_quota=?, min_premium=?, description=?, sort_order=?, status=? WHERE id=?')
            ->execute([
                trim($_POST['name']),
                (int) $_POST['spin_quota'],
                $_POST['min_premium'] !== '' ? (float) $_POST['min_premium'] : null,
                trim($_POST['description'] ?? '') ?: null,
                (int) $_POST['sort_order'],
                $_POST['status'],
                $id,
            ]);
        flash_set('success', 'อัปเดตระดับสมาชิกแล้ว');
    }
    if (($_POST['action'] ?? '') === 'create') {
        db()->prepare('INSERT INTO member_tiers (code, name, spin_quota, min_premium, description, sort_order) VALUES (?,?,?,?,?,?)')
            ->execute([
                trim($_POST['code']),
                trim($_POST['name']),
                (int) $_POST['spin_quota'],
                $_POST['min_premium'] !== '' ? (float) $_POST['min_premium'] : null,
                trim($_POST['description'] ?? '') ?: null,
                (int) $_POST['sort_order'],
            ]);
        flash_set('success', 'เพิ่มระดับแล้ว');
    }
    header('Location: member-tiers.php');
    exit;
}

$rows = db()->query('SELECT * FROM member_tiers ORDER BY sort_order')->fetchAll();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มระดับสมาชิก</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form">
      <input type="hidden" name="action" value="create" />
      <div class="admin-form__row">
        <div><label>รหัส (code)</label><input name="code" required placeholder="เช่น VIP" /></div>
        <div><label>ชื่อ</label><input name="name" required /></div>
      </div>
      <div class="admin-form__row">
        <div><label>สิทธิ์หมุนวงล้อ</label><input type="number" name="spin_quota" min="0" value="1" /></div>
        <div><label>เบี้ยขั้นต่ำ (บาท)</label><input type="number" name="min_premium" step="0.01" /></div>
      </div>
      <label>คำอธิบาย</label><textarea name="description"></textarea>
      <label>ลำดับ</label><input type="number" name="sort_order" value="0" />
      <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>
<div class="admin-card">
  <div class="admin-card__head"><h2>ระดับสมาชิก</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>รหัส</th><th>ชื่อ</th><th>หมุน</th><th>เบี้ยขั้นต่ำ</th><th>สถานะ</th><th>แก้ไข</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['code']) ?></td>
            <td><?= h($r['name']) ?></td>
            <td><?= (int) $r['spin_quota'] ?> ครั้ง</td>
            <td><?= $r['min_premium'] ? number_format((float)$r['min_premium']) : '-' ?></td>
            <td><?= h($r['status']) ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('tier-edit-' . $r['id']) ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('tier-edit-' . $r['id'], 'แก้ไขระดับ — ' . $r['name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <label>ชื่อ</label><input name="name" value="<?= h($r['name']) ?>" />
  <div class="admin-form__row">
    <div><label>สิทธิ์หมุน</label><input type="number" name="spin_quota" value="<?= (int) $r['spin_quota'] ?>" /></div>
    <div><label>เบี้ยขั้นต่ำ</label><input type="number" name="min_premium" value="<?= h((string) $r['min_premium']) ?>" /></div>
  </div>
  <label>คำอธิบาย</label><textarea name="description" rows="3"><?= h($r['description'] ?? '') ?></textarea>
  <div class="admin-form__row">
    <div><label>ลำดับ</label><input type="number" name="sort_order" value="<?= (int) $r['sort_order'] ?>" /></div>
    <div>
      <label>สถานะ</label>
      <select name="status">
        <option value="active">active</option>
        <option value="inactive"<?= $r['status'] === 'inactive' ? ' selected' : '' ?>>inactive</option>
      </select>
    </div>
  </div>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('ระดับสมาชิก', ob_get_clean(), 'member-tiers');
