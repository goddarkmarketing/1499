<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

start_session(app_config('session.member_key'));

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = read_json_body();
$firstName = trim($data['first_name'] ?? $data['firstName'] ?? '');
$lastName = trim($data['last_name'] ?? $data['lastName'] ?? '');
$phone = normalize_phone($data['phone'] ?? '');
$nationalId = preg_replace('/\D+/', '', trim($data['national_id'] ?? $data['nationalId'] ?? ''));
$birthDate = trim($data['birth_date'] ?? $data['birthDate'] ?? '');
$email = trim($data['email'] ?? '');
$consent = !empty($data['consent']);
$loginId = normalize_login_id($data['login_id'] ?? $data['loginId'] ?? '');
$password = (string) ($data['password'] ?? '');
$passwordConfirm = (string) ($data['password_confirm'] ?? $data['passwordConfirm'] ?? '');

if ($firstName === '' || $lastName === '') {
    json_response(['error' => 'กรุณากรอกชื่อและนามสกุล'], 422);
}
if (!validate_thai_phone($phone)) {
    json_response(['error' => 'กรุณากรอกเบอร์โทรศัพท์ 10 หลักให้ถูกต้อง'], 422);
}
if (!validate_thai_national_id($nationalId)) {
    json_response(['error' => 'กรุณากรอกเลขบัตรประชาชน 13 หลักให้ถูกต้อง'], 422);
}
if (!validate_birth_date($birthDate)) {
    json_response(['error' => 'กรุณาเลือกวันเกิดให้ถูกต้อง'], 422);
}
if (!$consent) {
    json_response(['error' => 'กรุณายินยอมข้อกำหนดและเงื่อนไข'], 422);
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['error' => 'รูปแบบอีเมลไม่ถูกต้อง'], 422);
}

$fullName = trim($firstName . ' ' . $lastName);
$tier = db()->query("SELECT id, spin_quota FROM member_tiers WHERE code='general' LIMIT 1")->fetch();
$tierId = $tier['id'] ?? null;
$spins = 2;

$accountCreated = false;
$credentialsIssued = false;
$issuedLoginId = '';
$issuedPassword = '';

$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare('SELECT * FROM members WHERE phone = ? LIMIT 1 FOR UPDATE');
    $stmt->execute([$phone]);
    $member = $stmt->fetch();

    if (!$member) {
        if ($loginId === '' || !validate_login_id($loginId)) {
            throw new RuntimeException('กรุณาตั้งไอดี 4-32 ตัวอักษร (a-z, 0-9, . _ -)');
        }
        if (!validate_member_password($password)) {
            throw new RuntimeException('กรุณาตั้งรหัสผ่านอย่างน้อย 6 ตัวอักษร');
        }
        if ($password !== $passwordConfirm) {
            throw new RuntimeException('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
        }

        $dupLogin = $pdo->prepare('SELECT id FROM members WHERE login_id = ? LIMIT 1');
        $dupLogin->execute([$loginId]);
        if ($dupLogin->fetch()) {
            throw new RuntimeException('ไอดีนี้ถูกใช้แล้ว กรุณาเลือกไอดีอื่น');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare(
            'INSERT INTO members (name, first_name, last_name, phone, login_id, password_hash, national_id, birth_date, email, tier_id, status, spins_remaining)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $fullName,
            $firstName,
            $lastName,
            $phone,
            $loginId,
            $passwordHash,
            $nationalId,
            $birthDate,
            $email !== '' ? $email : null,
            $tierId,
            'active',
            $spins,
        ]);
        $memberId = (int) $pdo->lastInsertId();
        $accountCreated = true;
        $credentialsIssued = true;
        $issuedLoginId = $loginId;
        $issuedPassword = $password;
    } else {
        $memberId = (int) $member['id'];

        if (empty($member['password_hash'])) {
            if ($loginId === '' || !validate_login_id($loginId)) {
                throw new RuntimeException('กรุณาตั้งไอดี 4-32 ตัวอักษร (a-z, 0-9, . _ -)');
            }
            if (!validate_member_password($password)) {
                throw new RuntimeException('กรุณาตั้งรหัสผ่านอย่างน้อย 6 ตัวอักษร');
            }
            if ($password !== $passwordConfirm) {
                throw new RuntimeException('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
            }

            $dupLogin = $pdo->prepare('SELECT id FROM members WHERE login_id = ? AND id <> ? LIMIT 1');
            $dupLogin->execute([$loginId, $memberId]);
            if ($dupLogin->fetch()) {
                throw new RuntimeException('ไอดีนี้ถูกใช้แล้ว กรุณาเลือกไอดีอื่น');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare(
                'UPDATE members
                 SET name = ?, first_name = ?, last_name = ?, national_id = ?, birth_date = ?, email = ?, spins_remaining = ?,
                     login_id = ?, password_hash = ?
                 WHERE id = ?'
            )->execute([
                $fullName,
                $firstName,
                $lastName,
                $nationalId,
                $birthDate,
                $email !== '' ? $email : null,
                $spins,
                $loginId,
                $passwordHash,
                $memberId,
            ]);
            $credentialsIssued = true;
            $issuedLoginId = $loginId;
            $issuedPassword = $password;
        } else {
            $pdo->prepare(
                'UPDATE members
                 SET name = ?, first_name = ?, last_name = ?, national_id = ?, birth_date = ?, email = ?, spins_remaining = ?
                 WHERE id = ?'
            )->execute([
                $fullName,
                $firstName,
                $lastName,
                $nationalId,
                $birthDate,
                $email !== '' ? $email : null,
                $spins,
                $memberId,
            ]);
        }
    }

    $pdo->prepare(
        'INSERT INTO wheel_registrations
         (member_id, first_name, last_name, phone, national_id, birth_date, email, consent_at, ip_address, user_agent)
         VALUES (?,?,?,?,?,?,?,?,?,?)'
    )->execute([
        $memberId,
        $firstName,
        $lastName,
        $phone,
        $nationalId,
        $birthDate,
        $email !== '' ? $email : null,
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'] ?? null,
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255) ?: null,
    ]);
    $registrationId = (int) $pdo->lastInsertId();

    $pdo->commit();
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_response(['error' => $e->getMessage()], 400);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_response(['error' => 'ลงทะเบียนไม่สำเร็จ กรุณาลองใหม่'], 500);
}

$stmt = db()->prepare(
    'SELECT m.*, mt.name AS tier_name, mt.code AS tier_code, mt.spin_quota
     FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id WHERE m.id = ?'
);
$stmt->execute([$memberId]);
$member = $stmt->fetch();

$credentialsSentEmail = false;
if ($credentialsIssued && $issuedLoginId !== '' && $issuedPassword !== '') {
    if ($email !== '') {
        $credentialsSentEmail = send_member_credentials_email($email, $fullName, $issuedLoginId, $issuedPassword);
    }
    notify_admin_new_member(array_merge($member, ['id' => $memberId]), $issuedLoginId, $issuedPassword);
}

unset($member['password_hash']);
$_SESSION['member'] = $member;
$_SESSION['wheel_registration_id'] = $registrationId;
$_SESSION['wheel_spin_unlocked'] = true;

$response = [
    'ok' => true,
    'registration_id' => $registrationId,
    'account_created' => $accountCreated,
    'member' => [
        'id' => (int) $member['id'],
        'name' => $member['name'],
        'first_name' => $member['first_name'] ?? $firstName,
        'last_name' => $member['last_name'] ?? $lastName,
        'phone' => $member['phone'],
        'login_id' => $member['login_id'] ?? $issuedLoginId,
        'spins_remaining' => (int) $member['spins_remaining'],
        'points' => (int) ($member['points'] ?? 0),
        'tier_name' => $member['tier_name'] ?? 'ทั่วไป',
        'tier_code' => $member['tier_code'] ?? 'general',
    ],
];

if ($credentialsIssued) {
    $response['credentials'] = [
        'login_id' => $issuedLoginId,
        'password' => $issuedPassword,
    ];
    $response['credentials_sent_email'] = $credentialsSentEmail;
}

json_response($response);
