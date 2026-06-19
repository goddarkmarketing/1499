<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    json_response(['error' => 'slug required'], 400);
}

$stmt = db()->prepare(
    "SELECT a.*, c.slug AS category_slug, c.title AS category_title, c.icon AS category_icon
     FROM articles a
     LEFT JOIN article_categories c ON c.id = a.category_id
     WHERE a.slug = ? AND a.status = 'published' LIMIT 1"
);
$stmt->execute([$slug]);
$row = $stmt->fetch();
if (!$row) {
    json_response(['error' => 'not found'], 404);
}

$body = null;
if (!empty($row['body_json'])) {
    $body = json_decode($row['body_json'], true);
}
if (!$body && !empty($row['body_html'])) {
    $body = [
        'date' => $row['published_at'] ? date('j M Y', strtotime($row['published_at'])) : '',
        'takeaways' => [],
        'sections' => [['type' => 'p', 'text' => strip_tags($row['body_html'])]],
    ];
}

json_response([
    'article' => [
        'id' => $row['slug'],
        'title' => $row['title'],
        'excerpt' => $row['excerpt'],
        'image' => $row['image_path'],
        'readTime' => $row['read_time'],
        'featured' => (bool) $row['featured'],
        'category' => [
            'id' => $row['category_slug'],
            'title' => $row['category_title'],
            'icon' => $row['category_icon'],
        ],
    ],
    'body' => $body,
    'source' => 'database',
]);
