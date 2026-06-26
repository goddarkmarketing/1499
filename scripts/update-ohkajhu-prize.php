<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$stmt = db()->prepare(
    "UPDATE prizes SET detail = ? WHERE short_name = ? OR name LIKE ?"
);
$stmt->execute([
    'Voucher โอ้กะจู๋ มูลค่า 1,000 บาท',
    'โอ้กะจู๋',
    '%โอ้กะจู๋%',
]);

echo 'updated rows: ' . $stmt->rowCount() . PHP_EOL;
