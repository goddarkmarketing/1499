<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$loginId = $argv[1] ?? 'mmm';
$spins = (int) ($argv[2] ?? 2);
$resetClaims = in_array('--reset-claims', $argv, true);

$stmt = db()->prepare(
    'SELECT id, name, login_id, phone, spins_remaining FROM members
     WHERE login_id = ? OR login_id LIKE ? OR name LIKE ? OR phone LIKE ?
     ORDER BY (login_id = ?) DESC, id DESC LIMIT 1'
);
$like = '%' . $loginId . '%';
$stmt->execute([$loginId, $like, $like, $like, $loginId]);
$member = $stmt->fetch();

if (!$member) {
    fwrite(STDERR, "Member not found: {$loginId}\n");
    exit(1);
}

$memberId = (int) $member['id'];

db()->prepare('UPDATE members SET spins_remaining = ? WHERE id = ?')->execute([$spins, $memberId]);

if ($resetClaims) {
    db()->prepare('DELETE FROM reward_claims WHERE member_id = ?')->execute([$memberId]);
    db()->prepare('DELETE FROM spin_logs WHERE member_id = ?')->execute([$memberId]);
}

$member['spins_remaining'] = $spins;
echo json_encode([
    'ok' => true,
    'member' => $member,
    'reset_claims' => $resetClaims,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
