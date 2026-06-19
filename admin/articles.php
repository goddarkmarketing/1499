<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'create') {
        db()->prepare('INSERT INTO articles (category_id, slug, title, excerpt, body_html, body_json, read_time, featured, status, published_at) VALUES (?,?,?,?,?,?,?,?,?,?)')
            ->execute([
                $_POST['category_id'] ?: null,
                trim($_POST['slug']),
                trim($_POST['title']),
                trim($_POST['excerpt'] ?? '') ?: null,
                trim($_POST['body_html'] ?? '') ?: null,
                trim($_POST['body_json'] ?? '') ?: null,
                trim($_POST['read_time'] ?? '') ?: null,
                isset($_POST['featured']) ? 1 : 0,
                $_POST['status'] ?? 'draft',
                $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null,
            ]);
        flash_set('success', 'เพิ่มบทความแล้ว');
    }
    if (($_POST['action'] ?? '') === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        db()->prepare('UPDATE articles SET category_id=?, slug=?, title=?, excerpt=?, body_html=?, body_json=?, read_time=?, featured=?, status=? WHERE id=?')
            ->execute([
                $_POST['category_id'] ?: null,
                trim($_POST['slug']), trim($_POST['title']),
                trim($_POST['excerpt'] ?? '') ?: null,
                trim($_POST['body_html'] ?? '') ?: null,
                trim($_POST['body_json'] ?? '') ?: null,
                trim($_POST['read_time'] ?? '') ?: null,
                isset($_POST['featured']) ? 1 : 0,
                $_POST['status'], $id,
            ]);
        flash_set('success', 'อัปเดตบทความแล้ว');
    }
    header('Location: articles.php');
    exit;
}

$rows = db()->query(
    'SELECT a.*, c.title AS category_title FROM articles a LEFT JOIN article_categories c ON c.id = a.category_id ORDER BY a.updated_at DESC'
)->fetchAll();
$cats = db()->query('SELECT id, title FROM article_categories ORDER BY sort_order')->fetchAll();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มบทความ</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form">
      <input type="hidden" name="action" value="create" />
      <label>slug (URL)</label><input name="slug" required placeholder="why-life-insurance" />
      <label>หัวข้อ</label><input name="title" required />
      <label>หมวด</label><select name="category_id"><option value="">--</option><?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>"><?= h($c['title']) ?></option><?php endforeach; ?></select>
      <label>คำโปรย</label><textarea name="excerpt"></textarea>
      <label>เนื้อหา (HTML)</label><textarea name="body_html" class="is-tall"></textarea>
      <label>เนื้อหา (JSON สำหรับหน้าอ่าน — ใช้รูปแบบเดียวกับ site.js)</label><textarea name="body_json" class="is-tall" placeholder='{"date":"...","takeaways":[],"sections":[]}'></textarea>
      <label>เวลาอ่าน</label><input name="read_time" placeholder="4 นาที" />
      <label>สถานะ</label><select name="status"><option value="draft">draft</option><option value="published">published</option></select>
      <label><input type="checkbox" name="featured" value="1" /> แนะนำ</label>
      <button class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>
<div class="admin-card">
  <div class="admin-card__head"><h2>บทความ</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>หัวข้อ</th><th>หมวด</th><th>สถานะ</th><th>แก้ไข</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['title']) ?><br><small><?= h($r['slug']) ?></small></td>
            <td><?= h($r['category_title'] ?? '-') ?></td>
            <td><?= h($r['status']) ?></td>
            <td>
              <div class="admin-actions">
                <?= admin_edit_button('article-edit-' . $r['id']) ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4" class="admin-empty">ยังไม่มีบทความในระบบ (เว็บใช้ข้อมูลจาก site.js อยู่)</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<?php admin_modal_start('article-edit-' . $r['id'], 'แก้ไขบทความ — ' . $r['title'], true); ?>
<form method="post" class="admin-form admin-form--modal">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
  <div class="admin-form__row">
    <div><label>slug</label><input name="slug" value="<?= h($r['slug']) ?>" /></div>
    <div>
      <label>หมวด</label>
      <select name="category_id"><option value="">--</option><?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>"<?= (int) $r['category_id'] === (int) $c['id'] ? ' selected' : '' ?>><?= h($c['title']) ?></option><?php endforeach; ?></select>
    </div>
  </div>
  <label>หัวข้อ</label><input name="title" value="<?= h($r['title']) ?>" />
  <label>คำโปรย</label><textarea name="excerpt" rows="2"><?= h($r['excerpt'] ?? '') ?></textarea>
  <label>เนื้อหา (HTML)</label><textarea name="body_html" class="is-tall"><?= h($r['body_html'] ?? '') ?></textarea>
  <label>เนื้อหา (JSON)</label><textarea name="body_json" class="is-tall"><?= h($r['body_json'] ?? '') ?></textarea>
  <label>สถานะ</label>
  <select name="status">
    <option value="draft">draft</option>
    <option value="published"<?= $r['status'] === 'published' ? ' selected' : '' ?>>published</option>
    <option value="archived"<?= $r['status'] === 'archived' ? ' selected' : '' ?>>archived</option>
  </select>
<?php admin_modal_end(); ?>
<?php endforeach; ?>
<?php render_layout('บทความ', ob_get_clean(), 'articles');
