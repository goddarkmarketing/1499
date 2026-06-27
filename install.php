<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

function install_filter_sql(string $sql): array {
    $statements = [];
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
        if ($statement === '') {
            continue;
        }
        $upper = strtoupper(ltrim($statement));
        if (strpos($upper, 'CREATE DATABASE') === 0) {
            continue;
        }
        if (strpos($upper, 'USE ') === 0) {
            continue;
        }
        $statements[] = $statement;
    }
    return $statements;
}

$schema = file_get_contents(__DIR__ . '/database/schema.sql') ?: '';
$seed = file_get_contents(__DIR__ . '/database/seed.sql') ?: '';
$db = app_config('db');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>ติดตั้ง BOYINSURE Backend</title>
  <style>
    body { font-family: sans-serif; max-width: 720px; margin: 40px auto; padding: 0 20px; line-height: 1.6; }
    .ok { color: #166534; } .err { color: #991b1b; } .warn { color: #92400e; }
    code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
    pre { background: #f8fafc; padding: 12px; border-radius: 8px; overflow: auto; }
  </style>
</head>
<body>
  <h1>ติดตั้งฐานข้อมูล BOYINSURE</h1>
  <p>DB ที่ใช้: <code><?= htmlspecialchars((string) ($db['name'] ?? '')) ?></code>
    @ <code><?= htmlspecialchars((string) ($db['host'] ?? '')) ?></code></p>
<?php
if (!is_file(__DIR__ . '/config.local.php') && ($db['user'] ?? '') === 'root' && ($db['pass'] ?? '') === '') {
    echo '<p class="warn">⚠ ยังใช้ค่า XAMPP เริ่มต้น — บนโฮสติ้งให้สร้าง <code>config.local.php</code> จาก <code>config.example.php</code> ก่อน</p>';
}

try {
    $pdo = db();
    $schemaStatements = install_filter_sql($schema);
    $seedStatements = install_filter_sql($seed);

    foreach ($schemaStatements as $sql) {
        $pdo->exec($sql);
    }
    foreach ($seedStatements as $sql) {
        $pdo->exec($sql);
    }

    echo '<p class="ok">✓ ติดตั้งสำเร็จ</p>';
    echo '<p>เข้า Admin: <a href="admin/login.php">admin/login.php</a></p>';
    echo '<p>Email: <code>admin@boyinsure.com</code> · รหัสผ่าน: <code>admin123</code></p>';
    echo '<p class="warn">แนะนำให้ลบหรือเปลี่ยนชื่อไฟล์ <code>install.php</code> หลังติดตั้ง</p>';
} catch (Throwable $e) {
    echo '<p class="err">✗ เกิดข้อผิดพลาด: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre class="err">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '<p>ตรวจสอบ:</p><ul>';
    echo '<li>สร้าง <code>config.local.php</code> แล้วใส่ host / db name / user / password จาก cPanel</li>';
    echo '<li>สร้าง Database ใน cPanel แล้ว assign user ให้ DB</li>';
    echo '<li>เปิด <a href="check-host.php">check-host.php</a> เพื่อดูรายละเอียด</li>';
    echo '</ul>';
}
?>
</body>
</html>
