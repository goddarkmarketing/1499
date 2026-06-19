<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

$key = trim($_GET['key'] ?? '');
$content = [];

if ($key !== '') {
    $block = site_content_get($key);
    if ($block) {
        $content[$key] = $block;
    }
} else {
    $rows = db()->query('SELECT content_key, body_json FROM site_content')->fetchAll();
    foreach ($rows as $row) {
        $decoded = json_decode((string) $row['body_json'], true);
        if (is_array($decoded)) {
            $content[$row['content_key']] = $decoded;
        }
    }
}

json_response([
    'settings' => public_site_settings(),
    'content' => $content,
    'source' => $content ? 'database' : 'empty',
]);
