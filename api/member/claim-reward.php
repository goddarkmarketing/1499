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
$claimId = (int) ($data['claim_id'] ?? 0);
$insuranceInterest = trim((string) ($data['insurance_interest'] ?? ''));
$contactName = trim((string) ($data['contact_name'] ?? $data['recipient_name'] ?? ''));
$contactPhone = normalize_phone($data['contact_phone'] ?? $data['recipient_phone'] ?? '');
$contactLine = trim((string) ($data['contact_line'] ?? ''));
$contactNote = trim((string) ($data['contact_note'] ?? ''));
$message = trim((string) ($data['message'] ?? ''));
$consent = !empty($data['consent']);

if ($claimId <= 0) {
    json_response(['error' => 'ไม่พบรางวัลที่เลือก'], 422);
}
if ($insuranceInterest === '') {
    json_response(['error' => 'กรุณาเลือกประเภทประกันที่สนใจ'], 422);
}
if (!$consent) {
    json_response(['error' => 'กรุณายินยอมให้ทีมงานติดต่อกลับ'], 422);
}
if ($contactName === '') {
    json_response(['error' => 'กรุณากรอกชื่อ-นามสกุล'], 422);
}
if (!validate_thai_phone($contactPhone)) {
    json_response(['error' => 'กรุณากรอกเบอร์โทรให้ถูกต้อง'], 422);
}

$pdo = db();
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare(
        'SELECT rc.id, rc.status, p.name AS prize_name
         FROM reward_claims rc JOIN prizes p ON p.id = rc.prize_id
         WHERE rc.id = ? AND rc.member_id = ? FOR UPDATE'
    );
    $stmt->execute([$claimId, $memberId]);
    $claim = $stmt->fetch();
    if (!$claim) {
        throw new RuntimeException('ไม่พบรางวัลที่เลือกในบัญชีของคุณ');
    }

    $noteParts = [
        'รางวัล: ' . ($claim['prize_name'] ?? ''),
        'สนใจประกัน: ' . $insuranceInterest,
    ];
    if ($contactNote !== '') {
        $noteParts[] = 'ช่วงเวลาที่สะดวก: ' . $contactNote;
    }
    if ($message !== '') {
        $noteParts[] = 'หมายเหตุ: ' . $message;
    }
    $claimNotes = implode("\n", $noteParts);

    $pdo->prepare(
        "UPDATE reward_claims SET
            status = 'pending_verify',
            selected = 1,
            insurance_interest = ?,
            recipient_name = ?,
            recipient_phone = ?,
            contact_line = ?,
            contact_note = ?,
            notes = ?,
            claimed_at = NOW(),
            consent_at = NOW()
         WHERE id = ? AND member_id = ?"
    )->execute([
        $insuranceInterest,
        $contactName,
        $contactPhone,
        $contactLine !== '' ? $contactLine : null,
        $contactNote !== '' ? $contactNote : null,
        $claimNotes,
        $claimId,
        $memberId,
    ]);

    $leadMessage = $claimNotes;
    if ($contactLine !== '') {
        $leadMessage .= "\nLINE: " . $contactLine;
    }

    $pdo->prepare(
        'INSERT INTO leads (name, phone, interest, message, source, plan_ref, status)
         VALUES (?,?,?,?,?,?,?)'
    )->execute([
        $contactName,
        $contactPhone,
        $insuranceInterest,
        $leadMessage,
        'wheel_reward',
        'claim:' . $claimId,
        'new',
    ]);

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
    json_response(['error' => 'บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่'], 500);
}

json_response([
    'ok' => true,
    'claim_id' => $claimId,
    'status' => 'pending_verify',
    'message' => 'บันทึกข้อมูลเรียบร้อย ทีมงานจะติดต่อกลับเพื่อแนะนำแผนประกันและดำเนินการมอบรางวัล',
]);
