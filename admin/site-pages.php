<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();
admin_require_roles($admin, ['super_admin', 'ops']);

$keys = [
    'footer' => 'Footer / ส่วนท้ายเว็บ',
    'about' => 'หน้าเกี่ยวกับเรา',
    'contact' => 'หน้าติดต่อเรา',
    'highlights' => 'ไฮไลท์หน้าแรก',
    'plan_process' => 'ขั้นตอนเลือกแผน (หน้ารายละเอียดประกัน)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = $_POST['content_key'] ?? '';
    if (!isset($keys[$key])) {
        flash_set('error', 'คีย์ไม่ถูกต้อง');
        header('Location: site-pages.php');
        exit;
    }
    $raw = trim($_POST['body_json'] ?? '');
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        flash_set('error', 'JSON ไม่ถูกต้อง — ตรวจสอบรูปแบบ');
        header('Location: site-pages.php?tab=' . urlencode($key));
        exit;
    }
    site_content_set($key, $decoded, $keys[$key]);
    log_activity((int) $admin['id'], 'update_site_content', 'site_content', 0, $key);
    flash_set('success', 'บันทึก ' . $keys[$key] . ' แล้ว');
    header('Location: site-pages.php?tab=' . urlencode($key));
    exit;
}

$tab = $_GET['tab'] ?? 'about';
if (!isset($keys[$tab])) {
    $tab = 'about';
}

$existing = [];
$rows = db()->query('SELECT content_key, body_json FROM site_content')->fetchAll();
foreach ($rows as $row) {
    $existing[$row['content_key']] = $row['body_json'];
}

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>จัดการเนื้อหาหน้าเว็บ</h2></div>
  <div class="admin-card__body">
    <p class="text-muted" style="margin:0 0 16px;">แก้ไขข้อความบนหน้า About, Contact, Footer และส่วนอื่น ๆ — ข้อมูลเบอร์โทร/อีเมลหลักอยู่ที่ <a href="settings.php">ตั้งค่าระบบ</a></p>
    <div class="admin-filters" style="margin-bottom:20px;">
      <?php foreach ($keys as $k => $label): ?>
        <a href="site-pages.php?tab=<?= urlencode($k) ?>" class="admin-btn admin-btn--sm<?= $tab === $k ? ' admin-btn--primary' : ' admin-btn--ghost' ?>"><?= h($label) ?></a>
      <?php endforeach; ?>
    </div>

    <form method="post" class="admin-form admin-form--wide">
      <input type="hidden" name="content_key" value="<?= h($tab) ?>" />
      <label><?= h($keys[$tab]) ?> — JSON</label>
      <textarea name="body_json" class="is-tall" style="min-height:420px;font-family:monospace;font-size:.85rem;"><?= h($existing[$tab] ?? "{\n}\n") ?></textarea>
      <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </form>

    <?php if ($tab === 'about'): ?>
      <details style="margin-top:20px;"><summary class="text-muted">โครงสร้าง JSON ตัวอย่าง (about)</summary>
        <pre style="font-size:.8rem;overflow:auto;">{"hero":{"eyebrow":"","title":"","lead":""},"paragraphs":[],"highlight":{"title":"","text":""},"services":{"heading":"","items":[{"icon":"","title":"","text":""}]},"cta":{"title":"","subtitle":""}}</pre>
      </details>
    <?php elseif ($tab === 'contact'): ?>
      <details style="margin-top:20px;"><summary class="text-muted">โครงสร้าง JSON ตัวอย่าง (contact)</summary>
        <pre style="font-size:.8rem;overflow:auto;">{"hero":{},"cards":[{"icon":"","title":"","text":""}],"features":[],"interests":[]}</pre>
      </details>
    <?php elseif ($tab === 'highlights'): ?>
      <details style="margin-top:20px;"><summary class="text-muted">โครงสร้าง JSON ตัวอย่าง (highlights)</summary>
        <pre style="font-size:.8rem;overflow:auto;">{"items":[{"brandName":"","title":"","text":"","image":"","link":""}]}</pre>
      </details>
    <?php elseif ($tab === 'plan_process'): ?>
      <details style="margin-top:20px;"><summary class="text-muted">โครงสร้าง JSON ตัวอย่าง (plan_process)</summary>
        <pre style="font-size:.8rem;overflow:auto;">{"steps":[{"icon":"","title":"","desc":""}]}</pre>
      </details>
    <?php endif; ?>
  </div>
</div>
<?php
render_layout('เนื้อหาหน้าเว็บ', ob_get_clean(), 'site-pages');
