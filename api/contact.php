<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = read_json_body();
$name = trim($data['name'] ?? $_POST['name'] ?? '');
$phone = trim($data['phone'] ?? $_POST['phone'] ?? '');
$interest = trim($data['interest'] ?? $_POST['interest'] ?? '');
$message = trim($data['message'] ?? $_POST['message'] ?? '');
$plan = trim($data['plan'] ?? $_POST['plan'] ?? '');

if ($name === '' || $phone === '') {
    json_response(['error' => 'กรุณากรอกชื่อและเบอร์โทร'], 422);
}

try {
    $stmt = db()->prepare(
        'INSERT INTO leads (name, phone, interest, message, source, plan_ref, status) VALUES (?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $name,
        $phone,
        $interest ?: null,
        $message ?: null,
        'contact_form',
        $plan ?: null,
        'new',
    ]);
    $leadId = (int) db()->lastInsertId();

    notify_admin_submission('Lead ใหม่: ' . $name, [
        'ชื่อ' => $name,
        'เบอร์' => $phone,
        'ความสนใจ' => $interest,
        'แผน' => $plan,
        'ข้อความ' => $message,
        'แหล่ง' => 'ฟอร์มติดต่อ',
        'Lead ID' => $leadId,
    ]);

    json_response(['ok' => true, 'message' => 'ขอบคุณครับ ทีมงานจะติดต่อกลับโดยเร็วที่สุด']);
} catch (Throwable $e) {
    json_response(['error' => 'ไม่สามารถบันทึกได้ กรุณาลองใหม่'], 500);
}
