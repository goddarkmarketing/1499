<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();
admin_require_roles($admin, ['super_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setting_set('site_name', trim($_POST['site_name'] ?? ''));
    setting_set('site_tagline', trim($_POST['site_tagline'] ?? ''));
    setting_set('contact_email', trim($_POST['contact_email'] ?? ''));
    setting_set('phone', preg_replace('/\D+/', '', trim($_POST['phone'] ?? '')));
    setting_set('phone_display', trim($_POST['phone_display'] ?? ''));
    setting_set('business_hours', trim($_POST['business_hours'] ?? ''));
    setting_set('address', trim($_POST['address'] ?? ''));
    setting_set('footer_note', trim($_POST['footer_note'] ?? ''));
    setting_set('facebook_url', trim($_POST['facebook_url'] ?? ''));
    setting_set('tiktok_url', trim($_POST['tiktok_url'] ?? ''));
    setting_set('line_url', trim($_POST['line_url'] ?? ''));
    setting_set('notify_email', trim($_POST['notify_email'] ?? ''));
    setting_set('low_stock_threshold', trim($_POST['low_stock_threshold'] ?? '5'));
    log_activity((int) $admin['id'], 'update_settings');
    flash_set('success', 'บันทึกการตั้งค่าแล้ว');
    header('Location: settings.php');
    exit;
}

$lowStock = (int) (setting_get('low_stock_threshold', '5') ?: 5);
$lowPrizes = db()->prepare(
    'SELECT name, stock FROM prizes WHERE stock IS NOT NULL AND stock <= ? AND status = "active" ORDER BY stock'
);
$lowPrizes->execute([$lowStock]);
$lowRows = $lowPrizes->fetchAll();

ob_start();
?>
<?php if ($lowRows): ?>
<div class="admin-card">
  <div class="admin-card__head"><h2>แจ้งเตือน — ของรางวัลใกล้หมด</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>รางวัล</th><th>คงเหลือ</th></tr></thead>
      <tbody>
        <?php foreach ($lowRows as $p): ?>
          <tr><td><?= h($p['name']) ?></td><td><span class="badge badge--rejected"><?= (int) $p['stock'] ?></span></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<div class="admin-card">
  <div class="admin-card__head"><h2>ตั้งค่าระบบ</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form admin-form--wide">
      <label>ชื่อเว็บไซต์</label>
      <input name="site_name" value="<?= h(setting_get('site_name', 'BOYINSURE')) ?>" />
      <label>สโลแกน (แสดงใน Footer)</label>
      <input name="site_tagline" value="<?= h(setting_get('site_tagline', 'คุ้มครองทุกช่วงชีวิต ด้วยใจ')) ?>" />
      <div class="admin-form__row">
        <div>
          <label>เบอร์โทร (ตัวเลข)</label>
          <input name="phone" value="<?= h(setting_get('phone', '0627878968')) ?>" placeholder="0627878968" />
        </div>
        <div>
          <label>เบอร์โทร (แสดงผล)</label>
          <input name="phone_display" value="<?= h(setting_get('phone_display', '062-787-8968')) ?>" />
        </div>
      </div>
      <label>อีเมลติดต่อ</label>
      <input type="email" name="contact_email" value="<?= h(setting_get('contact_email', '')) ?>" />
      <label>เวลาทำการ</label>
      <input name="business_hours" value="<?= h(setting_get('business_hours', 'จันทร์–ศุกร์ 09:00–18:00 น.')) ?>" />
      <label>ที่อยู่ / พื้นที่ให้บริการ</label>
      <input name="address" value="<?= h(setting_get('address', 'ให้บริการทั่วประเทศ')) ?>" />
      <label>ข้อความใต้โลโก้ (Footer)</label>
      <input name="footer_note" value="<?= h(setting_get('footer_note', 'พันธมิตรด้านประกันภัย')) ?>" />
      <div class="admin-form__row">
        <div>
          <label>Facebook URL</label>
          <input name="facebook_url" value="<?= h(setting_get('facebook_url', '')) ?>" placeholder="https://www.facebook.com/..." />
        </div>
        <div>
          <label>TikTok URL</label>
          <input name="tiktok_url" value="<?= h(setting_get('tiktok_url', '')) ?>" placeholder="https://www.tiktok.com/@..." />
        </div>
      </div>
      <label>LINE URL</label>
      <input name="line_url" value="<?= h(setting_get('line_url', '')) ?>" placeholder="https://line.me/R/ti/p/@..." />
      <label>อีเมลแจ้งเตือน Lead ใหม่</label>
      <input type="email" name="notify_email" value="<?= h(setting_get('notify_email', '')) ?>" />
      <label>แจ้งเตือนสต็อกต่ำกว่า (ชิ้น)</label>
      <input type="number" name="low_stock_threshold" min="0" value="<?= h(setting_get('low_stock_threshold', '5')) ?>" />
      <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </form>
  </div>
</div>
<?php
render_layout('ตั้งค่าระบบ', ob_get_clean(), 'settings');
