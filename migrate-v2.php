<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

function v2_column_exists(string $table, string $column): bool {
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function v2_table_exists(string $table): bool {
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
    );
    $stmt->execute([$table]);
    return (int) $stmt->fetchColumn() > 0;
}

$steps = [];
try {
    $pdo = db();

    if (!v2_table_exists('settings')) {
        $pdo->exec(
            'CREATE TABLE settings (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              setting_key VARCHAR(80) NOT NULL UNIQUE,
              setting_value TEXT NULL,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB'
        );
        $steps[] = 'สร้างตาราง settings';
    }

    $defaults = [
        'site_name' => 'BoyInsure',
        'contact_email' => 'admin@boyinsure.com',
        'notify_email' => '',
        'low_stock_threshold' => '5',
    ];
    $ins = $pdo->prepare('INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)');
    foreach ($defaults as $k => $v) {
        $ins->execute([$k, $v]);
    }
    $steps[] = 'ตั้งค่าเริ่มต้น settings';

    if (!v2_table_exists('member_game_quotas')) {
        $pdo->exec(
            'CREATE TABLE member_game_quotas (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              member_id INT UNSIGNED NOT NULL,
              game_id INT UNSIGNED NOT NULL,
              plays_remaining TINYINT UNSIGNED NOT NULL DEFAULT 0,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              UNIQUE KEY uk_member_game (member_id, game_id),
              KEY idx_mgq_member (member_id),
              CONSTRAINT fk_mgq_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
              CONSTRAINT fk_mgq_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
            ) ENGINE=InnoDB'
        );
        $steps[] = 'สร้างตาราง member_game_quotas';
    }

    $gameId = (int) ($pdo->query("SELECT id FROM games WHERE code='lucky_wheel' LIMIT 1")->fetchColumn() ?: 1);
    $pdo->exec(
        "INSERT INTO member_game_quotas (member_id, game_id, plays_remaining)
         SELECT m.id, {$gameId}, m.spins_remaining FROM members m
         ON DUPLICATE KEY UPDATE plays_remaining = VALUES(plays_remaining)"
    );
    $steps[] = 'ย้ายสิทธิ์หมุนไป member_game_quotas';

    if (!v2_column_exists('articles', 'body_json')) {
        $pdo->exec('ALTER TABLE articles ADD COLUMN body_json MEDIUMTEXT NULL AFTER body_html');
        $steps[] = 'เพิ่ม articles.body_json';
    }

    if (!v2_column_exists('agent_applications', 'agent_id')) {
        $pdo->exec('ALTER TABLE agent_applications ADD COLUMN agent_id INT UNSIGNED NULL AFTER status');
        $steps[] = 'เพิ่ม agent_applications.agent_id (กันสร้างตัวแทนซ้ำ)';
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
  <title>อัปเดต v2 | BoyInsure</title>
  <style>body{font-family:sans-serif;max-width:640px;margin:40px auto;padding:0 20px}.ok{color:#166534}.err{color:#991b1b}</style>
</head>
<body>
  <h1>อัปเดตระบบ v2</h1>
<?php if ($ok): ?>
  <p class="ok">✓ สำเร็จ</p>
  <ul><?php foreach ($steps as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?></ul>
  <p><a href="admin/index.php">เข้า Admin</a></p>
<?php else: ?>
  <p class="err">✗ <?= htmlspecialchars($error ?? '') ?></p>
<?php endif; ?>
</body>
</html>
