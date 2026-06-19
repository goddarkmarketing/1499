<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

start_session(app_config('session.member_key'));

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

$member = $_SESSION['member'] ?? null;
if (!$member) {
    json_response(['logged_in' => false]);
}

$stmt = db()->prepare(
    'SELECT m.id, m.name, m.phone, m.spins_remaining, m.points, mt.name AS tier_name, mt.code AS tier_code
     FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id WHERE m.id = ?'
);
$stmt->execute([(int) $member['id']]);
$row = $stmt->fetch();

if (!$row) {
    unset($_SESSION['member']);
    json_response(['logged_in' => false]);
}

$rewards = db()->prepare(
    'SELECT rc.id, rc.status, rc.created_at, p.name, p.short_name, p.detail, p.logo_path, p.color
     FROM reward_claims rc JOIN prizes p ON p.id = rc.prize_id
     WHERE rc.member_id = ? ORDER BY rc.created_at DESC'
);
$rewards->execute([(int) $row['id']]);

json_response([
    'logged_in' => true,
    'member' => [
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'phone' => $row['phone'],
        'spins_remaining' => (int) $row['spins_remaining'],
        'points' => (int) $row['points'],
        'tier_name' => $row['tier_name'] ?? 'ทั่วไป',
        'tier_code' => $row['tier_code'] ?? 'general',
    ],
    'rewards' => $rewards->fetchAll(),
]);
