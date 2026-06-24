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

$sessionMember = $_SESSION['member'] ?? null;
if (!$sessionMember) {
    json_response(['error' => 'กรุณาเข้าสู่ระบบก่อนหมุนวงล้อ'], 401);
}

$memberId = (int) $sessionMember['id'];
$stmt = db()->prepare(
    'SELECT m.*, mt.name AS tier_name, mt.code AS tier_code
     FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id
     WHERE m.id = ? AND m.status = ? LIMIT 1'
);
$stmt->execute([$memberId, 'active']);
$member = $stmt->fetch();

if (!$member) {
    unset($_SESSION['member']);
    json_response(['error' => 'บัญชีไม่พร้อมใช้งาน'], 403);
}

if ((int) $member['spins_remaining'] <= 0) {
    json_response(['error' => 'คุณใช้สิทธิ์หมุนวงล้อครบแล้ว'], 400);
}

$firstName = trim($member['first_name'] ?? '');
$lastName = trim($member['last_name'] ?? '');
if ($firstName === '' || $lastName === '') {
    $parts = preg_split('/\s+/', trim($member['name'] ?? ''), 2);
    $firstName = $parts[0] ?? 'สมาชิก';
    $lastName = $parts[1] ?? '';
}

$pdo = db();
$pdo->prepare(
    'INSERT INTO wheel_registrations
     (member_id, first_name, last_name, phone, national_id, birth_date, email, consent_at, ip_address, user_agent)
     VALUES (?,?,?,?,?,?,?,?,?,?)'
)->execute([
    $memberId,
    $firstName,
    $lastName,
    $member['phone'],
    $member['national_id'] ?? null,
    $member['birth_date'] ?? null,
    $member['email'] ?? null,
    date('Y-m-d H:i:s'),
    $_SERVER['REMOTE_ADDR'] ?? null,
    substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255) ?: null,
]);

$registrationId = (int) $pdo->lastInsertId();
unset($member['password_hash']);
$_SESSION['member'] = array_merge($sessionMember, $member);
$_SESSION['wheel_registration_id'] = $registrationId;
$_SESSION['wheel_spin_unlocked'] = true;

json_response([
    'ok' => true,
    'registration_id' => $registrationId,
    'member' => [
        'id' => $memberId,
        'name' => $member['name'],
        'first_name' => $firstName,
        'last_name' => $lastName,
        'login_id' => $member['login_id'] ?? '',
        'phone' => $member['phone'],
        'spins_remaining' => (int) $member['spins_remaining'],
        'points' => (int) ($member['points'] ?? 0),
        'tier_name' => $member['tier_name'] ?? 'ทั่วไป',
        'tier_code' => $member['tier_code'] ?? 'general',
    ],
]);
