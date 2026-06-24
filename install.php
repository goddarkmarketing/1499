<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$schema = file_get_contents(__DIR__ . '/database/schema.sql');
$seed = file_get_contents(__DIR__ . '/database/seed.sql');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>ติดตั้ง BOYINSURE Backend</title>
  <style>
    body { font-family: sans-serif; max-width: 640px; margin: 40px auto; padding: 0 20px; }
    .ok { color: #166534; } .err { color: #991b1b; }
    code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
  </style>
</head>
<body>
  <h1>ติดตั้งฐานข้อมูล BOYINSURE</h1>
<?php
try {
    $c = app_config('db');
    $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $c['host'], $c['port'], $c['charset']);
    $pdo = new PDO($dsn, $c['user'], $c['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    foreach (array_filter(array_map('trim', explode(';', $schema))) as $sql) {
        if ($sql !== '') {
            $pdo->exec($sql);
        }
    }
    foreach (array_filter(array_map('trim', explode(';', $seed))) as $sql) {
        if ($sql !== '') {
            $pdo->exec($sql);
        }
    }
    echo '<p class="ok">✓ ติดตั้งสำเร็จ</p>';
    echo '<p>เข้า Admin: <a href="admin/login.php">admin/login.php</a></p>';
    echo '<p>Email: <code>admin@boyinsure.com</code> · รหัสผ่าน: <code>admin123</code></p>';
} catch (Throwable $e) {
    echo '<p class="err">✗ เกิดข้อผิดพลาด: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>ตรวจสอบ MySQL ใน XAMPP และไฟล์ <code>config.php</code></p>';
}
?>
</body>
</html>
