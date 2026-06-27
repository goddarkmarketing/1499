<?php

declare(strict_types=1);

require_once __DIR__ . '/../admin/includes/auth.php';
require_once __DIR__ . '/../admin/includes/backup.php';

$result = backup_create_full(['id' => 1, 'name' => 'CLI Test', 'email' => 'admin@boyinsure.com']);
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
