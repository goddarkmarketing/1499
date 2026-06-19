<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $tierId = $_POST['tier_id'] ?: null;
        $tier = $tierId ? db()->query("SELECT spin_quota FROM member_tiers WHERE id = " . (int) $tierId)->fetch() : null;
        $spins = $tier ? (int) $tier['spin_quota'] : (int) ($_POST['spins_remaining'] ?? 1);
        db()->prepare(
            'INSERT INTO members (name, phone, email, tier_id, status, spins_remaining, notes) VALUES (?,?,?,?,?,?,?)'
        )->execute([
            trim($_POST['name']),
            trim($_POST['phone']),
            trim($_POST['email'] ?? '') ?: null,
            $tierId,
            $_POST['status'] ?? 'active',
            $spins,
            trim($_POST['notes'] ?? '') ?: null,
        ]);
        flash_set('success', 'เพิ่มสมาชิกแล้ว');
    }
    if ($action === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        db()->prepare(
            'UPDATE members SET name=?, phone=?, email=?, tier_id=?, status=?, spins_remaining=?, notes=? WHERE id=?'
        )->execute([
            trim($_POST['name']),
            trim($_POST['phone']),
            trim($_POST['email'] ?? '') ?: null,
            $_POST['tier_id'] ?: null,
            $_POST['status'],
            (int) $_POST['spins_remaining'],
            trim($_POST['notes'] ?? '') ?: null,
            $id,
        ]);
        log_activity((int) $admin['id'], 'update_member', 'member', $id);
        flash_set('success', 'อัปเดตสมาชิกแล้ว');
    }
    header('Location: members.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$sql = 'SELECT m.*, mt.name AS tier_name FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id WHERE 1=1';
$params = [];
if ($statusFilter) {
    $sql .= ' AND m.status = ?';
    $params[] = $statusFilter;
}
$sql .= ' ORDER BY m.created_at DESC LIMIT 200';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$tiers = db()->query('SELECT id, name FROM member_tiers WHERE status="active" ORDER BY sort_order')->fetchAll();
$labels = status_labels()['member'];

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มสมาชิก</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form">
      <input type="hidden" name="action" value="create" />
      <div class="admin-form__row">
        <div><label>ชื่อ</label><input name="name" required /></div>
        <div><label>เบอร์โทร</label><input name="phone" required /></div>
      </div>
      <div class="admin-form__row">
        <div><label>อีเมล</label><input type="email" name="email" /></div>
        <div><label>ระดับ</label>
          <select name="tier_id"><option value="">-- เลือก --</option>
            <?php foreach ($tiers as $t): ?><option value="<?= $t['id'] ?>"><?= h($t['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__head">
    <h2>รายชื่อสมาชิก (<?= count($rows) ?>)</h2>
    <form method="get" class="admin-filters">
      <select name="status" onchange="this.form.submit()">
        <option value="">ทุกสถานะ</option>
        <?php foreach ($labels as $k => $v): ?>
          <option value="<?= h($k) ?>"<?= $statusFilter === $k ? ' selected' : '' ?>><?= h($v) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>ชื่อ</th><th>เบอร์</th><th>ระดับ</th><th>สิทธิ์หมุน</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['name']) ?><br><a href="member-view.php?id=<?= (int) $r['id'] ?>" class="admin-btn admin-btn--ghost admin-btn--sm" style="margin-top:4px;">ดูรายละเอียด</a></td>
            <td><?= h($r['phone']) ?></td>
            <td><?= h($r['tier_name'] ?? '-') ?></td>
            <td><?= (int) $r['spins_remaining'] ?></td>
            <td><?= status_badge('member', $r['status']) ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('member-edit-' . $r['id']) ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('member-edit-' . $r['id'], 'แก้ไขสมาชิก — ' . $r['name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <div class="admin-form__row">
    <div><label>ชื่อ</label><input name="name" value="<?= h($r['name']) ?>" required /></div>
    <div><label>เบอร์</label><input name="phone" value="<?= h($r['phone']) ?>" required /></div>
  </div>
  <label>อีเมล</label><input name="email" value="<?= h($r['email'] ?? '') ?>" />
  <div class="admin-form__row">
    <div>
      <label>ระดับ</label>
      <select name="tier_id"><option value="">--</option>
        <?php foreach ($tiers as $t): ?>
          <option value="<?= $t['id'] ?>"<?= (int) $r['tier_id'] === (int) $t['id'] ? ' selected' : '' ?>><?= h($t['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label>สิทธิ์หมุนคงเหลือ</label><input type="number" name="spins_remaining" min="0" value="<?= (int) $r['spins_remaining'] ?>" /></div>
  </div>
  <label>สถานะ</label>
  <select name="status"><?php foreach ($labels as $k => $v): ?>
    <option value="<?= h($k) ?>"<?= $r['status'] === $k ? ' selected' : '' ?>><?= h($v) ?></option>
  <?php endforeach; ?></select>
  <label>หมายเหตุ</label><textarea name="notes" rows="3"><?= h($r['notes'] ?? '') ?></textarea>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php
render_layout('สมาชิก / ลูกค้า', ob_get_clean(), 'members');
