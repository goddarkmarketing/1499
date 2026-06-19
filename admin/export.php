<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();
admin_require_roles($admin, ['super_admin', 'ops', 'sales_manager', 'support']);

$type = $_GET['type'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$whereDate = '';
$params = [];
if ($from !== '') {
    $whereDate .= ' AND DATE(created_at) >= ?';
    $params[] = $from;
}
if ($to !== '') {
    $whereDate .= ' AND DATE(created_at) <= ?';
    $params[] = $to;
}

match ($type) {
    'leads' => (function () use ($whereDate, $params) {
        $sql = "SELECT created_at, name, phone, interest, source, plan_ref, status, notes
                FROM leads WHERE 1=1{$whereDate} ORDER BY created_at DESC LIMIT 5000";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        csv_output('leads.csv', ['วันที่', 'ชื่อ', 'เบอร์', 'สนใจ', 'แหล่ง', 'แผน', 'สถานะ', 'หมายเหตุ'], $stmt->fetchAll(PDO::FETCH_NUM));
    })(),
    'members' => (function () use ($whereDate, $params) {
        $sql = "SELECT m.created_at, m.name, m.phone, m.email, mt.name, m.spins_remaining, m.status
                FROM members m LEFT JOIN member_tiers mt ON mt.id = m.tier_id
                WHERE 1=1" . str_replace('created_at', 'm.created_at', $whereDate) . ' ORDER BY m.created_at DESC LIMIT 5000';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        csv_output('members.csv', ['วันที่', 'ชื่อ', 'เบอร์', 'อีเมล', 'ระดับ', 'สิทธิ์หมุน', 'สถานะ'], $stmt->fetchAll(PDO::FETCH_NUM));
    })(),
    'plays' => (function () use ($whereDate, $params) {
        $sql = "SELECT s.created_at, m.name, m.phone, g.name, p.name, c.name
                FROM spin_logs s
                JOIN members m ON m.id = s.member_id
                LEFT JOIN games g ON g.id = s.game_id
                JOIN prizes p ON p.id = s.prize_id
                LEFT JOIN campaigns c ON c.id = s.campaign_id
                WHERE 1=1" . str_replace('created_at', 's.created_at', $whereDate) . ' ORDER BY s.created_at DESC LIMIT 5000';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        csv_output('plays.csv', ['วันที่', 'ลูกค้า', 'เบอร์', 'เกม', 'รางวัล', 'แคมเปญ'], $stmt->fetchAll(PDO::FETCH_NUM));
    })(),
    'rewards' => (function () use ($whereDate, $params) {
        $sql = "SELECT rc.created_at, m.name, m.phone, p.name, rc.status, rc.notes
                FROM reward_claims rc
                JOIN members m ON m.id = rc.member_id
                JOIN prizes p ON p.id = rc.prize_id
                WHERE 1=1" . str_replace('created_at', 'rc.created_at', $whereDate) . ' ORDER BY rc.created_at DESC LIMIT 5000';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        csv_output('rewards.csv', ['วันที่', 'ลูกค้า', 'เบอร์', 'รางวัล', 'สถานะ', 'หมายเหตุ'], $stmt->fetchAll(PDO::FETCH_NUM));
    })(),
    default => (function () {
        header('Location: reports.php');
        exit;
    })(),
};
