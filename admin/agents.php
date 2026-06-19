<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'create') {
        db()->prepare('INSERT INTO agents (code, name, phone, email, tier_id, license_no, status, joined_at) VALUES (?,?,?,?,?,?,?,?)')
            ->execute([
                trim($_POST['code'] ?? '') ?: null,
                trim($_POST['name']),
                trim($_POST['phone']),
                trim($_POST['email'] ?? '') ?: null,
                $_POST['tier_id'] ?: null,
                trim($_POST['license_no'] ?? '') ?: null,
                $_POST['status'] ?? 'active',
                $_POST['joined_at'] ?: null,
            ]);
        flash_set('success', 'เพิ่มตัวแทนแล้ว');
    }
    if (($_POST['action'] ?? '') === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        db()->prepare('UPDATE agents SET code=?, name=?, phone=?, email=?, tier_id=?, license_no=?, status=?, notes=? WHERE id=?')
            ->execute([
                trim($_POST['code'] ?? '') ?: null,
                trim($_POST['name']), trim($_POST['phone']),
                trim($_POST['email'] ?? '') ?: null,
                $_POST['tier_id'] ?: null,
                trim($_POST['license_no'] ?? '') ?: null,
                $_POST['status'], trim($_POST['notes'] ?? '') ?: null, $id,
            ]);
        flash_set('success', 'อัปเดตตัวแทนแล้ว');
    }
    header('Location: agents.php');
    exit;
}

$rows = db()->query(
    'SELECT a.*, at.name AS tier_name FROM agents a LEFT JOIN agent_tiers at ON at.id = a.tier_id ORDER BY a.created_at DESC'
)->fetchAll();
$tiers = db()->query('SELECT id, name FROM agent_tiers ORDER BY sort_order')->fetchAll();
$labels = status_labels()['agent'];

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มตัวแทน</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form">
      <input type="hidden" name="action" value="create" />
      <div class="admin-form__row">
        <div><label>ชื่อ</label><input name="name" required /></div>
        <div><label>เบอร์</label><input name="phone" required /></div>
      </div>
      <div class="admin-form__row">
        <div><label>รหัสตัวแทน</label><input name="code" /></div>
        <div><label>ระดับ</label><select name="tier_id"><option value="">--</option><?php foreach ($tiers as $t): ?><option value="<?= $t['id'] ?>"><?= h($t['name']) ?></option><?php endforeach; ?></select></div>
      </div>
      <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>
<div class="admin-card">
  <div class="admin-card__head"><h2>ตัวแทนทั้งหมด</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>รหัส</th><th>ชื่อ</th><th>เบอร์</th><th>ระดับ</th><th>สถานะ</th><th>แก้ไข</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['code'] ?? '-') ?></td>
            <td><?= h($r['name']) ?></td>
            <td><?= h($r['phone']) ?></td>
            <td><?= h($r['tier_name'] ?? '-') ?></td>
            <td><?= status_badge('agent', $r['status']) ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('agent-edit-' . $r['id']) ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('agent-edit-' . $r['id'], 'แก้ไขตัวแทน — ' . $r['name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <div class="admin-form__row">
    <div><label>ชื่อ</label><input name="name" value="<?= h($r['name']) ?>" /></div>
    <div><label>เบอร์</label><input name="phone" value="<?= h($r['phone']) ?>" /></div>
  </div>
  <label>ระดับ</label>
  <select name="tier_id"><option value="">--</option><?php foreach ($tiers as $t): ?><option value="<?= $t['id'] ?>"<?= (int) $r['tier_id'] === (int) $t['id'] ? ' selected' : '' ?>><?= h($t['name']) ?></option><?php endforeach; ?></select>
  <label>สถานะ</label>
  <select name="status"><?php foreach ($labels as $k => $v): ?><option value="<?= h($k) ?>"<?= $r['status'] === $k ? ' selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
  <label>หมายเหตุ</label><textarea name="notes" rows="3"><?= h($r['notes'] ?? '') ?></textarea>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('ตัวแทน', ob_get_clean(), 'agents');
