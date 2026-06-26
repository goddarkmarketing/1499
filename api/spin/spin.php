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

if (empty($_SESSION['wheel_spin_unlocked']) || empty($_SESSION['wheel_registration_id'])) {
    json_response(['error' => 'กรุณาลงทะเบียนก่อนหมุนวงล้อ'], 401);
}

$member = $_SESSION['member'] ?? null;
if (!$member) {
    json_response(['error' => 'กรุณาลงทะเบียนก่อนหมุนวงล้อ'], 401);
}

$registrationId = (int) $_SESSION['wheel_registration_id'];
$memberId = (int) $member['id'];
$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare('SELECT * FROM members WHERE id = ? FOR UPDATE');
    $stmt->execute([$memberId]);
    $m = $stmt->fetch();
    if (!$m || $m['status'] !== 'active') {
        throw new RuntimeException('บัญชีไม่พร้อมใช้งาน');
    }

    $stmt = $pdo->prepare('SELECT id FROM wheel_registrations WHERE id = ? AND member_id = ? AND spin_log_id IS NULL LIMIT 1 FOR UPDATE');
    $stmt->execute([$registrationId, $memberId]);
    if (!$stmt->fetch()) {
        throw new RuntimeException('กรุณาลงทะเบียนก่อนหมุนวงล้อ');
    }

    $game = game_by_code('lucky_wheel');
    if (!$game || $game['status'] !== 'active') {
        throw new RuntimeException('เกมไม่พร้อมใช้งาน');
    }
    $gameId = (int) $game['id'];

    $playsLeft = member_game_plays($memberId, $gameId);
    if ($playsLeft <= 0) {
        throw new RuntimeException('ไม่มีสิทธิ์หมุนเหลือ');
    }

    $stmt = $pdo->prepare(
        "SELECT * FROM prizes WHERE status = 'active' AND wheel_enabled = 1 AND game_id = ? FOR UPDATE"
    );
    $stmt->execute([$gameId]);
    $prizes = $stmt->fetchAll();
    $prize = pick_weighted_prize($prizes);
    if (!$prize) {
        throw new RuntimeException('ไม่มีรางวัลในขณะนี้');
    }

    member_game_deduct($memberId, $gameId);

    if ($prize['stock'] !== null) {
        prize_decrement_stock((int) $prize['id']);
    }

    $campaign = $pdo->query("SELECT id FROM campaigns WHERE status='active' ORDER BY id LIMIT 1")->fetch();
    $campaignId = $campaign['id'] ?? null;

    $pdo->prepare('INSERT INTO spin_logs (member_id, registration_id, game_id, prize_id, campaign_id, ip_address) VALUES (?,?,?,?,?,?)')
        ->execute([$memberId, $registrationId, $gameId, $prize['id'], $campaignId, $_SERVER['REMOTE_ADDR'] ?? null]);

    $spinId = (int) $pdo->lastInsertId();

    $pdo->prepare('UPDATE wheel_registrations SET spin_log_id = ? WHERE id = ?')
        ->execute([$spinId, $registrationId]);

    $pdo->prepare('INSERT INTO reward_claims (member_id, prize_id, spin_log_id, status) VALUES (?,?,?,?)')
        ->execute([$memberId, $prize['id'], $spinId, 'won']);
    $claimId = (int) $pdo->lastInsertId();

    $pdo->commit();

    $stmt = $pdo->prepare('SELECT spins_remaining FROM members WHERE id = ? LIMIT 1');
    $stmt->execute([$memberId]);
    $spinsLeft = (int) $stmt->fetchColumn();

    unset($_SESSION['wheel_spin_unlocked'], $_SESSION['wheel_registration_id']);
    if (!empty($_SESSION['member'])) {
        $_SESSION['member']['spins_remaining'] = $spinsLeft;
    }

    json_response([
        'ok' => true,
        'claim_id' => $claimId,
        'prize' => [
            'id' => (int) $prize['id'],
            'name' => $prize['name'],
            'short_name' => $prize['short_name'],
            'detail' => $prize['detail'],
            'logo' => $prize['logo_path'],
            'color' => $prize['color'],
            'prize_type' => $prize['prize_type'] ?? 'voucher',
        ],
        'spins_remaining' => $spinsLeft,
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_response(['error' => $e->getMessage()], 400);
}
