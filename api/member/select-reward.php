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
    json_response(['error' => 'กรุณาเข้าสู่ระบบ'], 401);
}
$memberId = (int) $sessionMember['id'];

$data = read_json_body();
$chosenId = (int) ($data['claim_id'] ?? 0);
$roundIds = array_values(array_unique(array_map('intval', (array) ($data['claim_ids'] ?? []))));

if ($chosenId <= 0) {
    json_response(['error' => 'ไม่พบรางวัลที่เลือก'], 422);
}
if (!in_array($chosenId, $roundIds, true)) {
    $roundIds[] = $chosenId;
}

$pdo = db();
$pdo->beginTransaction();
try {
    $placeholders = implode(',', array_fill(0, count($roundIds), '?'));
    $stmt = $pdo->prepare(
        "SELECT id FROM reward_claims
         WHERE member_id = ? AND id IN ($placeholders) FOR UPDATE"
    );
    $stmt->execute(array_merge([$memberId], $roundIds));
    $ownedIds = array_map('intval', array_column($stmt->fetchAll(), 'id'));

    if (!in_array($chosenId, $ownedIds, true)) {
        throw new RuntimeException('ไม่พบรางวัลที่เลือกในบัญชีของคุณ');
    }

    $pdo->prepare('UPDATE reward_claims SET selected = 1 WHERE id = ? AND member_id = ?')
        ->execute([$chosenId, $memberId]);

    $others = array_values(array_diff($ownedIds, [$chosenId]));
    if ($others) {
        $ph = implode(',', array_fill(0, count($others), '?'));
        $pdo->prepare("UPDATE reward_claims SET selected = 0 WHERE member_id = ? AND id IN ($ph)")
            ->execute(array_merge([$memberId], $others));
    }

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
    json_response(['error' => 'บันทึกการเลือกรางวัลไม่สำเร็จ'], 500);
}

$stmt = db()->prepare(
    'SELECT rc.id, rc.status, rc.selected, p.name, p.short_name, p.detail, p.logo_path, p.color, p.prize_type
     FROM reward_claims rc JOIN prizes p ON p.id = rc.prize_id
     WHERE rc.id = ? LIMIT 1'
);
$stmt->execute([$chosenId]);
$claim = $stmt->fetch();

json_response([
    'ok' => true,
    'claim' => [
        'id' => (int) $claim['id'],
        'status' => $claim['status'],
        'name' => $claim['name'],
        'short_name' => $claim['short_name'],
        'detail' => $claim['detail'],
        'logo' => $claim['logo_path'],
        'color' => $claim['color'],
        'prize_type' => $claim['prize_type'],
    ],
]);
