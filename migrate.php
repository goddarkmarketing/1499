<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

function db_column_exists(string $table, string $column): bool {
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function db_table_exists(string $table): bool {
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
    );
    $stmt->execute([$table]);
    return (int) $stmt->fetchColumn() > 0;
}

$steps = [];
try {
    $pdo = db();

    if (!db_table_exists('games')) {
        $pdo->exec(
            "CREATE TABLE games (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              code VARCHAR(40) NOT NULL UNIQUE,
              name VARCHAR(120) NOT NULL,
              type ENUM('wheel','scratch','quiz','other') NOT NULL DEFAULT 'wheel',
              description TEXT NULL,
              status ENUM('active','inactive') NOT NULL DEFAULT 'active',
              sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB"
        );
        $steps[] = 'สร้างตาราง games';
    }

    $pdo->exec(
        "INSERT IGNORE INTO games (id, code, name, type, description, status, sort_order) VALUES
        (1, 'lucky_wheel', 'วงล้อโชคดี', 'wheel', 'เกมหมุนวงล้อลุ้นรางวัลสำหรับลูกค้า BOYINSURE', 'active', 1)"
    );
    $steps[] = 'เพิ่มเกมวงล้อโชคดี (ถ้ายังไม่มี)';

    if (!db_column_exists('prizes', 'game_id')) {
        $pdo->exec('ALTER TABLE prizes ADD COLUMN game_id INT UNSIGNED NULL AFTER id, ADD KEY idx_prizes_game (game_id)');
        $steps[] = 'เพิ่มคอลัมน์ prizes.game_id';
    }
    $pdo->exec('UPDATE prizes SET game_id = 1 WHERE game_id IS NULL');
    $steps[] = 'อัปเดตของรางวัลให้ผูกกับวงล้อโชคดี';

    if (!db_column_exists('spin_logs', 'game_id')) {
        $pdo->exec(
            'ALTER TABLE spin_logs
             ADD COLUMN game_id INT UNSIGNED NULL AFTER member_id,
             ADD KEY idx_spin_game (game_id),
             ADD KEY idx_spin_created (created_at)'
        );
        $steps[] = 'เพิ่มคอลัมน์ spin_logs.game_id';
    }
    $pdo->exec('UPDATE spin_logs SET game_id = 1 WHERE game_id IS NULL');
    $steps[] = 'อัปเดตประวัติการเล่นให้ผูกกับวงล้อโชคดี';

    foreach ([
        'first_name' => 'VARCHAR(60) NULL AFTER name',
        'last_name' => 'VARCHAR(60) NULL AFTER first_name',
        'national_id' => 'VARCHAR(13) NULL AFTER phone',
        'birth_date' => 'DATE NULL AFTER national_id',
    ] as $column => $definition) {
        if (!db_column_exists('members', $column)) {
            $pdo->exec("ALTER TABLE members ADD COLUMN {$column} {$definition}");
            $steps[] = "เพิ่มคอลัมน์ members.{$column}";
        }
    }

    if (!db_table_exists('wheel_registrations')) {
        $pdo->exec(
            "CREATE TABLE wheel_registrations (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              member_id INT UNSIGNED NULL,
              first_name VARCHAR(60) NOT NULL,
              last_name VARCHAR(60) NOT NULL,
              phone VARCHAR(20) NOT NULL,
              national_id VARCHAR(13) NOT NULL,
              birth_date DATE NOT NULL,
              email VARCHAR(190) NULL,
              consent_at DATETIME NOT NULL,
              ip_address VARCHAR(45) NULL,
              user_agent VARCHAR(255) NULL,
              spin_log_id INT UNSIGNED NULL,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              KEY idx_wr_phone (phone),
              KEY idx_wr_created (created_at),
              KEY idx_wr_spin (spin_log_id),
              CONSTRAINT fk_wr_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL,
              CONSTRAINT fk_wr_spin FOREIGN KEY (spin_log_id) REFERENCES spin_logs(id) ON DELETE SET NULL
            ) ENGINE=InnoDB"
        );
        $steps[] = 'สร้างตาราง wheel_registrations';
    }

    if (!db_column_exists('spin_logs', 'registration_id')) {
        $pdo->exec(
            'ALTER TABLE spin_logs
             ADD COLUMN registration_id INT UNSIGNED NULL AFTER member_id,
             ADD KEY idx_spin_registration (registration_id),
             ADD CONSTRAINT fk_spin_registration FOREIGN KEY (registration_id) REFERENCES wheel_registrations(id) ON DELETE SET NULL'
        );
        $steps[] = 'เพิ่มคอลัมน์ spin_logs.registration_id';
    }

    if (!db_column_exists('members', 'login_id')) {
        $pdo->exec(
            'ALTER TABLE members
             ADD COLUMN login_id VARCHAR(60) NULL AFTER phone,
             ADD UNIQUE KEY uk_members_login_id (login_id)'
        );
        $steps[] = 'เพิ่มคอลัมน์ members.login_id';
    }
    $pdo->exec('UPDATE members SET login_id = phone WHERE login_id IS NULL OR login_id = ""');
    $steps[] = 'ตั้งค่า login_id จากเบอร์โทรสำหรับสมาชิกเดิม';

    foreach ([
        'selected' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER status",
        'delivery_method' => "VARCHAR(30) NULL AFTER selected",
        'insurance_interest' => "VARCHAR(120) NULL AFTER delivery_method",
        'recipient_name' => "VARCHAR(120) NULL AFTER insurance_interest",
        'recipient_phone' => "VARCHAR(20) NULL AFTER recipient_name",
        'address_line' => "VARCHAR(255) NULL AFTER recipient_phone",
        'subdistrict' => "VARCHAR(120) NULL AFTER address_line",
        'district' => "VARCHAR(120) NULL AFTER subdistrict",
        'province' => "VARCHAR(120) NULL AFTER district",
        'postal_code' => "VARCHAR(10) NULL AFTER province",
        'contact_line' => "VARCHAR(120) NULL AFTER postal_code",
        'contact_note' => "VARCHAR(255) NULL AFTER contact_line",
        'claimed_at' => "DATETIME NULL AFTER contact_note",
        'consent_at' => "DATETIME NULL AFTER claimed_at",
    ] as $column => $definition) {
        if (!db_column_exists('reward_claims', $column)) {
            $pdo->exec("ALTER TABLE reward_claims ADD COLUMN {$column} {$definition}");
            $steps[] = "เพิ่มคอลัมน์ reward_claims.{$column}";
        }
    }

    $notifyEmail = trim(setting_get('notify_email', ''));
    if ($notifyEmail === '') {
        setting_set('notify_email', 'boyinsure8@gmail.com');
        $steps[] = 'ตั้งค่าอีเมลแจ้งเตือนเป็น boyinsure8@gmail.com';
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
  <title>อัปเดตฐานข้อมูล — เกมหลายประเภท</title>
  <style>
    body { font-family: sans-serif; max-width: 640px; margin: 40px auto; padding: 0 20px; }
    .ok { color: #166534; } .err { color: #991b1b; }
    li { margin-bottom: 6px; }
  </style>
</head>
<body>
  <h1>อัปเดตระบบเกมหลายประเภท</h1>
<?php if ($ok): ?>
  <p class="ok">✓ อัปเดตสำเร็จ</p>
  <ul>
    <?php foreach ($steps as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?>
  </ul>
  <p><a href="admin/plays.php">ไปหน้าประวัติการเล่น</a> · <a href="admin/games.php">จัดการเกม</a></p>
<?php else: ?>
  <p class="err">✗ <?= htmlspecialchars($error ?? 'unknown error') ?></p>
<?php endif; ?>
</body>
</html>
