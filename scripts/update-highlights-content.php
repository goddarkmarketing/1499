<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$path = dirname(__DIR__) . '/database/cms-site.json';
if (!is_file($path)) {
    fwrite(STDERR, "ไม่พบ database/cms-site.json\n");
    exit(1);
}

$data = json_decode((string) file_get_contents($path), true);
if (!is_array($data) || empty($data['highlights'])) {
    fwrite(STDERR, "ไม่พบ block highlights ใน cms-site.json\n");
    exit(1);
}

$block = $data['highlights'];
$body = $block['body'] ?? $block;
$title = $block['title'] ?? 'highlights';

site_content_set('highlights', $body, $title);

$count = isset($body['items']) && is_array($body['items']) ? count($body['items']) : 0;
echo "อัปเดต highlights ลงฐานข้อมูลแล้ว ({$count} รายการ)\n";
