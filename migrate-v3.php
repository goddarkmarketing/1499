<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/cms-seed.php';

header('Content-Type: text/html; charset=utf-8');

function v3_column_exists(string $table, string $column): bool {
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function v3_table_exists(string $table): bool {
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
    );
    $stmt->execute([$table]);
    return (int) $stmt->fetchColumn() > 0;
}

$steps = [];
try {
    $pdo = db();

    if (!v3_table_exists('insurance_categories')) {
        $pdo->exec(
            'CREATE TABLE insurance_categories (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              slug VARCHAR(40) NOT NULL UNIQUE,
              title VARCHAR(120) NOT NULL,
              tagline VARCHAR(255) NULL,
              icon VARCHAR(40) NULL DEFAULT "shield",
              detail_json MEDIUMTEXT NULL,
              sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
              status ENUM("active","inactive") NOT NULL DEFAULT "active",
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB'
        );
        $steps[] = 'สร้างตาราง insurance_categories';
    }

    if (!v3_table_exists('insurance_plans')) {
        $pdo->exec(
            'CREATE TABLE insurance_plans (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              category_id INT UNSIGNED NOT NULL,
              slug VARCHAR(80) NOT NULL UNIQUE,
              name VARCHAR(200) NOT NULL,
              description TEXT NULL,
              image_path VARCHAR(255) NULL,
              features_json MEDIUMTEXT NULL,
              detail_json MEDIUMTEXT NULL,
              featured TINYINT(1) NOT NULL DEFAULT 0,
              sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
              status ENUM("active","inactive") NOT NULL DEFAULT "active",
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              KEY idx_ins_plan_cat (category_id),
              KEY idx_ins_plan_status (status),
              CONSTRAINT fk_ins_plan_cat FOREIGN KEY (category_id) REFERENCES insurance_categories(id) ON DELETE CASCADE
            ) ENGINE=InnoDB'
        );
        $steps[] = 'สร้างตาราง insurance_plans';
    }

    if (!v3_table_exists('site_content')) {
        $pdo->exec(
            'CREATE TABLE site_content (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              content_key VARCHAR(60) NOT NULL UNIQUE,
              title VARCHAR(120) NULL,
              body_json MEDIUMTEXT NOT NULL,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB'
        );
        $steps[] = 'สร้างตาราง site_content';
    }

    $extraSettings = [
        'phone' => '0627878968',
        'phone_display' => '062-787-8968',
        'business_hours' => 'จันทร์–ศุกร์ 09:00–18:00 น.',
        'address' => 'ให้บริการทั่วประเทศ',
        'site_tagline' => 'คุ้มครองทุกช่วงชีวิต ด้วยใจ',
        'footer_note' => 'ศูนย์ไทยประกันชีวิต',
    ];
    $ins = $pdo->prepare('INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)');
    foreach ($extraSettings as $k => $v) {
        $ins->execute([$k, $v]);
    }
    $steps[] = 'ตั้งค่า contact / footer เริ่มต้น';

    $seedSteps = cms_seed_if_empty($pdo);
    foreach ($seedSteps as $s) {
        $steps[] = $s;
    }

    $ok = true;
} catch (Throwable $e) {
    $ok = false;
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>อัปเดต v3 CMS | BoyInsure</title>
  <style>body{font-family:sans-serif;max-width:640px;margin:40px auto;padding:0 20px}.ok{color:#166534}.err{color:#991b1b}</style>
</head>
<body>
  <h1>อัปเดตระบบ v3 — CMS เนื้อหาเว็บ</h1>
<?php if ($ok): ?>
  <p class="ok">✓ สำเร็จ</p>
  <ul><?php foreach ($steps as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?></ul>
  <p><a href="admin/insurance-plans.php">จัดการแผนประกัน</a> · <a href="admin/site-pages.php">จัดการหน้าเว็บ</a> · <a href="admin/index.php">Admin</a></p>
<?php else: ?>
  <p class="err">✗ <?= htmlspecialchars($error ?? '') ?></p>
<?php endif; ?>
</body>
</html>
