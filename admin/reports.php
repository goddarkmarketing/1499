<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();
admin_require_roles($admin, ['super_admin', 'ops', 'sales_manager', 'support']);

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

$summary = [
    'leads' => 0,
    'plays' => 0,
    'rewards' => 0,
    'members' => 0,
];
$stmt = db()->prepare('SELECT COUNT(*) FROM leads WHERE DATE(created_at) BETWEEN ? AND ?');
$stmt->execute([$from, $to]);
$summary['leads'] = (int) $stmt->fetchColumn();
$stmt = db()->prepare('SELECT COUNT(*) FROM spin_logs WHERE DATE(created_at) BETWEEN ? AND ?');
$stmt->execute([$from, $to]);
$summary['plays'] = (int) $stmt->fetchColumn();
$stmt = db()->prepare('SELECT COUNT(*) FROM reward_claims WHERE DATE(created_at) BETWEEN ? AND ?');
$stmt->execute([$from, $to]);
$summary['rewards'] = (int) $stmt->fetchColumn();
$stmt = db()->prepare('SELECT COUNT(*) FROM members WHERE DATE(created_at) BETWEEN ? AND ?');
$stmt->execute([$from, $to]);
$summary['members'] = (int) $stmt->fetchColumn();

$stmt = db()->prepare(
    'SELECT DATE(created_at) AS d, COUNT(*) AS c FROM spin_logs
     WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY d'
);
$stmt->execute([$from, $to]);
$playsByDay = $stmt->fetchAll();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head">
    <h2>สรุปรายงาน</h2>
    <form method="get" class="admin-filters">
      <input type="date" name="from" value="<?= h($from) ?>" />
      <input type="date" name="to" value="<?= h($to) ?>" />
      <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">กรอง</button>
    </form>
  </div>
  <div class="admin-card__body">
    <div class="admin-grid">
      <div class="admin-stat admin-stat--default"><div class="admin-stat__body"><div class="admin-stat__label">Lead ใหม่</div><div class="admin-stat__value"><?= $summary['leads'] ?></div></div></div>
      <div class="admin-stat admin-stat--muted"><div class="admin-stat__body"><div class="admin-stat__label">สมาชิกใหม่</div><div class="admin-stat__value"><?= $summary['members'] ?></div></div></div>
      <div class="admin-stat admin-stat--success"><div class="admin-stat__body"><div class="admin-stat__label">การเล่น</div><div class="admin-stat__value"><?= $summary['plays'] ?></div></div></div>
      <div class="admin-stat admin-stat--warn"><div class="admin-stat__body"><div class="admin-stat__label">รางวัลที่ออก</div><div class="admin-stat__value"><?= $summary['rewards'] ?></div></div></div>
    </div>
    <?php if ($playsByDay): ?>
      <h3 style="margin:24px 0 12px;font-size:1rem;">การเล่นรายวัน</h3>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>วันที่</th><th>จำนวนครั้ง</th></tr></thead>
          <tbody>
            <?php foreach ($playsByDay as $row): ?>
              <tr><td><?= h($row['d']) ?></td><td><?= (int) $row['c'] ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__head"><h2>Export CSV</h2></div>
  <div class="admin-card__body">
    <p class="text-muted" style="margin:0 0 16px;">ดาวน์โหลดข้อมูลช่วง <?= h($from) ?> ถึง <?= h($to) ?></p>
    <div class="admin-actions">
      <?php foreach (['leads' => 'Lead', 'members' => 'สมาชิก', 'plays' => 'ประวัติการเล่น', 'rewards' => 'รางวัล'] as $t => $label): ?>
        <a href="export.php?type=<?= $t ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>" class="admin-btn admin-btn--ghost admin-btn--sm">Export <?= h($label) ?></a>
      <?php endforeach; ?>
      <?php if (admin_can($admin, ['super_admin'])): ?>
        <a href="backups.php" class="admin-btn admin-btn--primary admin-btn--sm">สำรองข้อมูลเต็มระบบ (ZIP)</a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php
render_layout('รายงาน / Export', ob_get_clean(), 'reports');
