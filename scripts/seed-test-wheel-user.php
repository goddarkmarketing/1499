<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

$loginId = 'testwheel';
$password = 'Test1234';
$phone = '0899999999';
$birthDate = '1990-01-15';
$firstName = 'demo';
$lastName = 'หมุนวงล้อ';
$fullName = $firstName . ' ' . $lastName;
$email = 'testwheel@boyinsure.local';

function make_valid_national_id(string $seed12): string {
    $seed12 = preg_replace('/\D+/', '', $seed12);
    $seed12 = str_pad(substr($seed12, 0, 12), 12, '0', STR_PAD_LEFT);
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $sum += (int) $seed12[$i] * (13 - $i);
    }
    $check = (11 - ($sum % 11)) % 10;
    return $seed12 . $check;
}

$nationalId = make_valid_national_id('345990044944');

$tier = db()->query("SELECT id, spin_quota FROM member_tiers WHERE code='general' LIMIT 1")->fetch();
$tierId = $tier['id'] ?? null;
$spins = $tier ? max(5, (int) $tier['spin_quota']) : 5;
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = db()->prepare('SELECT id FROM members WHERE login_id = ? OR phone = ? LIMIT 1');
$stmt->execute([$loginId, $phone]);
$existing = $stmt->fetch();

if ($existing) {
    db()->prepare(
        'UPDATE members SET name=?, first_name=?, last_name=?, login_id=?, password_hash=?, national_id=?, birth_date=?, email=?, tier_id=?, status=?, spins_remaining=? WHERE id=?'
    )->execute([
        $fullName, $firstName, $lastName, $loginId, $passwordHash, $nationalId, $birthDate, $email, $tierId, 'active', $spins, $existing['id'],
    ]);
    $memberId = (int) $existing['id'];
    $action = 'อัปเดต';
} else {
    db()->prepare(
        'INSERT INTO members (name, first_name, last_name, phone, login_id, password_hash, national_id, birth_date, email, tier_id, status, spins_remaining)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
    )->execute([
        $fullName, $firstName, $lastName, $phone, $loginId, $passwordHash, $nationalId, $birthDate, $email, $tierId, 'active', $spins,
    ]);
    $memberId = (int) db()->lastInsertId();
    $action = 'สร้าง';
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>บัญชีทดสอบหมุนวงล้อ</title>
  <style>
    body { font-family: sans-serif; max-width: 520px; margin: 40px auto; padding: 0 20px; line-height: 1.6; }
    .box { background: #f0f6ff; border: 1px solid #c5d8f5; border-radius: 12px; padding: 20px; margin: 20px 0; }
    code { background: #fff; padding: 2px 8px; border-radius: 6px; }
    a { color: #1a4fa0; }
  </style>
</head>
<body>
  <h1>บัญชีทดสอบหมุนวงล้อ</h1>
  <p><?= htmlspecialchars($action) ?>สมาชิก ID #<?= $memberId ?> เรียบร้อย</p>
  <div class="box">
    <p><strong>ไอดี:</strong> <code><?= htmlspecialchars($loginId) ?></code></p>
    <p><strong>รหัสผ่าน:</strong> <code><?= htmlspecialchars($password) ?></code></p>
    <p><strong>เบอร์โทร (ลงทะเบียนหมุน):</strong> <code><?= htmlspecialchars($phone) ?></code></p>
    <p><strong>เลขบัตรประชาชน:</strong> <code><?= htmlspecialchars($nationalId) ?></code></p>
    <p><strong>วันเกิด:</strong> <code><?= htmlspecialchars($birthDate) ?></code></p>
    <p><strong>สิทธิ์หมุนคงเหลือ:</strong> <?= (int) $spins ?> ครั้ง</p>
  </div>
  <p><strong>ทดสอบเข้าสู่ระบบ:</strong> <a href="../promotions.html">promotions.html</a> → เข้าสู่ระบบ</p>
  <p><strong>ทดสอบหมุนวงล้อ:</strong> กด SPIN → ลงทะเบียน (ใช้เบอร์ด้านบน) → หมุน</p>
  <p><strong>หลังบ้าน:</strong> <a href="../admin/members.php">admin/members.php</a></p>
</body>
</html>
