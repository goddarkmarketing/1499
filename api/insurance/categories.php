<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['ok' => true]);
}

$categories = db()->query(
    "SELECT * FROM insurance_categories WHERE status = 'active' ORDER BY sort_order, id"
)->fetchAll();

$plans = db()->query(
    "SELECT p.*, c.slug AS category_slug FROM insurance_plans p
     JOIN insurance_categories c ON c.id = p.category_id
     WHERE p.status = 'active' AND c.status = 'active'
     ORDER BY p.sort_order, p.id"
)->fetchAll();

$grouped = [];
foreach ($categories as $cat) {
    $grouped[$cat['slug']] = [
        'id' => $cat['slug'],
        'title' => $cat['title'],
        'tagline' => $cat['tagline'],
        'icon' => $cat['icon'] ?: 'shield',
        'plans' => [],
    ];
}

foreach ($plans as $p) {
    $catSlug = $p['category_slug'];
    if (!isset($grouped[$catSlug])) {
        continue;
    }
    $features = [];
    if (!empty($p['features_json'])) {
        $features = json_decode((string) $p['features_json'], true) ?: [];
    }
    $grouped[$catSlug]['plans'][] = [
        'id' => $p['slug'],
        'name' => $p['name'],
        'desc' => $p['description'],
        'image' => $p['image_path'],
        'featured' => (bool) $p['featured'],
        'features' => $features,
    ];
}

$result = array_values(array_filter($grouped, fn($c) => count($c['plans']) > 0));

json_response([
    'categories' => $result,
    'source' => count($result) > 0 ? 'database' : 'empty',
]);
