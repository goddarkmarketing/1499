<?php
/** @var array $admin */
/** @var ?array $flash */
/** @var array $menuGroups */
/** @var string $title */
/** @var string $content */
/** @var string $active */

$roleLabels = [
    'super_admin' => 'Super Admin',
    'admin' => 'ผู้ดูแลระบบ',
    'editor' => 'ผู้แก้ไขเนื้อหา',
    'support' => 'ฝ่ายสนับสนุน',
];
$roleText = $roleLabels[$admin['role']] ?? $admin['role'];
$initial = mb_strtoupper(mb_substr($admin['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= h($title) ?> | BOYINSURE Admin</title>
  <link rel="stylesheet" href="../assets/css/fonts.css" />
  <link rel="stylesheet" href="assets/admin.css" />
  <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>
  <script src="assets/admin.js" defer></script>
</head>
<body class="admin-body">
  <div class="admin-sidebar__overlay" id="sidebarOverlay" aria-hidden="true"></div>
  <div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
      <a href="index.php" class="admin-brand">
        <span class="admin-brand__mark">B</span>
        BOYINSURE <span>Admin</span>
      </a>
      <nav class="admin-nav">
        <?php foreach ($menuGroups as [$groupLabel, $items]): ?>
          <div class="admin-nav__group">
            <div class="admin-nav__label"><?= h($groupLabel) ?></div>
            <?php foreach ($items as [$key, $href, $label, $icon]): ?>
              <?php $badgeCount = (int) ($navBadges[$key] ?? 0); ?>
              <a href="<?= h($href) ?>" class="admin-nav__link<?= $active === $key ? ' is-active' : '' ?>">
                <i data-lucide="<?= h($icon) ?>" aria-hidden="true"></i>
                <span class="admin-nav__text"><?= h($label) ?></span>
                <?php if ($badgeCount > 0): ?>
                  <span class="admin-nav__badge" aria-label="รายการใหม่ <?= (int) $badgeCount ?>"><?= h(admin_nav_badge_label($badgeCount)) ?></span>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </nav>
      <div class="admin-sidebar__foot">
        <div class="admin-user">
          <div class="admin-user__avatar" aria-hidden="true"><?= h($initial) ?></div>
          <div class="admin-user__info">
            <p class="admin-user__name"><?= h($admin['name']) ?></p>
            <small class="admin-user__role"><?= h($roleText) ?></small>
          </div>
        </div>
        <a href="logout.php" class="admin-logout">
          <i data-lucide="log-out" aria-hidden="true"></i>
          ออกจากระบบ
        </a>
      </div>
    </aside>
    <div class="admin-main">
      <header class="admin-topbar">
        <div class="admin-topbar__left">
          <button type="button" class="admin-topbar__menu-btn" id="sidebarToggle" aria-label="เปิดเมนู">
            <i data-lucide="menu" aria-hidden="true"></i>
          </button>
          <div class="admin-topbar__titles">
            <h1><?= h($title) ?></h1>
          </div>
        </div>
        <div class="admin-topbar__actions">
          <a href="../index.html" class="admin-topbar__link" target="_blank" rel="noopener">
            <i data-lucide="external-link" aria-hidden="true"></i>
            ดูเว็บไซต์
          </a>
        </div>
      </header>
      <?php if ($flash): ?>
        <div class="admin-flash admin-flash--<?= h($flash['type']) ?>" role="alert">
          <i data-lucide="<?= $flash['type'] === 'success' ? 'check-circle' : 'alert-circle' ?>" aria-hidden="true"></i>
          <?= h($flash['msg']) ?>
          <button type="button" class="admin-flash__close" aria-label="ปิด">
            <i data-lucide="x" aria-hidden="true"></i>
          </button>
        </div>
      <?php endif; ?>
      <div class="admin-content">
        <?= $content ?>
      </div>
    </div>
  </div>
</body>
</html>
