<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();
admin_nav_seen_mark('plays');

$gameFilter = $_GET['game_id'] ?? '';
$campaignFilter = $_GET['campaign_id'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$search = trim($_GET['q'] ?? '');

$sql = 'SELECT s.*, m.name AS member_name, m.phone, p.name AS prize_name,
               g.name AS game_name, g.code AS game_code,
               c.name AS campaign_name,
               rc.status AS reward_status
        FROM spin_logs s
        JOIN members m ON m.id = s.member_id
        JOIN prizes p ON p.id = s.prize_id
        LEFT JOIN games g ON g.id = s.game_id
        LEFT JOIN campaigns c ON c.id = s.campaign_id
        LEFT JOIN reward_claims rc ON rc.spin_log_id = s.id
        WHERE 1=1';
$params = [];

if ($gameFilter !== '') {
    $sql .= ' AND s.game_id = ?';
    $params[] = (int) $gameFilter;
}
if ($campaignFilter !== '') {
    $sql .= ' AND s.campaign_id = ?';
    $params[] = (int) $campaignFilter;
}
if ($dateFrom !== '') {
    $sql .= ' AND DATE(s.created_at) >= ?';
    $params[] = $dateFrom;
}
if ($dateTo !== '') {
    $sql .= ' AND DATE(s.created_at) <= ?';
    $params[] = $dateTo;
}
if ($search !== '') {
    $sql .= ' AND (m.name LIKE ? OR m.phone LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
}

$sql .= ' ORDER BY s.created_at DESC LIMIT 500';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$games = game_list();
$campaigns = db()->query('SELECT id, name FROM campaigns ORDER BY created_at DESC')->fetchAll();
$rewardLabels = status_labels()['reward'];

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head">
    <h2>ประวัติการเล่น (<?= count($rows) ?>)</h2>
    <form method="get" class="admin-filters">
      <select name="game_id" onchange="this.form.submit()">
        <option value="">ทุกเกม</option>
        <?php foreach ($games as $g): ?>
          <option value="<?= (int) $g['id'] ?>"<?= (string) $gameFilter === (string) $g['id'] ? ' selected' : '' ?>><?= h($g['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="campaign_id" onchange="this.form.submit()">
        <option value="">ทุกแคมเปญ</option>
        <?php foreach ($campaigns as $c): ?>
          <option value="<?= (int) $c['id'] ?>"<?= (string) $campaignFilter === (string) $c['id'] ? ' selected' : '' ?>><?= h($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="date" name="date_from" value="<?= h($dateFrom) ?>" title="ตั้งแต่" />
      <input type="date" name="date_to" value="<?= h($dateTo) ?>" title="ถึง" />
      <input type="search" name="q" value="<?= h($search) ?>" placeholder="ค้นหาชื่อ/เบอร์" />
      <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">กรอง</button>
      <?php if ($gameFilter || $campaignFilter || $dateFrom || $dateTo || $search): ?>
        <a href="plays.php" class="admin-btn admin-btn--ghost admin-btn--sm">ล้าง</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>วันที่</th>
          <th>ลูกค้า</th>
          <th>เกม</th>
          <th>แคมเปญ</th>
          <th>รางวัล</th>
          <th>สถานะรางวัล</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><small><?= h(substr($r['created_at'], 0, 16)) ?></small></td>
            <td><?= h($r['member_name']) ?><small><?= h($r['phone']) ?></small></td>
            <td><?= h($r['game_name'] ?? 'วงล้อโชคดี') ?></td>
            <td><small><?= h($r['campaign_name'] ?? '-') ?></small></td>
            <td><?= h($r['prize_name']) ?></td>
            <td>
              <?php if ($r['reward_status']): ?>
                <?= status_badge('reward', $r['reward_status']) ?>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td><small><?= h($r['ip_address'] ?? '-') ?></small></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
          <tr><td colspan="7" class="admin-empty">ยังไม่มีประวัติการเล่น</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
render_layout('ประวัติการเล่น', ob_get_clean(), 'plays');
