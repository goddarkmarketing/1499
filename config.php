<?php
/**
 * BOYINSURE — การตั้งค่าระบบ
 * ปรับค่า DB ให้ตรงกับ XAMPP ของคุณ
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
