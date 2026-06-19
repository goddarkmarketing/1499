<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($id = (int) ($_POST['id'] ?? 0))) {
    $status = $_POST['status'] ?? '';
    $notes = trim($_POST['notes'] ?? '') ?: null;
    $extra = '';
    $params = [$status, $notes];
    if ($status === 'approved') { $extra = ', approved_at = NOW()'; }
    if ($status === 'sent') { $extra = ', sent_at = NOW()'; }
    if ($status === 'redeemed') { $extra = ', redeemed_at = NOW()'; }
    $params[] = $id;
    db()->prepare("UPDATE reward_claims SET status = ?, notes = ?{$extra} WHERE id = ?")->execute($params);
    log_activity((int) $admin['id'], 'update_reward_status', 'reward_claim', $id, $status);
    flash_set('success', 'อัปเดตสถานะรางวัลแล้ว');
    header('Location: rewards.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$sql = 'SELECT rc.*, m.name AS member_name, m.phone, p.name AS prize_name
        FROM reward_claims rc
        JOIN members m ON m.id = rc.member_id
        JOIN prizes p ON p.id = rc.prize_id WHERE 1=1';
$params = [];
if ($statusFilter) { $sql .= ' AND rc.status = ?'; $params[] = $statusFilter; }
$sql .= ' ORDER BY rc.created_at DESC LIMIT 300';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$labels = status_labels()['reward'];

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head">
    <h2>สถานะของรางวัลลูกค้า (<?= count($rows) ?>)</h2>
    <form method="get" class="admin-filters">
      <select name="status" onchange="this.form.submit()">
        <option value="">ทุกสถานะ</option>
        <?php foreach ($labels as $k=>$v): ?><option value="<?= h($k) ?>"<?= $statusFilter===$k?' selected':'' ?>><?= h($v) ?></option><?php endforeach; ?>
      </select>
    </form>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>วันที่</th><th>ลูกค้า</th><th>รางวัล</th><th>สถานะ</th><th>อัปเดต</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><small><?= h(substr($r['created_at'], 0, 16)) ?></small></td>
            <td><?= h($r['member_name']) ?><br><small><?= h($r['phone']) ?></small></td>
            <td><?= h($r['prize_name']) ?></td>
            <td><?= status_badge('reward', $r['status']) ?>
              <?php if ($r['notes']): ?><br><small><?= h($r['notes']) ?></small><?php endif; ?>
            </td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('reward-edit-' . $r['id'], 'อัปเดต') ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="5" class="admin-empty">ยังไม่มีรางวัล — จะปรากฏเมื่อลูกค้าหมุนวงล้อ</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('reward-edit-' . $r['id'], 'อัปเดตรางวัล — ' . $r['member_name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <p class="text-muted" style="margin:0 0 12px;">รางวัล: <strong><?= h($r['prize_name']) ?></strong></p>
  <label>สถานะ</label>
  <select name="status"><?php foreach ($labels as $k => $v): ?><option value="<?= h($k) ?>"<?= $r['status'] === $k ? ' selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
  <label>หมายเหตุ</label><textarea name="notes" rows="3" placeholder="หมายเหตุ"><?= h($r['notes'] ?? '') ?></textarea>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('สถานะรางวัลลูกค้า', ob_get_clean(), 'rewards');
