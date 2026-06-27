<?php
/**
 * ตัวอย่างการตั้งค่า — คัดลอกเป็น config.local.php แล้วใส่ค่าจากโฮสติ้ง
 * (config.local.php ไม่ถูก commit ขึ้น git)
 */
return [
    'db' => [
        'host' => 'localhost',          // หรือ mysql.yourhost.com
        'port' => '3306',
        'name' => 'your_db_name',       // ชื่อ DB ที่สร้างใน cPanel
        'user' => 'your_db_user',
        'pass' => 'your_db_password',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'url' => '/',                   // path ของเว็บ เช่น / หรือ /1499
        'timezone' => 'Asia/Bangkok',
    ],
    // 'session' => [
    //     'save_path' => '/path/writable/sessions', // ถ้า session มีปัญหาบนโฮสต์
    // ],
];
