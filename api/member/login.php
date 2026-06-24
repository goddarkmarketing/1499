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
$loginId = normalize_login_id($data['login_id'] ?? $data['loginId'] ?? '');
$password = (string) ($data['password'] ?? '');

if ($loginId === '' || $password === '') {
    json_response(['error' => 'กรุณากรอกไอดีและรหัสผ่าน'], 422);
}

$stmt = db()->prepare(
    'SELECT m.*, mt.name AS tier_name, mt.code AS tier_code
     FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id
     WHERE m.login_id = ? LIMIT 1'
);
$stmt->execute([$loginId]);
$member = $stmt->fetch();

if (!$member) {
    json_response(['error' => 'ไอดีหรือรหัสผ่านไม่ถูกต้อง'], 401);
}

if ($member['status'] !== 'active') {
    json_response(['error' => 'บัญชีถูกระงับ กรุณาติดต่อทีมงาน'], 403);
}

if (empty($member['password_hash']) || !password_verify($password, $member['password_hash'])) {
    json_response(['error' => 'ไอดีหรือรหัสผ่านไม่ถูกต้อง'], 401);
}

unset($member['password_hash']);
$_SESSION['member'] = $member;

json_response([
    'ok' => true,
    'member' => [
        'id' => (int) $member['id'],
        'name' => $member['name'],
        'first_name' => $member['first_name'] ?? '',
        'last_name' => $member['last_name'] ?? '',
        'login_id' => $member['login_id'] ?? $loginId,
        'phone' => $member['phone'],
        'spins_remaining' => (int) $member['spins_remaining'],
        'points' => (int) ($member['points'] ?? 0),
        'tier_name' => $member['tier_name'] ?? 'ทั่วไป',
        'tier_code' => $member['tier_code'] ?? 'general',
    ],
]);
