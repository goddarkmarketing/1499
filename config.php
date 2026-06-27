<?php
/**
 * BOYINSURE — การตั้งค่าระบบ
 * Local (XAMPP): ใช้ค่าด้านล่างได้เลย
 * Hosting: คัดลอก config.example.php เป็น config.local.php แล้วใส่ค่า DB จาก cPanel
 */
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'boyinsure',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => 'BOYINSURE Admin',
        'url' => '/1499',
        'timezone' => 'Asia/Bangkok',
    ],
    'session' => [
        'admin_key' => 'boyinsure_admin',
        'member_key' => 'boyinsure_member',
    ],
];
