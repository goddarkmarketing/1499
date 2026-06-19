<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

$game = game_by_code('lucky_wheel');
$gameId = $game ? (int) $game['id'] : 0;

$stmt = db()->prepare(
    "SELECT id, name, short_name, detail, logo_path, color, prize_type, weight
     FROM prizes WHERE status = 'active' AND wheel_enabled = 1 AND game_id = ?
     ORDER BY sort_order"
);
$stmt->execute([$gameId ?: 1]);
$prizes = $stmt->fetchAll();

json_response(['prizes' => $prizes]);
