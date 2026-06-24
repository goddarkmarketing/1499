<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

start_session(app_config('session.member_key'));

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

$sessionMember = $_SESSION['member'] ?? null;
if (!$sessionMember) {
    json_response(['logged_in' => false], 401);
}

$memberId = (int) $sessionMember['id'];
$stmt = db()->prepare(
    'SELECT m.id, m.name, m.first_name, m.last_name, m.login_id, m.phone, m.email,
            m.national_id, m.birth_date, m.spins_remaining, m.points, m.status, m.created_at,
            mt.name AS tier_name, mt.code AS tier_code
     FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id WHERE m.id = ?'
);
$stmt->execute([$memberId]);
$row = $stmt->fetch();

if (!$row || $row['status'] !== 'active') {
    unset($_SESSION['member']);
    json_response(['logged_in' => false], 401);
}

$rewardsStmt = db()->prepare(
    'SELECT rc.id, rc.status, rc.created_at, rc.updated_at,
            p.name, p.short_name, p.detail, p.logo_path, p.color
     FROM reward_claims rc JOIN prizes p ON p.id = rc.prize_id
     WHERE rc.member_id = ? ORDER BY rc.created_at DESC LIMIT 50'
);
$rewardsStmt->execute([$memberId]);

$spinsStmt = db()->prepare(
    'SELECT s.id, s.created_at, g.name AS game_name, p.name AS prize_name, p.short_name
     FROM spin_logs s
     LEFT JOIN games g ON g.id = s.game_id
     JOIN prizes p ON p.id = s.prize_id
     WHERE s.member_id = ? ORDER BY s.created_at DESC LIMIT 50'
);
$spinsStmt->execute([$memberId]);

$regStmt = db()->prepare(
    'SELECT id, created_at, spin_log_id FROM wheel_registrations
     WHERE member_id = ? ORDER BY created_at DESC LIMIT 20'
);
$regStmt->execute([$memberId]);

$statsStmt = db()->prepare(
    'SELECT
        (SELECT COUNT(*) FROM spin_logs WHERE member_id = ?) AS total_spins,
        (SELECT COUNT(*) FROM reward_claims WHERE member_id = ?) AS total_rewards'
);
$statsStmt->execute([$memberId, $memberId]);
$stats = $statsStmt->fetch() ?: ['total_spins' => 0, 'total_rewards' => 0];

json_response([
    'logged_in' => true,
    'member' => [
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'first_name' => $row['first_name'] ?? '',
        'last_name' => $row['last_name'] ?? '',
        'login_id' => $row['login_id'] ?? '',
        'phone' => $row['phone'],
        'email' => $row['email'] ?? '',
        'national_id' => $row['national_id'] ?? '',
        'birth_date' => $row['birth_date'] ?? '',
        'spins_remaining' => (int) $row['spins_remaining'],
        'points' => (int) $row['points'],
        'tier_name' => $row['tier_name'] ?? 'ทั่วไป',
        'tier_code' => $row['tier_code'] ?? 'general',
        'created_at' => $row['created_at'],
    ],
    'stats' => [
        'total_spins' => (int) $stats['total_spins'],
        'total_rewards' => (int) $stats['total_rewards'],
    ],
    'rewards' => $rewardsStmt->fetchAll(),
    'spins' => $spinsStmt->fetchAll(),
    'registrations' => $regStmt->fetchAll(),
]);
