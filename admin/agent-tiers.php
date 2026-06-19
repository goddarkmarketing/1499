<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($id = (int) ($_POST['id'] ?? 0))) {
    db()->prepare('UPDATE agent_tiers SET name=?, description=?, min_policies=?, min_premium=?, sort_order=?, status=? WHERE id=?')
        ->execute([
            trim($_POST['name']),
            trim($_POST['description'] ?? '') ?: null,
            $_POST['min_policies'] !== '' ? (int) $_POST['min_policies'] : null,
            $_POST['min_premium'] !== '' ? (float) $_POST['min_premium'] : null,
            (int) $_POST['sort_order'],
            $_POST['status'],
            $id,
        ]);
    flash_set('success', 'อัปเดตแล้ว');
    header('Location: agent-tiers.php');
    exit;
}

$rows = db()->query('SELECT * FROM agent_tiers ORDER BY sort_order')->fetchAll();
ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>ระดับตัวแทน</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>รหัส</th><th>ชื่อ</th><th>กรมธรรม์ขั้นต่ำ</th><th>เบี้ยขั้นต่ำ</th><th>แก้ไข</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['code']) ?></td>
            <td><?= h($r['name']) ?><br><small><?= h($r['description'] ?? '') ?></small></td>
            <td><?= $r['min_policies'] ?? '-' ?></td>
            <td><?= $r['min_premium'] ? number_format((float)$r['min_premium']) : '-' ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('agent-tier-edit-' . $r['id']) ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('agent-tier-edit-' . $r['id'], 'แก้ไขระดับ — ' . $r['name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <label>ชื่อ</label><input name="name" value="<?= h($r['name']) ?>" />
  <label>คำอธิบาย</label><textarea name="description" rows="3"><?= h($r['description'] ?? '') ?></textarea>
  <div class="admin-form__row">
    <div><label>กรมธรรม์ขั้นต่ำ</label><input type="number" name="min_policies" value="<?= h((string) $r['min_policies']) ?>" /></div>
    <div><label>เบี้ยขั้นต่ำ</label><input type="number" name="min_premium" value="<?= h((string) $r['min_premium']) ?>" /></div>
  </div>
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
<?php render_layout('ระดับตัวแทน', ob_get_clean(), 'agent-tiers');
