<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$rows = db()->query(
    'SELECT id, name, login_id, phone, spins_remaining, created_at
     FROM members ORDER BY id DESC LIMIT 20'
)->fetchAll();

echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
