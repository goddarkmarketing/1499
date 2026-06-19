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
$phone = preg_replace('/\D+/', '', trim($data['phone'] ?? ''));

if ($phone === '') {
    json_response(['error' => 'กรุณากรอกเบอร์โทร'], 422);
}

$stmt = db()->prepare(
    'SELECT m.*, mt.name AS tier_name, mt.code AS tier_code
     FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id WHERE m.phone = ? LIMIT 1'
);
$stmt->execute([$phone]);
$member = $stmt->fetch();

if (!$member) {
    json_response(['error' => 'ไม่พบสมาชิก กรุณาสมัครสมาชิก'], 404);
}

if ($member['status'] !== 'active') {
    json_response(['error' => 'บัญชีถูกระงับ กรุณาติดต่อทีมงาน'], 403);
}

unset($member['password_hash']);
$_SESSION['member'] = $member;

json_response([
    'ok' => true,
    'member' => [
        'id' => (int) $member['id'],
        'name' => $member['name'],
        'phone' => $member['phone'],
        'spins_remaining' => (int) $member['spins_remaining'],
        'tier_name' => $member['tier_name'] ?? 'ทั่วไป',
        'tier_code' => $member['tier_code'] ?? 'general',
    ],
]);
