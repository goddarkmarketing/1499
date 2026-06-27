<?php

declare(strict_types=1);

/** @return string Absolute project root (httpdocs) */
function backup_project_root(): string {
    return dirname(__DIR__, 2);
}

/** @return string Absolute backups storage directory */
function backup_storage_dir(): string {
    $dir = backup_project_root() . '/storage/backups';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $htaccess = $dir . '/.htaccess';
    if (!is_file($htaccess)) {
        file_put_contents($htaccess, "Require all denied\nDeny from all\n");
    }
    $index = $dir . '/index.html';
    if (!is_file($index)) {
        file_put_contents($index, '');
    }
    return $dir;
}

function backup_safe_filename(string $name): bool {
    return (bool) preg_match('/^boyinsure-backup-\d{8}-\d{6}\.zip$/', $name);
}

/** @return list<string> */
function backup_list_files(): array {
    $dir = backup_storage_dir();
    $files = glob($dir . '/boyinsure-backup-*.zip') ?: [];
    rsort($files);
    return array_map('basename', $files);
}

function backup_file_path(string $filename): ?string {
    if (!backup_safe_filename($filename)) {
        return null;
    }
    $path = backup_storage_dir() . '/' . $filename;
    return is_file($path) ? $path : null;
}

function backup_sql_value(PDO $pdo, mixed $value): string {
    if ($value === null) {
        return 'NULL';
    }
    return $pdo->quote((string) $value);
}

function backup_export_database(PDO $pdo): string {
    $dbName = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
    $lines = [
        '-- BOYINSURE full database backup',
        '-- Generated: ' . date('c'),
        '-- Database: ' . $dbName,
        '',
        'SET NAMES utf8mb4;',
        'SET FOREIGN_KEY_CHECKS = 0;',
        '',
    ];

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $table = (string) $table;
        $createRow = $pdo->query('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`')->fetch(PDO::FETCH_NUM);
        if (!$createRow) {
            continue;
        }
        $lines[] = 'DROP TABLE IF EXISTS `' . $table . '`;';
        $lines[] = $createRow[1] . ';';
        $lines[] = '';

        $stmt = $pdo->query('SELECT * FROM `' . str_replace('`', '``', $table) . '`');
        $columns = [];
        $first = true;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($first) {
                $columns = array_keys($row);
                $first = false;
            }
            $values = array_map(static fn($v) => backup_sql_value($pdo, $v), array_values($row));
            $colList = '`' . implode('`, `', $columns) . '`';
            $lines[] = 'INSERT INTO `' . $table . '` (' . $colList . ') VALUES (' . implode(', ', $values) . ');';
        }
        $lines[] = '';
    }

    $lines[] = 'SET FOREIGN_KEY_CHECKS = 1;';
    $lines[] = '';

    return implode("\n", $lines);
}

/** @return array<string, int> */
function backup_table_counts(PDO $pdo): array {
    $counts = [];
    foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
        $table = (string) $table;
        $counts[$table] = (int) $pdo->query('SELECT COUNT(*) FROM `' . str_replace('`', '``', $table) . '`')->fetchColumn();
    }
    return $counts;
}

/** @return list<string> */
function backup_file_roots(): array {
    return [
        'assets',
        'admin',
        'api',
        'includes',
        'database',
        'scripts',
    ];
}

/** @return list<string> */
function backup_root_files(): array {
    $root = backup_project_root();
    $names = [
        'index.html',
        'promotions.html',
        'profile.html',
        'about.html',
        'articles.html',
        'article.html',
        'contact.html',
        'insurance.html',
        'insurance-plan.html',
        'config.php',
        'config.example.php',
        'config.local.php',
        'migrate.php',
        'migrate-v2.php',
        'migrate-v3.php',
        'install.php',
        'check-host.php',
    ];
    $out = [];
    foreach ($names as $name) {
        if (is_file($root . '/' . $name)) {
            $out[] = $name;
        }
    }
    return $out;
}

function backup_should_skip_path(string $relativePath): bool {
    $normalized = str_replace('\\', '/', $relativePath);
    $skip = [
        'storage/backups/',
        '/.git/',
        '/node_modules/',
        '/_site/',
        '/.idea/',
        '/.vscode/',
    ];
    foreach ($skip as $part) {
        if (strpos($normalized, $part) !== false) {
            return true;
        }
    }
    if (preg_match('/\.(log|tmp)$/i', $normalized)) {
        return true;
    }
    return false;
}

function backup_add_path_to_zip(ZipArchive $zip, string $sourcePath, string $zipPath): void {
    if (!is_file($sourcePath) || backup_should_skip_path($zipPath)) {
        return;
    }
    $zip->addFile($sourcePath, $zipPath);
}

function backup_add_directory_to_zip(ZipArchive $zip, string $sourceDir, string $zipPrefix): int {
    if (!is_dir($sourceDir)) {
        return 0;
    }
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $sourceDir = rtrim(str_replace('\\', '/', $sourceDir), '/');
    foreach ($iterator as $item) {
        /** @var SplFileInfo $item */
        $full = str_replace('\\', '/', $item->getPathname());
        $relative = ltrim(substr($full, strlen($sourceDir)), '/');
        $zipPath = rtrim($zipPrefix, '/') . '/' . $relative;
        if (backup_should_skip_path($zipPath)) {
            continue;
        }
        if ($item->isDir()) {
            $zip->addEmptyDir($zipPath);
            continue;
        }
        $zip->addFile($full, $zipPath);
        $count++;
    }
    return $count;
}

/**
 * @return array{filename: string, size: int, tables: int, files: int}
 */
function backup_create_full(array $admin): array {
    if (!class_exists(ZipArchive::class)) {
        throw new RuntimeException('เซิร์ฟเวอร์ไม่รองรับ ZipArchive — ติดต่อโฮสต์เพื่อเปิด extension zip');
    }

    @set_time_limit(600);
    @ini_set('memory_limit', '512M');

    $root = backup_project_root();
    $storage = backup_storage_dir();
    $filename = 'boyinsure-backup-' . date('Ymd-His') . '.zip';
    $zipPath = $storage . '/' . $filename;

    $pdo = db();
    $sql = backup_export_database($pdo);
    $tableCounts = backup_table_counts($pdo);

    $manifest = [
        'app' => 'BOYINSURE',
        'type' => 'full',
        'created_at' => date('c'),
        'created_by' => $admin['name'] ?? $admin['email'] ?? 'admin',
        'php_version' => PHP_VERSION,
        'db_name' => app_config('db.name'),
        'table_counts' => $tableCounts,
        'includes' => [
            'database/full.sql',
            'files/assets',
            'files/admin',
            'files/api',
            'files/includes',
            'files/database',
            'files/scripts',
            'files/html',
            'files/config',
        ],
    ];

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('สร้างไฟล์ ZIP ไม่ได้');
    }

    $zip->addFromString('manifest.json', json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    $zip->addFromString('database/full.sql', $sql);

    $fileCount = 0;
    foreach (backup_file_roots() as $dir) {
        $fileCount += backup_add_directory_to_zip($zip, $root . '/' . $dir, 'files/' . $dir);
    }
    foreach (backup_root_files() as $name) {
        backup_add_path_to_zip($zip, $root . '/' . $name, 'files/' . $name);
        $fileCount++;
    }

    $zip->close();

    if (!is_file($zipPath)) {
        throw new RuntimeException('บันทึกไฟล์แบ็คอัพไม่สำเร็จ');
    }

    log_activity((int) ($admin['id'] ?? 0), 'create_backup', 'backup', null, $filename);

    return [
        'filename' => $filename,
        'size' => (int) filesize($zipPath),
        'tables' => count($tableCounts),
        'files' => $fileCount,
    ];
}

function backup_delete_file(string $filename, array $admin): bool {
    $path = backup_file_path($filename);
    if (!$path) {
        return false;
    }
    $ok = unlink($path);
    if ($ok) {
        log_activity((int) ($admin['id'] ?? 0), 'delete_backup', 'backup', null, $filename);
    }
    return $ok;
}

function backup_format_bytes(int $bytes): string {
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1048576) {
        return round($bytes / 1024, 1) . ' KB';
    }
    if ($bytes < 1073741824) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    return round($bytes / 1073741824, 2) . ' GB';
}
