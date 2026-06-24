<?php

declare(strict_types=1);

$config = require dirname(__DIR__) . '/config.php';
date_default_timezone_set($config['app']['timezone']);

function app_config(string $key = null) {
    static $cfg;
    if (!$cfg) {
        $cfg = require dirname(__DIR__) . '/config.php';
    }
    if ($key === null) {
        return $cfg;
    }
    $parts = explode('.', $key);
    $val = $cfg;
    foreach ($parts as $p) {
        if (!isset($val[$p])) {
            return null;
        }
        $val = $val[$p];
    }
    return $val;
}

function db(): PDO {
    static $pdo;
    if ($pdo) {
        return $pdo;
    }
    $c = app_config('db');
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $c['host'],
        $c['port'],
        $c['name'],
        $c['charset']
    );
    $pdo = new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function json_response(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function read_json_body(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) {
        return $_POST ?: [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function normalize_phone(string $phone): string {
    return preg_replace('/\D+/', '', trim($phone));
}

function validate_thai_phone(string $phone): bool {
    return (bool) preg_match('/^0\d{9}$/', $phone);
}

function validate_thai_national_id(string $id): bool {
    if (!preg_match('/^\d{13}$/', $id)) {
        return false;
    }
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $sum += (int) $id[$i] * (13 - $i);
    }
    $check = (11 - ($sum % 11)) % 10;
    return $check === (int) $id[12];
}

function validate_birth_date(string $date): bool {
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dt || $dt->format('Y-m-d') !== $date) {
        return false;
    }
    $today = new DateTime('today');
    if ($dt > $today) {
        return false;
    }
    $age = $dt->diff($today)->y;
    return $age >= 15 && $age <= 120;
}

function normalize_login_id(string $loginId): string {
    return strtolower(trim($loginId));
}

function validate_login_id(string $loginId): bool {
    return (bool) preg_match('/^[a-zA-Z0-9._-]{4,32}$/', $loginId);
}

function validate_member_password(string $password): bool {
    return strlen($password) >= 6;
}

function start_session(string $key): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name($key);
        session_start();
    }
}

function log_activity(?int $adminId, string $action, ?string $entityType = null, ?int $entityId = null, ?string $detail = null): void {
    try {
        $stmt = db()->prepare(
            'INSERT INTO activity_logs (admin_id, action, entity_type, entity_id, detail) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$adminId, $action, $entityType, $entityId, $detail]);
    } catch (Throwable $e) {
        // ignore logging failures
    }
}

function status_labels(): array {
    return [
        'lead' => [
            'new' => 'ใหม่',
            'contacted' => 'ติดต่อแล้ว',
            'following' => 'กำลังติดตาม',
            'closed_won' => 'ปิดการขาย',
            'closed_lost' => 'ไม่สนใจ',
        ],
        'application' => [
            'submitted' => 'ส่งใบสมัคร',
            'contact_pending' => 'รอติดต่อ',
            'interview' => 'นัดสัมภาษณ์',
            'training' => 'อบรม',
            'exam' => 'สอบใบอนุญาต',
            'approved' => 'อนุมัติ',
            'rejected' => 'ปฏิเสธ',
            'cancelled' => 'ยกเลิก',
        ],
        'reward' => [
            'won' => 'ได้รางวัล',
            'pending_verify' => 'รอตรวจสอบ',
            'approved' => 'อนุมัติ',
            'shipping' => 'กำลังจัดส่ง',
            'sent' => 'ส่งแล้ว',
            'redeemed' => 'ใช้แล้ว',
            'rejected' => 'ปฏิเสธ',
            'expired' => 'หมดอายุ',
        ],
        'member' => [
            'pending' => 'รอยืนยัน',
            'active' => 'ใช้งาน',
            'suspended' => 'ระงับ',
            'closed' => 'ปิด',
        ],
        'agent' => [
            'active' => 'ใช้งาน',
            'suspended' => 'ระงับ',
            'inactive' => 'ไม่ใช้งาน',
        ],
    ];
}

function pick_weighted_prize(array $prizes): ?array {
    $pool = array_filter($prizes, fn($p) => $p['status'] === 'active' && $p['wheel_enabled']);
    if (!$pool) {
        return null;
    }
    $total = array_sum(array_column($pool, 'weight'));
    $rand = random_int(1, max(1, $total));
    $acc = 0;
    foreach ($pool as $prize) {
        $acc += (int) $prize['weight'];
        if ($rand <= $acc) {
            return $prize;
        }
    }
    return $pool[array_key_first($pool)];
}

function mask_name(string $name): string {
    $parts = preg_split('/\s+/u', trim($name));
    if (count($parts) <= 1) {
        return mb_substr($name, 0, 1) . '***';
    }
    return $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '***';
}

function game_list(bool $activeOnly = false): array {
    $sql = 'SELECT * FROM games';
    if ($activeOnly) {
        $sql .= " WHERE status = 'active'";
    }
    $sql .= ' ORDER BY sort_order, id';
    return db()->query($sql)->fetchAll();
}

function game_by_code(string $code): ?array {
    $stmt = db()->prepare('SELECT * FROM games WHERE code = ? LIMIT 1');
    $stmt->execute([$code]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function game_type_label(string $type): string {
    return match ($type) {
        'wheel' => 'วงล้อ',
        'scratch' => 'ขูดบัตร',
        'quiz' => 'ทายคำถาม',
        default => 'อื่นๆ',
    };
}

function setting_get(string $key, ?string $default = null): ?string {
    try {
        $stmt = db()->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (string) $val : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function setting_set(string $key, ?string $value): void {
    db()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    )->execute([$key, $value]);
}

function mail_from_address(): string {
    return setting_get('contact_email', 'noreply@localhost') ?: 'noreply@localhost';
}

function send_plain_mail(string $to, string $subject, string $body): bool {
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $headers = 'From: ' . mail_from_address() . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
}

function send_member_credentials_email(string $email, string $name, string $loginId, string $password): bool {
    $subject = 'ข้อมูลเข้าสู่ระบบ BOYINSURE';
    $body = "เรียน {$name}\n\n"
        . "ขอบคุณที่สมัครใช้งาน BOYINSURE\n"
        . "ข้อมูลเข้าสู่ระบบของคุณมีดังนี้\n\n"
        . "ไอดี: {$loginId}\n"
        . "รหัสผ่าน: {$password}\n\n"
        . "กรุณาเก็บรักษาข้อมูลนี้ไว้เป็นความลับ\n"
        . "เข้าสู่ระบบได้ที่หน้าโปรโมชั่นและของรางวัล\n\n"
        . "BOYINSURE";
    return send_plain_mail($email, $subject, $body);
}

function notify_admin_new_member(array $member, string $loginId, ?string $plainPassword = null): bool {
    $notify = setting_get('notify_email', '');
    if ($notify === '' || !filter_var($notify, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $subject = '[BOYINSURE] สมาชิกใหม่: ' . ($member['name'] ?? '');
    $body = "มีการสมัครสมาชิกใหม่\n\n"
        . 'ชื่อ: ' . ($member['name'] ?? '-') . "\n"
        . 'เบอร์: ' . ($member['phone'] ?? '-') . "\n"
        . 'อีเมล: ' . ($member['email'] ?? '-') . "\n"
        . 'ไอดี: ' . $loginId . "\n";
    if ($plainPassword !== null && $plainPassword !== '') {
        $body .= 'รหัสผ่าน: ' . $plainPassword . "\n";
    }
    $body .= "\nดูรายละเอียดใน Admin > สมาชิก (ID: " . (int) ($member['id'] ?? 0) . ')';
    return send_plain_mail($notify, $subject, $body);
}

function member_game_plays(int $memberId, int $gameId): int {
    $stmt = db()->prepare(
        'SELECT plays_remaining FROM member_game_quotas WHERE member_id = ? AND game_id = ? LIMIT 1'
    );
    $stmt->execute([$memberId, $gameId]);
    $val = $stmt->fetchColumn();
    if ($val !== false) {
        return (int) $val;
    }
    $stmt = db()->prepare('SELECT spins_remaining FROM members WHERE id = ? LIMIT 1');
    $stmt->execute([$memberId]);
    return (int) ($stmt->fetchColumn() ?: 0);
}

function member_game_deduct(int $memberId, int $gameId): void {
    $stmt = db()->prepare(
        'SELECT id FROM member_game_quotas WHERE member_id = ? AND game_id = ? LIMIT 1'
    );
    $stmt->execute([$memberId, $gameId]);
    if ($stmt->fetch()) {
        db()->prepare(
            'UPDATE member_game_quotas SET plays_remaining = GREATEST(plays_remaining - 1, 0)
             WHERE member_id = ? AND game_id = ?'
        )->execute([$memberId, $gameId]);
    }
    db()->prepare(
        'UPDATE members SET spins_remaining = GREATEST(spins_remaining - 1, 0) WHERE id = ?'
    )->execute([$memberId]);
}

function prize_decrement_stock(int $prizeId): void {
    $stmt = db()->prepare('SELECT stock FROM prizes WHERE id = ? AND stock IS NOT NULL FOR UPDATE');
    $stmt->execute([$prizeId]);
    $stock = $stmt->fetchColumn();
    if ($stock === false) {
        return;
    }
    $newStock = max(0, (int) $stock - 1);
    $status = $newStock === 0 ? 'out_of_stock' : 'active';
    db()->prepare('UPDATE prizes SET stock = ?, status = ? WHERE id = ?')->execute([$newStock, $status, $prizeId]);
}

function csv_output(string $filename, array $headers, array $rows): void {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);
    foreach ($rows as $row) {
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

function format_thai_date_short(?string $datetime): ?string {
    if (!$datetime) {
        return null;
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return null;
    }
    $months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.', 5 => 'พ.ค.', 6 => 'มิ.ย.',
        7 => 'ก.ค.', 8 => 'ส.ค.', 9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.',
    ];
    $m = (int) date('n', $ts);
    return (int) date('j', $ts) . ' ' . ($months[$m] ?? '') . ' ' . date('Y', $ts);
}

function format_article_read_time(?string $readTime): ?string {
    if ($readTime === null || $readTime === '') {
        return null;
    }
    if (str_contains($readTime, 'อ่าน')) {
        return $readTime;
    }
    return 'อ่าน ' . $readTime;
}

require_once __DIR__ . '/cms-seed.php';
