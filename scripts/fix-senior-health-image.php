<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

$image = 'assets/img/products/senior-health.jpg';
$stmt = db()->prepare('UPDATE insurance_plans SET image_path = ? WHERE slug = ?');
$stmt->execute([$image, 'senior-health']);

echo json_encode([
    'ok' => true,
    'updated' => $stmt->rowCount(),
    'image_path' => $image,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
