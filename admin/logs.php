<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();
admin_require_roles($admin, ['super_admin', 'ops']);

$rows = db()->query(
    'SELECT l.*, a.name AS admin_name FROM activity_logs l
     LEFT JOIN admin_users a ON a.id = l.admin_id
     ORDER BY l.created_at DESC LIMIT 500'
)->fetchAll();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>ประวัติการทำงาน (<?= count($rows) ?>)</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>วันที่</th><th>ผู้ดูแล</th><th>การกระทำ</th><th>รายละเอียด</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><small><?= h(substr($r['created_at'], 0, 16)) ?></small></td>
            <td><?= h($r['admin_name'] ?? 'ระบบ') ?></td>
            <td><code><?= h($r['action']) ?></code>
              <?php if ($r['entity_type']): ?><br><small class="text-muted"><?= h($r['entity_type']) ?> #<?= (int) $r['entity_id'] ?></small><?php endif; ?>
            </td>
            <td><small><?= h($r['detail'] ?? '-') ?></small></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4" class="admin-empty">ยังไม่มี log</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
render_layout('ประวัติระบบ', ob_get_clean(), 'logs');
