<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        db()->prepare('INSERT INTO leads (name, phone, interest, message, source, plan_ref, status) VALUES (?,?,?,?,?,?,?)')
            ->execute([
                trim($_POST['name']), trim($_POST['phone']),
                trim($_POST['interest'] ?? '') ?: null,
                trim($_POST['message'] ?? '') ?: null,
                $_POST['source'] ?? 'manual',
                trim($_POST['plan_ref'] ?? '') ?: null,
                'new',
            ]);
        flash_set('success', 'เพิ่ม Lead แล้ว');
    }
    if ($action === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        db()->prepare('UPDATE leads SET status=?, notes=?, assigned_agent_id=? WHERE id=?')->execute([
            $_POST['status'],
            trim($_POST['notes'] ?? '') ?: null,
            $_POST['assigned_agent_id'] ?: null,
            $id,
        ]);
        log_activity((int) $admin['id'], 'update_lead', 'lead', $id);
        flash_set('success', 'อัปเดต Lead แล้ว');
    }
    header('Location: leads.php' . ($statusFilter = $_GET['status'] ?? '' ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$sql = 'SELECT l.*, a.name AS agent_name FROM leads l LEFT JOIN agents a ON a.id = l.assigned_agent_id WHERE 1=1';
$params = [];
if ($statusFilter) { $sql .= ' AND l.status = ?'; $params[] = $statusFilter; }
$sql .= ' ORDER BY l.created_at DESC LIMIT 300';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$agents = db()->query('SELECT id, name FROM agents WHERE status="active" ORDER BY name')->fetchAll();
$labels = status_labels()['lead'];

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head">
    <h2>Lead / สอบถาม (<?= count($rows) ?>)</h2>
    <form method="get" class="admin-filters">
      <select name="status" onchange="this.form.submit()">
        <option value="">ทุกสถานะ</option>
        <?php foreach ($labels as $k=>$v): ?><option value="<?= h($k) ?>"<?= $statusFilter===$k?' selected':'' ?>><?= h($v) ?></option><?php endforeach; ?>
      </select>
    </form>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>วันที่</th><th>ชื่อ</th><th>สนใจ</th><th>แหล่งที่มา</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><small><?= h(substr($r['created_at'], 0, 16)) ?></small></td>
            <td><?= h($r['name']) ?><br><small><?= h($r['phone']) ?></small>
              <?php if ($r['message']): ?><br><small class="text-muted"><?= h(mb_strimwidth($r['message'], 0, 80, '…')) ?></small><?php endif; ?>
            </td>
            <td><?= h($r['interest'] ?: '-') ?><?php if ($r['plan_ref']): ?><br><small>แผน: <?= h($r['plan_ref']) ?></small><?php endif; ?></td>
            <td><?= h($r['source']) ?></td>
            <td><?= status_badge('lead', $r['status']) ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('lead-edit-' . $r['id'], 'อัปเดต') ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="6" class="admin-empty">ยังไม่มี Lead — จะเข้ามาจากฟอร์มติดต่ออัตโนมัติ</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('lead-edit-' . $r['id'], 'อัปเดต Lead — ' . $r['name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <?php if ($r['message']): ?><p class="text-muted" style="margin:0 0 12px;"><?= h($r['message']) ?></p><?php endif; ?>
  <label>สถานะ</label>
  <select name="status"><?php foreach ($labels as $k => $v): ?><option value="<?= h($k) ?>"<?= $r['status'] === $k ? ' selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
  <label>มอบหมายตัวแทน</label>
  <select name="assigned_agent_id"><option value="">--</option><?php foreach ($agents as $a): ?><option value="<?= $a['id'] ?>"<?= (int) $r['assigned_agent_id'] === (int) $a['id'] ? ' selected' : '' ?>><?= h($a['name']) ?></option><?php endforeach; ?></select>
  <label>หมายเหตุ</label><textarea name="notes" rows="3"><?= h($r['notes'] ?? '') ?></textarea>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('Lead / สอบถาม', ob_get_clean(), 'leads');
