<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        db()->prepare('INSERT INTO agent_applications (name, phone, email, education, experience, status) VALUES (?,?,?,?,?,?)')
            ->execute([
                trim($_POST['name']), trim($_POST['phone']),
                trim($_POST['email'] ?? '') ?: null,
                trim($_POST['education'] ?? '') ?: null,
                trim($_POST['experience'] ?? '') ?: null,
                'submitted',
            ]);
        flash_set('success', 'เพิ่มใบสมัครแล้ว');
    }
    if ($action === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        $stmt = db()->prepare('SELECT status, agent_id FROM agent_applications WHERE id = ?');
        $stmt->execute([$id]);
        $before = $stmt->fetch();
        db()->prepare('UPDATE agent_applications SET status=?, notes=?, assigned_admin_id=? WHERE id=?')
            ->execute([$_POST['status'], trim($_POST['notes'] ?? '') ?: null, $admin['id'], $id]);
        if ($_POST['status'] === 'approved' && $before && $before['status'] !== 'approved' && empty($before['agent_id'])) {
            $stmt = db()->prepare('SELECT * FROM agent_applications WHERE id = ?');
            $stmt->execute([$id]);
            $app = $stmt->fetch();
            if ($app) {
                $tier = db()->query("SELECT id FROM agent_tiers WHERE code='trainee' LIMIT 1")->fetch();
                db()->prepare('INSERT INTO agents (name, phone, email, tier_id, status, joined_at) VALUES (?,?,?,?,?,CURDATE())')
                    ->execute([$app['name'], $app['phone'], $app['email'], $tier['id'] ?? null, 'active']);
                $agentId = (int) db()->lastInsertId();
                db()->prepare('UPDATE agent_applications SET agent_id = ? WHERE id = ?')->execute([$agentId, $id]);
            }
        }
        log_activity((int) $admin['id'], 'update_application', 'application', $id, $_POST['status']);
        flash_set('success', 'อัปเดตใบสมัครแล้ว');
    }
    header('Location: applications.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$sql = 'SELECT * FROM agent_applications WHERE 1=1';
$params = [];
if ($statusFilter) { $sql .= ' AND status = ?'; $params[] = $statusFilter; }
$sql .= ' ORDER BY created_at DESC LIMIT 200';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$labels = status_labels()['application'];

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head">
    <h2>ใบสมัครตัวแทน (<?= count($rows) ?>)</h2>
    <form method="get" class="admin-filters">
      <select name="status" onchange="this.form.submit()">
        <option value="">ทุกสถานะ</option>
        <?php foreach ($labels as $k=>$v): ?><option value="<?= h($k) ?>"<?= $statusFilter===$k?' selected':'' ?>><?= h($v) ?></option><?php endforeach; ?>
      </select>
    </form>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>วันที่</th><th>ชื่อ</th><th>ติดต่อ</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><small><?= h(substr($r['created_at'], 0, 10)) ?></small></td>
            <td><?= h($r['name']) ?></td>
            <td><?= h($r['phone']) ?><br><small><?= h($r['email'] ?? '') ?></small></td>
            <td><?= status_badge('application', $r['status']) ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('app-edit-' . $r['id'], 'อัปเดต') ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('app-edit-' . $r['id'], 'อัปเดตใบสมัคร — ' . $r['name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <?php if ($r['education']): ?><p class="text-muted" style="margin:0 0 8px;">การศึกษา: <?= h($r['education']) ?></p><?php endif; ?>
  <?php if ($r['experience']): ?><p class="text-muted" style="margin:0 0 12px;"><?= h($r['experience']) ?></p><?php endif; ?>
  <label>สถานะ</label>
  <select name="status"><?php foreach ($labels as $k => $v): ?><option value="<?= h($k) ?>"<?= $r['status'] === $k ? ' selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
  <label>หมายเหตุ</label><textarea name="notes" rows="3"><?= h($r['notes'] ?? '') ?></textarea>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('ใบสมัครตัวแทน', ob_get_clean(), 'applications');
