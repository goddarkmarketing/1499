<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/backup.php';

$admin = require_admin();
admin_require_roles($admin, ['super_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create') {
            $result = backup_create_full($admin);
            flash_set(
                'success',
                'สร้างแบ็คอัพสำเร็จ — ' . $result['filename']
                . ' (' . backup_format_bytes($result['size']) . ', '
                . $result['tables'] . ' ตาราง, ' . $result['files'] . ' ไฟล์)'
            );
        } elseif ($action === 'delete') {
            $filename = basename((string) ($_POST['filename'] ?? ''));
            if (!backup_delete_file($filename, $admin)) {
                throw new RuntimeException('ลบไฟล์แบ็คอัพไม่ได้');
            }
            flash_set('success', 'ลบแบ็คอัพ ' . $filename . ' แล้ว');
        }
    } catch (Throwable $e) {
        flash_set('error', $e->getMessage());
    }
    header('Location: backups.php');
    exit;
}

$backups = [];
foreach (backup_list_files() as $filename) {
    $path = backup_file_path($filename);
    if (!$path) {
        continue;
    }
    $backups[] = [
        'filename' => $filename,
        'size' => (int) filesize($path),
        'mtime' => (int) filemtime($path),
    ];
}

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head">
    <h2>สร้างแบ็คอัพเต็มระบบ</h2>
    <form method="post">
      <input type="hidden" name="action" value="create" />
      <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm">
        <i data-lucide="archive" aria-hidden="true"></i>
        สร้างแบ็คอัพตอนนี้
      </button>
    </form>
  </div>
  <div class="admin-card__body">
    <p class="text-muted" style="margin:0 0 12px;">
      ดาวน์โหลดไฟล์ ZIP ที่รวม <strong>ฐานข้อมูลทั้งหมด</strong> และ <strong>ไฟล์เว็บ/หลังบ้าน</strong>
      (assets, admin, api, includes, database, HTML, config)
    </p>
    <ul class="admin-backup-list">
      <li>database/full.sql — ข้อมูลทุกตาราง (สมาชิก, รางวัล, lead, บทความ, CMS ฯลฯ)</li>
      <li>files/assets — รูปภาพ, CSS, JS, วิดีโอ</li>
      <li>files/admin, api, includes — ระบบหลังบ้านและ API</li>
      <li>files/database — schema, seed, CMS JSON</li>
      <li>files/*.html, config.php — หน้าเว็บและการตั้งค่า</li>
    </ul>
    <p class="text-muted" style="margin:12px 0 0;font-size:.88rem;">
      การสร้างแบ็คอัพอาจใช้เวลาสักครู่ ขึ้นกับขนาดไฟล์บนเซิร์ฟเวอร์
    </p>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__head">
    <h2>ไฟล์แบ็คอัพที่เก็บไว้ (<?= count($backups) ?>)</h2>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ไฟล์</th>
          <th>ขนาด</th>
          <th>สร้างเมื่อ</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($backups as $row): ?>
          <tr>
            <td><code><?= h($row['filename']) ?></code></td>
            <td><?= h(backup_format_bytes($row['size'])) ?></td>
            <td><small><?= h(date('Y-m-d H:i:s', $row['mtime'])) ?></small></td>
            <td>
              <div class="admin-actions">
                <a href="backup-download.php?file=<?= urlencode($row['filename']) ?>" class="admin-btn admin-btn--ghost admin-btn--sm">ดาวน์โหลด</a>
                <form method="post" style="display:inline" onsubmit="return confirm('ลบไฟล์แบ็คอัพนี้?');">
                  <input type="hidden" name="action" value="delete" />
                  <input type="hidden" name="filename" value="<?= h($row['filename']) ?>" />
                  <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">ลบ</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$backups): ?>
          <tr><td colspan="4" class="admin-empty">ยังไม่มีแบ็คอัพ — กดปุ่ม "สร้างแบ็คอัพตอนนี้"</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__head"><h2>วิธีกู้คืน</h2></div>
  <div class="admin-card__body">
    <ol class="admin-backup-steps">
      <li>แตก ZIP บนเครื่องของคุณ</li>
      <li>Import <code>database/full.sql</code> ใน phpMyAdmin (เลือก database ก่อน)</li>
      <li>อัปโหลดโฟลเดอร์ใน <code>files/</code> กลับไปที่ <code>httpdocs</code></li>
      <li>ตรวจสอบ <code>config.php</code> / <code>config.local.php</code> ให้ตรงกับโฮสต์</li>
    </ol>
  </div>
</div>
<?php
render_layout('สำรองข้อมูล', ob_get_clean(), 'backups');
