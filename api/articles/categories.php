<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

$categories = db()->query(
    "SELECT id, slug, title, tagline, icon, sort_order FROM article_categories
     WHERE status = 'active' ORDER BY sort_order"
)->fetchAll();

$articles = db()->query(
    "SELECT a.slug, a.title, a.excerpt, a.image_path, a.read_time, a.featured, a.published_at,
            c.slug AS category_slug, c.title AS category_title
     FROM articles a
     LEFT JOIN article_categories c ON c.id = a.category_id
     WHERE a.status = 'published'
     ORDER BY a.featured DESC, a.published_at DESC"
)->fetchAll();

$grouped = [];
foreach ($categories as $cat) {
    $grouped[$cat['slug']] = [
        'id' => $cat['slug'],
        'title' => $cat['title'],
        'tagline' => $cat['tagline'],
        'icon' => $cat['icon'],
        'articles' => [],
    ];
}

foreach ($articles as $a) {
    $catSlug = $a['category_slug'] ?: 'general';
    if (!isset($grouped[$catSlug])) {
        $grouped[$catSlug] = [
            'id' => $catSlug,
            'title' => $catSlug,
            'tagline' => '',
            'icon' => 'file-text',
            'articles' => [],
        ];
    }
    $grouped[$catSlug]['articles'][] = [
        'id' => $a['slug'],
        'title' => $a['title'],
        'excerpt' => $a['excerpt'],
        'image' => $a['image_path'],
        'readTime' => format_article_read_time($a['read_time']),
        'date' => format_thai_date_short($a['published_at']),
        'categoryLabel' => $a['category_title'] ?: $grouped[$catSlug]['title'],
        'featured' => (bool) $a['featured'],
    ];
}

json_response([
    'categories' => array_values(array_filter($grouped, fn($c) => count($c['articles']) > 0)),
    'source' => 'database',
]);
