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

    $notify = setting_get('notify_email', '');
    if ($notify !== '' && filter_var($notify, FILTER_VALIDATE_EMAIL)) {
        $subject = '[BOYINSURE] Lead ใหม่: ' . $name;
        $body = "ชื่อ: {$name}\nเบอร์: {$phone}\nความสนใจ: {$interest}\nแผน: {$plan}\nข้อความ: {$message}";
        @mail($notify, $subject, $body, 'From: ' . (setting_get('contact_email', 'noreply@localhost') ?: 'noreply@localhost'));
    }

    json_response(['ok' => true, 'message' => 'ขอบคุณครับ ทีมงานจะติดต่อกลับโดยเร็วที่สุด']);
} catch (Throwable $e) {
    json_response(['error' => 'ไม่สามารถบันทึกได้ กรุณาลองใหม่'], 500);
}
