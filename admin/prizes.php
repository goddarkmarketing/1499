<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

$gameFilter = $_GET['game_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($id = (int) ($_POST['id'] ?? 0))) {
    db()->prepare('UPDATE prizes SET name=?, short_name=?, detail=?, weight=?, stock=?, status=?, wheel_enabled=?, game_id=? WHERE id=?')
        ->execute([
            trim($_POST['name']), trim($_POST['short_name']),
            trim($_POST['detail'] ?? '') ?: null,
            (int) $_POST['weight'],
            $_POST['stock'] !== '' ? (int) $_POST['stock'] : null,
            $_POST['status'], isset($_POST['wheel_enabled']) ? 1 : 0,
            $_POST['game_id'] ?: null,
            $id,
        ]);
    flash_set('success', 'อัปเดตของรางวัลแล้ว');
    $redirect = 'prizes.php' . ($gameFilter !== '' ? '?game_id=' . urlencode((string) $gameFilter) : '');
    header('Location: ' . $redirect);
    exit;
}

$sql = 'SELECT p.*, c.name AS campaign_name, g.name AS game_name
        FROM prizes p
        LEFT JOIN campaigns c ON c.id = p.campaign_id
        LEFT JOIN games g ON g.id = p.game_id
        WHERE 1=1';
$params = [];
if ($gameFilter !== '') {
    $sql .= ' AND p.game_id = ?';
    $params[] = (int) $gameFilter;
}
$sql .= ' ORDER BY p.sort_order';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$games = game_list();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head">
    <h2>ของรางวัล (<?= count($rows) ?>)</h2>
    <form method="get" class="admin-filters">
      <select name="game_id" onchange="this.form.submit()">
        <option value="">ทุกเกม</option>
        <?php foreach ($games as $g): ?>
          <option value="<?= (int) $g['id'] ?>"<?= (string) $gameFilter === (string) $g['id'] ? ' selected' : '' ?>><?= h($g['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>ชื่อ</th><th>เกม</th><th>แคมเปญ</th><th>น้ำหนัก</th><th>สต็อก</th><th>วงล้อ</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><strong><?= h($r['name']) ?></strong><br><small><?= h($r['short_name']) ?></small></td>
            <td><?= h($r['game_name'] ?? '-') ?></td>
            <td><?= h($r['campaign_name'] ?? '-') ?></td>
            <td><?= (int) $r['weight'] ?></td>
            <td><?= $r['stock'] ?? '∞' ?></td>
            <td><?= $r['wheel_enabled'] ? '✓' : '-' ?></td>
            <td><?= h($r['status']) ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('prize-edit-' . $r['id']) ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="8" class="admin-empty">ไม่พบของรางวัล</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('prize-edit-' . $r['id'], 'แก้ไขของรางวัล — ' . $r['name']); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <label>เกม</label>
  <select name="game_id">
    <option value="">-- ไม่ระบุ --</option>
    <?php foreach ($games as $g): ?>
      <option value="<?= $g['id'] ?>"<?= (int) $r['game_id'] === (int) $g['id'] ? ' selected' : '' ?>><?= h($g['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <div class="admin-form__row">
    <div><label>ชื่อ</label><input name="name" value="<?= h($r['name']) ?>" /></div>
    <div><label>ชื่อสั้น</label><input name="short_name" value="<?= h($r['short_name']) ?>" /></div>
  </div>
  <label>รายละเอียด</label>
  <textarea name="detail" rows="3"><?= h($r['detail'] ?? '') ?></textarea>
  <div class="admin-form__row">
    <div><label>น้ำหนัก (โอกาส)</label><input type="number" name="weight" value="<?= (int) $r['weight'] ?>" /></div>
    <div><label>สต็อก (ว่าง=ไม่จำกัด)</label><input type="number" name="stock" value="<?= h((string) $r['stock']) ?>" /></div>
  </div>
  <label>สถานะ</label>
  <select name="status">
    <option value="active">active</option>
    <option value="inactive"<?= $r['status'] === 'inactive' ? ' selected' : '' ?>>inactive</option>
    <option value="out_of_stock"<?= $r['status'] === 'out_of_stock' ? ' selected' : '' ?>>out_of_stock</option>
  </select>
  <label class="admin-check"><input type="checkbox" name="wheel_enabled" value="1"<?= $r['wheel_enabled'] ? ' checked' : '' ?> /> แสดงบนวงล้อ</label>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php
render_layout('ของรางวัล', ob_get_clean(), 'prizes');
