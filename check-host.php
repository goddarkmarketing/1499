<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ตรวจสอบระบบ BOYINSURE</title>
  <style>
    body { font-family: sans-serif; max-width: 720px; margin: 32px auto; padding: 0 20px; line-height: 1.6; }
    .ok { color: #166534; } .err { color: #991b1b; } .warn { color: #92400e; }
    code, pre { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
    pre { padding: 12px; overflow: auto; }
    ol { padding-left: 20px; }
  </style>
</head>
<body>
  <h1>ตรวจสอบระบบ BOYINSURE</h1>
  <pre><?php
echo 'PHP version: ' . PHP_VERSION . "\n";
echo 'PDO MySQL: ' . (extension_loaded('pdo_mysql') ? 'OK' : 'MISSING') . "\n";
echo 'config.local.php: ' . (is_file(__DIR__ . '/config.local.php') ? 'พบแล้ว' : 'ยังไม่มี — ต้องสร้างจาก config.example.php') . "\n\n";

try {
    require_once __DIR__ . '/includes/bootstrap.php';
    $db = app_config('db');
    echo "DB host: {$db['host']}\n";
    echo "DB name: {$db['name']}\n";
    echo "DB user: {$db['user']}\n\n";

    $pdo = db();
    echo "เชื่อมต่อฐานข้อมูล: OK\n";

    $required = ['admin_users', 'members', 'prizes', 'reward_claims', 'spin_logs'];
    foreach ($required as $table) {
        $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        $exists = (bool) $stmt->fetchColumn();
        echo 'ตาราง ' . $table . ': ' . ($exists ? 'OK' : 'MISSING') . "\n";
    }

    $admin = $pdo->query('SELECT email FROM admin_users WHERE status = "active" LIMIT 1')->fetch();
    echo "\nบัญชี Admin: " . ($admin ? $admin['email'] : 'ยังไม่มี — รัน install.php') . "\n";
} catch (Throwable $e) {
    echo "\n<span class=\"err\">ERROR: " . htmlspecialchars($e->getMessage()) . "</span>\n";
}
?></pre>

  <h2>วิธีแก้เมื่อขึ้นโฮสติ้ง</h2>
  <ol>
    <li>สร้าง MySQL Database + User ใน cPanel แล้วจดชื่อ DB / user / password</li>
    <li>อัปโหลดไฟล์โปรเจกต์ทั้งหมด (รวมโฟลเดอร์ <code>admin/</code>, <code>api/</code>)</li>
    <li>คัดลอก <code>config.example.php</code> เป็น <code>config.local.php</code> แล้วใส่ค่า DB จากโฮสต์</li>
    <li>เปิด <a href="install.php">install.php</a> ครั้งเดียวเพื่อสร้างตาราง + admin</li>
    <li>เข้า <a href="admin/login.php">admin/login.php</a> — <code>admin@boyinsure.com</code> / <code>admin123</code></li>
    <li>ลบหรือเปลี่ยนชื่อ <code>install.php</code> หลังติดตั้งเสร็จ</li>
  </ol>
</body>
</html>
