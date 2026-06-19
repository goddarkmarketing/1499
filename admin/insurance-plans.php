<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();
admin_require_roles($admin, ['super_admin', 'ops']);

$cats = db()->query('SELECT id, title, slug FROM insurance_categories ORDER BY sort_order')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        db()->prepare(
            'INSERT INTO insurance_plans (category_id, slug, name, description, image_path, features_json, detail_json, featured, sort_order, status) VALUES (?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            (int) $_POST['category_id'],
            trim($_POST['slug']),
            trim($_POST['name']),
            trim($_POST['description'] ?? '') ?: null,
            trim($_POST['image_path'] ?? '') ?: null,
            trim($_POST['features_json'] ?? '') ?: null,
            trim($_POST['detail_json'] ?? '') ?: null,
            isset($_POST['featured']) ? 1 : 0,
            (int) ($_POST['sort_order'] ?? 0),
            $_POST['status'] ?? 'active',
        ]);
        log_activity((int) $admin['id'], 'create_insurance_plan');
        flash_set('success', 'เพิ่มแผนประกันแล้ว');
    }
    if ($action === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        db()->prepare(
            'UPDATE insurance_plans SET category_id=?, slug=?, name=?, description=?, image_path=?, features_json=?, detail_json=?, featured=?, sort_order=?, status=? WHERE id=?'
        )->execute([
            (int) $_POST['category_id'],
            trim($_POST['slug']),
            trim($_POST['name']),
            trim($_POST['description'] ?? '') ?: null,
            trim($_POST['image_path'] ?? '') ?: null,
            trim($_POST['features_json'] ?? '') ?: null,
            trim($_POST['detail_json'] ?? '') ?: null,
            isset($_POST['featured']) ? 1 : 0,
            (int) ($_POST['sort_order'] ?? 0),
            $_POST['status'] ?? 'active',
            $id,
        ]);
        log_activity((int) $admin['id'], 'update_insurance_plan', 'insurance_plan', $id);
        flash_set('success', 'อัปเดตแผนประกันแล้ว');
    }
    header('Location: insurance-plans.php');
    exit;
}

$rows = db()->query(
    'SELECT p.*, c.title AS category_title FROM insurance_plans p
     JOIN insurance_categories c ON c.id = p.category_id
     ORDER BY c.sort_order, p.sort_order, p.id'
)->fetchAll();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มแผนประกัน</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form admin-form--wide">
      <input type="hidden" name="action" value="create" />
      <div class="admin-form__row">
        <div>
          <label>หมวด</label>
          <select name="category_id" required>
            <?php foreach ($cats as $c): ?><option value="<?= (int) $c['id'] ?>"><?= h($c['title']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div><label>slug (URL)</label><input name="slug" required placeholder="health" /></div>
      </div>
      <label>ชื่อแผน</label><input name="name" required />
      <label>คำอธิบายสั้น</label><textarea name="description" rows="2"></textarea>
      <label>รูปภาพ (path)</label><input name="image_path" placeholder="assets/img/products/health.jpg" />
      <label>จุดเด่น (JSON array หรือบรรทัดละ 1 รายการ)</label>
      <textarea name="features_json" placeholder='["ค่ารักษาผู้ป่วยใน","เลือกวงเงิน"]'></textarea>
      <label>รายละเอียดเพิ่ม (JSON: coverageDetails, useCase, faq, facts)</label>
      <textarea name="detail_json" class="is-tall"></textarea>
      <div class="admin-form__row">
        <div><label>ลำดับ</label><input type="number" name="sort_order" value="0" /></div>
        <div><label>สถานะ</label><select name="status"><option value="active">active</option><option value="inactive">inactive</option></select></div>
      </div>
      <label><input type="checkbox" name="featured" value="1" /> แนะนำในหมวด</label>
      <button class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__head">
    <h2>แผนประกันทั้งหมด (<?= count($rows) ?>)</h2>
    <a href="insurance-categories.php" class="admin-btn admin-btn--ghost admin-btn--sm">← หมวด</a>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>แผน</th><th>หมวด</th><th>สถานะ</th><th>แก้ไข</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['name']) ?><br><small><?= h($r['slug']) ?></small><?= $r['featured'] ? ' <span class="badge badge--approved">แนะนำ</span>' : '' ?></td>
            <td><?= h($r['category_title']) ?></td>
            <td><?= h($r['status']) ?></td>
            <td><div class="admin-actions"><?= admin_edit_button('plan-edit-' . $r['id']) ?></div></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4" class="admin-empty">ยังไม่มี — รัน migrate-v3.php</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('plan-edit-' . $r['id'], 'แก้ไขแผน — ' . $r['name'], true); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <div class="admin-form__row">
    <div>
      <label>หมวด</label>
      <select name="category_id">
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int) $c['id'] ?>"<?= (int) $r['category_id'] === (int) $c['id'] ? ' selected' : '' ?>><?= h($c['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label>slug</label><input name="slug" value="<?= h($r['slug']) ?>" /></div>
  </div>
  <label>ชื่อแผน</label><input name="name" value="<?= h($r['name']) ?>" />
  <label>คำอธิบาย</label><textarea name="description" rows="2"><?= h($r['description'] ?? '') ?></textarea>
  <label>รูปภาพ</label><input name="image_path" value="<?= h($r['image_path'] ?? '') ?>" />
  <label>จุดเด่น (JSON)</label><textarea name="features_json" rows="3"><?= h($r['features_json'] ?? '') ?></textarea>
  <label>รายละเอียดเพิ่ม (JSON)</label><textarea name="detail_json" class="is-tall"><?= h($r['detail_json'] ?? '') ?></textarea>
  <div class="admin-form__row">
    <div><label>ลำดับ</label><input type="number" name="sort_order" value="<?= (int) $r['sort_order'] ?>" /></div>
    <div>
      <label>สถานะ</label>
      <select name="status">
        <option value="active"<?= $r['status'] === 'active' ? ' selected' : '' ?>>active</option>
        <option value="inactive"<?= $r['status'] === 'inactive' ? ' selected' : '' ?>>inactive</option>
      </select>
    </div>
  </div>
  <label><input type="checkbox" name="featured" value="1"<?= $r['featured'] ? ' checked' : '' ?> /> แนะนำ</label>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('แผนประกัน', ob_get_clean(), 'insurance-plans');
