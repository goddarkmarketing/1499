<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header('Location: members.php');
    exit;
}

$stmt = db()->prepare(
    'SELECT m.*, mt.name AS tier_name FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id WHERE m.id = ?'
);
$stmt->execute([$id]);
$member = $stmt->fetch();
if (!$member) {
    flash_set('error', 'ไม่พบสมาชิก');
    header('Location: members.php');
    exit;
}

$quotas = db()->prepare(
    'SELECT g.name, g.code, mgq.plays_remaining FROM member_game_quotas mgq
     JOIN games g ON g.id = mgq.game_id WHERE mgq.member_id = ?'
);
$quotas->execute([$id]);
$gameQuotas = $quotas->fetchAll();

$plays = db()->prepare(
    'SELECT s.created_at, g.name AS game_name, p.name AS prize_name
     FROM spin_logs s LEFT JOIN games g ON g.id = s.game_id JOIN prizes p ON p.id = s.prize_id
     WHERE s.member_id = ? ORDER BY s.created_at DESC LIMIT 20'
);
$plays->execute([$id]);
$playRows = $plays->fetchAll();

$rewards = db()->prepare(
    'SELECT rc.created_at, rc.status, p.name AS prize_name FROM reward_claims rc
     JOIN prizes p ON p.id = rc.prize_id WHERE rc.member_id = ? ORDER BY rc.created_at DESC LIMIT 20'
);
$rewards->execute([$id]);
$rewardRows = $rewards->fetchAll();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head">
    <h2><?= h($member['name']) ?></h2>
    <a href="members.php" class="admin-btn admin-btn--ghost admin-btn--sm">← กลับรายชื่อ</a>
  </div>
  <div class="admin-card__body">
    <div class="admin-form__row">
      <div><strong>เบอร์</strong><br><?= h($member['phone']) ?></div>
      <div><strong>อีเมล</strong><br><?= h($member['email'] ?? '-') ?></div>
    </div>
    <div class="admin-form__row" style="margin-top:16px;">
      <div><strong>ระดับ</strong><br><?= h($member['tier_name'] ?? '-') ?></div>
      <div><strong>สถานะ</strong><br><?= status_badge('member', $member['status']) ?></div>
    </div>
    <?php if ($gameQuotas): ?>
      <h3 style="margin:24px 0 8px;font-size:1rem;">สิทธิ์เล่นต่อเกม</h3>
      <ul><?php foreach ($gameQuotas as $q): ?><li><?= h($q['name']) ?>: <?= (int) $q['plays_remaining'] ?> ครั้ง</li><?php endforeach; ?></ul>
    <?php else: ?>
      <p class="text-muted" style="margin-top:16px;">สิทธิ์หมุนคงเหลือ: <?= (int) $member['spins_remaining'] ?> ครั้ง</p>
    <?php endif; ?>
  </div>
</div>

<div class="admin-split">
  <div class="admin-card">
    <div class="admin-card__head"><h2>ประวัติการเล่นล่าสุด</h2></div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>วันที่</th><th>เกม</th><th>รางวัล</th></tr></thead>
        <tbody>
          <?php foreach ($playRows as $r): ?>
            <tr><td><small><?= h(substr($r['created_at'], 0, 16)) ?></small></td><td><?= h($r['game_name'] ?? '-') ?></td><td><?= h($r['prize_name']) ?></td></tr>
          <?php endforeach; ?>
          <?php if (!$playRows): ?><tr><td colspan="3" class="admin-empty">ยังไม่มี</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="admin-card">
    <div class="admin-card__head"><h2>รางวัล</h2></div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>วันที่</th><th>รางวัล</th><th>สถานะ</th></tr></thead>
        <tbody>
          <?php foreach ($rewardRows as $r): ?>
            <tr><td><small><?= h(substr($r['created_at'], 0, 16)) ?></small></td><td><?= h($r['prize_name']) ?></td><td><?= status_badge('reward', $r['status']) ?></td></tr>
          <?php endforeach; ?>
          <?php if (!$rewardRows): ?><tr><td colspan="3" class="admin-empty">ยังไม่มี</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
render_layout('รายละเอียดสมาชิก', ob_get_clean(), 'members');
