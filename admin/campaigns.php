<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'create') {
        db()->prepare('INSERT INTO campaigns (name, description, start_date, end_date, status) VALUES (?,?,?,?,?)')
            ->execute([
                trim($_POST['name']),
                trim($_POST['description'] ?? '') ?: null,
                $_POST['start_date'] ?: null,
                $_POST['end_date'] ?: null,
                $_POST['status'] ?? 'draft',
            ]);
        flash_set('success', 'เพิ่มแคมเปญแล้ว');
    }
    if (($_POST['action'] ?? '') === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        db()->prepare('UPDATE campaigns SET name=?, description=?, start_date=?, end_date=?, status=? WHERE id=?')
            ->execute([
                trim($_POST['name']),
                trim($_POST['description'] ?? '') ?: null,
                $_POST['start_date'] ?: null,
                $_POST['end_date'] ?: null,
                $_POST['status'], $id,
            ]);
        flash_set('success', 'อัปเดตแคมเปญแล้ว');
    }
    header('Location: campaigns.php');
    exit;
}

$rows = db()->query('SELECT * FROM campaigns ORDER BY created_at DESC')->fetchAll();
ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มแคมเปญ</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form">
      <input type="hidden" name="action" value="create" />
      <label>ชื่อ</label><input name="name" required />
      <label>คำอธิบาย</label><textarea name="description"></textarea>
      <div class="admin-form__row">
        <div><label>เริ่ม</label><input type="date" name="start_date" /></div>
        <div><label>สิ้นสุด</label><input type="date" name="end_date" /></div>
      </div>
      <label>สถานะ</label><select name="status"><option value="draft">draft</option><option value="active">active</option><option value="ended">ended</option></select>
      <button class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>
<div class="admin-card">
  <div class="admin-card__head"><h2>แคมเปญ</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>ชื่อ</th><th>ช่วงเวลา</th><th>สถานะ</th><th>แก้ไข</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['name']) ?></td>
            <td><?= h($r['start_date'] ?? '-') ?> — <?= h($r['end_date'] ?? '-') ?></td>
            <td><?= h($r['status']) ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('campaign-edit-' . $r['id']) ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('campaign-edit-' . $r['id'], 'แก้ไขแคมเปญ — ' . $r['name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <label>ชื่อ</label><input name="name" value="<?= h($r['name']) ?>" />
  <label>คำอธิบาย</label><textarea name="description" rows="3"><?= h($r['description'] ?? '') ?></textarea>
  <div class="admin-form__row">
    <div><label>เริ่ม</label><input type="date" name="start_date" value="<?= h($r['start_date'] ?? '') ?>" /></div>
    <div><label>สิ้นสุด</label><input type="date" name="end_date" value="<?= h($r['end_date'] ?? '') ?>" /></div>
  </div>
  <label>สถานะ</label>
  <select name="status">
    <option value="draft">draft</option>
    <option value="active"<?= $r['status'] === 'active' ? ' selected' : '' ?>>active</option>
    <option value="ended"<?= $r['status'] === 'ended' ? ' selected' : '' ?>>ended</option>
  </select>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('แคมเปญ', ob_get_clean(), 'campaigns');
