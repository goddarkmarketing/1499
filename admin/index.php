<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

function count_table(string $table, string $where = '1=1'): int {
    return (int) db()->query("SELECT COUNT(*) FROM {$table} WHERE {$where}")->fetchColumn();
}

$stats = [
    ['สมาชิกทั้งหมด', count_table('members'), '', 'users', 'default'],
    ['Lead ใหม่', count_table('leads', "status = 'new'"), 'warn', 'message-square', 'warn'],
    ['ใบสมัครรอดำเนินการ', count_table('agent_applications', "status NOT IN ('approved','rejected','cancelled')"), 'warn', 'file-text', 'warn'],
    ['รางวัลรอตรวจสอบ', count_table('reward_claims', "status IN ('won','pending_verify')"), 'warn', 'package-check', 'warn'],
    ['การเล่นวันนี้', count_table('spin_logs', 'DATE(created_at) = CURDATE()'), '', 'history', 'muted'],
    ['ของรางวัลเปิดใช้', count_table('prizes', "status = 'active'"), '', 'gift', 'success'],
];

$recentLeads = db()->query(
    'SELECT id, name, phone, interest, status, created_at FROM leads ORDER BY created_at DESC LIMIT 5'
)->fetchAll();
$recentRewards = db()->query(
    'SELECT rc.id, rc.status, rc.created_at, m.name AS member_name, p.name AS prize_name
     FROM reward_claims rc
     JOIN members m ON m.id = rc.member_id
     JOIN prizes p ON p.id = rc.prize_id
     ORDER BY rc.created_at DESC LIMIT 5'
)->fetchAll();

ob_start();
?>
<?php if (admin_can($admin, ['super_admin', 'ops'])): ?>
<?php
$lowStock = (int) (setting_get('low_stock_threshold', '5') ?: 5);
$lowStmt = db()->prepare('SELECT COUNT(*) FROM prizes WHERE stock IS NOT NULL AND stock <= ? AND status = "active"');
$lowStmt->execute([$lowStock]);
$lowCount = (int) $lowStmt->fetchColumn();
if ($lowCount > 0):
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>แจ้งเตือน</h2><a href="settings.php" class="admin-btn admin-btn--ghost admin-btn--sm">ตั้งค่า</a></div>
  <div class="admin-card__body">
    <p style="margin:0;">มีของรางวัล <strong><?= $lowCount ?></strong> รายการใกล้หมดสต็อก — <a href="settings.php">ดูรายละเอียด</a></p>
  </div>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="admin-grid">
  <?php foreach ($stats as [$label, $val, $type, $icon, $tone]): ?>
    <div class="admin-stat admin-stat--<?= h($tone ?: 'default') ?>">
      <div class="admin-stat__icon">
        <i data-lucide="<?= h($icon) ?>" aria-hidden="true"></i>
      </div>
      <div class="admin-stat__body">
        <div class="admin-stat__label"><?= h($label) ?></div>
        <div class="admin-stat__value<?= $type === 'warn' && $val > 0 ? ' admin-stat__value--warn' : '' ?>"><?= (int) $val ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="admin-split">
  <div class="admin-card">
    <div class="admin-card__head">
      <h2>Lead ล่าสุด</h2>
      <a href="leads.php" class="admin-btn admin-btn--ghost admin-btn--sm">ดูทั้งหมด</a>
    </div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>ชื่อ</th><th>สนใจ</th><th>สถานะ</th></tr></thead>
        <tbody>
          <?php foreach ($recentLeads as $row): ?>
            <tr>
              <td><?= h($row['name']) ?><small><?= h($row['phone']) ?></small></td>
              <td><?= h($row['interest'] ?: '-') ?></td>
              <td><?= status_badge('lead', $row['status']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$recentLeads): ?><tr><td colspan="3" class="admin-empty">ยังไม่มีข้อมูล</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="admin-card">
    <div class="admin-card__head">
      <h2>รางวัลล่าสุด</h2>
      <a href="rewards.php" class="admin-btn admin-btn--ghost admin-btn--sm">ดูทั้งหมด</a>
    </div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>ลูกค้า</th><th>รางวัล</th><th>สถานะ</th></tr></thead>
        <tbody>
          <?php foreach ($recentRewards as $row): ?>
            <tr>
              <td><?= h($row['member_name']) ?></td>
              <td><?= h($row['prize_name']) ?></td>
              <td><?= status_badge('reward', $row['status']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$recentRewards): ?><tr><td colspan="3" class="admin-empty">ยังไม่มีข้อมูล</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
render_layout('Dashboard', $content, 'dashboard');
