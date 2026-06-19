<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

$limit = min(50, max(1, (int) ($_GET['limit'] ?? 10)));

$rows = db()->query(
    "SELECT s.created_at, m.name, p.name AS prize_name, p.short_name
     FROM spin_logs s
     JOIN members m ON m.id = s.member_id
     JOIN prizes p ON p.id = s.prize_id
     ORDER BY s.created_at DESC
     LIMIT {$limit}"
)->fetchAll();

$winners = array_map(function ($row) {
    return [
        'name' => mask_name($row['name']),
        'prize' => $row['prize_name'],
        'time' => date('d/m/Y H:i', strtotime($row['created_at'])),
    ];
}, $rows);

json_response(['winners' => $winners]);
