<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();
admin_require_roles($admin, ['super_admin', 'ops']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        db()->prepare(
            'INSERT INTO insurance_categories (slug, title, tagline, icon, detail_json, sort_order, status) VALUES (?,?,?,?,?,?,?)'
        )->execute([
            trim($_POST['slug']),
            trim($_POST['title']),
            trim($_POST['tagline'] ?? '') ?: null,
            trim($_POST['icon'] ?? '') ?: 'shield',
            trim($_POST['detail_json'] ?? '') ?: null,
            (int) ($_POST['sort_order'] ?? 0),
            $_POST['status'] ?? 'active',
        ]);
        log_activity((int) $admin['id'], 'create_insurance_category');
        flash_set('success', 'เพิ่มหมวดประกันแล้ว');
    }
    if ($action === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        db()->prepare(
            'UPDATE insurance_categories SET slug=?, title=?, tagline=?, icon=?, detail_json=?, sort_order=?, status=? WHERE id=?'
        )->execute([
            trim($_POST['slug']),
            trim($_POST['title']),
            trim($_POST['tagline'] ?? '') ?: null,
            trim($_POST['icon'] ?? '') ?: 'shield',
            trim($_POST['detail_json'] ?? '') ?: null,
            (int) ($_POST['sort_order'] ?? 0),
            $_POST['status'] ?? 'active',
            $id,
        ]);
        log_activity((int) $admin['id'], 'update_insurance_category', 'insurance_category', $id);
        flash_set('success', 'อัปเดตหมวดประกันแล้ว');
    }
    header('Location: insurance-categories.php');
    exit;
}

$rows = db()->query(
    'SELECT c.*, (SELECT COUNT(*) FROM insurance_plans p WHERE p.category_id = c.id) AS plan_count
     FROM insurance_categories c ORDER BY c.sort_order, c.id'
)->fetchAll();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มหมวดประกัน</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form admin-form--wide">
      <input type="hidden" name="action" value="create" />
      <div class="admin-form__row">
        <div><label>slug (URL)</label><input name="slug" required placeholder="health" /></div>
        <div><label>ชื่อหมวด</label><input name="title" required /></div>
      </div>
      <label>คำโปรย</label><input name="tagline" />
      <div class="admin-form__row">
        <div><label>ไอคอน (Lucide)</label><input name="icon" placeholder="heart-pulse" /></div>
        <div><label>ลำดับ</label><input type="number" name="sort_order" value="0" /></div>
      </div>
      <label>รายละเอียดหมวด (JSON: facts, exclusions, faq)</label>
      <textarea name="detail_json" class="is-tall" placeholder='{"facts":{"age":"0-65 ปี"},"exclusions":[],"faq":[]}'></textarea>
      <label>สถานะ</label><select name="status"><option value="active">active</option><option value="inactive">inactive</option></select>
      <button class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__head"><h2>หมวดประกัน (<?= count($rows) ?>)</h2><a href="insurance-plans.php" class="admin-btn admin-btn--ghost admin-btn--sm">จัดการแผน →</a></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>หมวด</th><th>แผน</th><th>สถานะ</th><th>แก้ไข</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['title']) ?><br><small>#<?= h($r['slug']) ?></small></td>
            <td><?= (int) $r['plan_count'] ?> แผน</td>
            <td><?= h($r['status']) ?></td>
            <td><div class="admin-actions"><?= admin_edit_button('cat-edit-' . $r['id']) ?></div></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4" class="admin-empty">ยังไม่มี — รัน migrate-v3.php</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('cat-edit-' . $r['id'], 'แก้ไขหมวด — ' . $r['title'], true); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <div class="admin-form__row">
    <div><label>slug</label><input name="slug" value="<?= h($r['slug']) ?>" /></div>
    <div><label>ชื่อ</label><input name="title" value="<?= h($r['title']) ?>" /></div>
  </div>
  <label>คำโปรย</label><input name="tagline" value="<?= h($r['tagline'] ?? '') ?>" />
  <div class="admin-form__row">
    <div><label>ไอคอน</label><input name="icon" value="<?= h($r['icon'] ?? '') ?>" /></div>
    <div><label>ลำดับ</label><input type="number" name="sort_order" value="<?= (int) $r['sort_order'] ?>" /></div>
  </div>
  <label>รายละเอียดหมวด (JSON)</label>
  <textarea name="detail_json" class="is-tall"><?= h($r['detail_json'] ?? '') ?></textarea>
  <label>สถานะ</label>
  <select name="status">
    <option value="active"<?= $r['status'] === 'active' ? ' selected' : '' ?>>active</option>
    <option value="inactive"<?= $r['status'] === 'inactive' ? ' selected' : '' ?>>inactive</option>
  </select>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('หมวดประกัน', ob_get_clean(), 'insurance-categories');
