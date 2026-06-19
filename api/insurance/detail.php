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
    "SELECT p.*, c.slug AS category_slug, c.title AS category_title, c.tagline AS category_tagline,
            c.icon AS category_icon, c.detail_json AS category_detail_json
     FROM insurance_plans p
     JOIN insurance_categories c ON c.id = p.category_id
     WHERE p.slug = ? AND p.status = 'active' AND c.status = 'active' LIMIT 1"
);
$stmt->execute([$slug]);
$row = $stmt->fetch();
if (!$row) {
    json_response(['error' => 'not found'], 404);
}

$category = [
    'id' => $row['category_slug'],
    'title' => $row['category_title'],
    'tagline' => $row['category_tagline'],
    'icon' => $row['category_icon'] ?: 'shield',
    'detail_json' => $row['category_detail_json'],
];

$features = [];
if (!empty($row['features_json'])) {
    $features = json_decode((string) $row['features_json'], true) ?: [];
}

$plan = [
    'id' => $row['slug'],
    'name' => $row['name'],
    'desc' => $row['description'],
    'description' => $row['description'],
    'image' => $row['image_path'],
    'featured' => (bool) $row['featured'],
    'features' => $features,
    'detail_json' => $row['detail_json'],
];

$detail = insurance_plan_detail_merged($row, $category);
$detail['coverageDetails'] = $detail['coverage'] ?? $features;
unset($detail['coverage']);

json_response([
    'category' => [
        'id' => $category['id'],
        'title' => $category['title'],
        'tagline' => $category['tagline'],
        'icon' => $category['icon'],
        'plans' => [],
    ],
    'plan' => [
        'id' => $plan['id'],
        'name' => $plan['name'],
        'desc' => $plan['desc'],
        'image' => $plan['image'],
        'featured' => $plan['featured'],
        'features' => $plan['features'],
    ],
    'detail' => $detail,
    'source' => 'database',
]);
