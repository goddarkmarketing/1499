<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/backup.php';

$admin = require_admin();
admin_require_roles($admin, ['super_admin']);

$filename = basename((string) ($_GET['file'] ?? ''));
$path = backup_file_path($filename);
if (!$path) {
    http_response_code(404);
    exit('ไม่พบไฟล์แบ็คอัพ');
}

log_activity((int) ($admin['id'] ?? 0), 'download_backup', 'backup', null, $filename);

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . (string) filesize($path));
header('Cache-Control: no-store');
readfile($path);
exit;
