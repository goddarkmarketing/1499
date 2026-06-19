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
$name = trim($data['name'] ?? '');

if ($phone === '' || $name === '') {
    json_response(['error' => 'กรุณากรอกชื่อและเบอร์โทร'], 422);
}

$tier = db()->query("SELECT id, spin_quota FROM member_tiers WHERE code='general' LIMIT 1")->fetch();
$tierId = $tier['id'] ?? null;
$spins = $tier ? (int) $tier['spin_quota'] : 1;

$stmt = db()->prepare('SELECT * FROM members WHERE phone = ? LIMIT 1');
$stmt->execute([$phone]);
$member = $stmt->fetch();

if (!$member) {
    db()->prepare('INSERT INTO members (name, phone, tier_id, status, spins_remaining) VALUES (?,?,?,?,?)')
        ->execute([$name, $phone, $tierId, 'active', $spins]);
    $memberId = (int) db()->lastInsertId();
} else {
    $memberId = (int) $member['id'];
    if ($member['name'] !== $name) {
        db()->prepare('UPDATE members SET name = ? WHERE id = ?')->execute([$name, $memberId]);
    }
}

$stmt = db()->prepare(
    'SELECT m.*, mt.name AS tier_name, mt.code AS tier_code, mt.spin_quota
     FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id WHERE m.id = ?'
);
$stmt->execute([$memberId]);
$member = $stmt->fetch();

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
