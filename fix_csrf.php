<?php
// ไฟล์แก้ไขปัญหา CSRF และ Session อัตโนมัติ
header('Content-Type: application/json');

// เริ่ม session ใหม่ด้วยการตั้งค่าที่ปลอดภัย
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // HTTP ธรรมดา
ini_set('session.use_strict_mode', 0); // ไม่เข้มงวดเกินไป
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 28800);

// ล้าง session เก่า
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// สร้าง session ใหม่
session_name('AEO_TRAFFIC_SESSION');
session_start();

// สร้าง CSRF token ใหม่
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$_SESSION['initiated'] = true;
$_SESSION['fix_time'] = date('Y-m-d H:i:s');

// รายงานผลลัพธ์
$result = [
    'success' => true,
    'message' => 'CSRF และ Session ได้รับการแก้ไขแล้ว',
    'session_id' => session_id(),
    'csrf_token' => $_SESSION['csrf_token'],
    'fix_time' => $_SESSION['fix_time'],
    'instructions' => [
        '1. กลับไปที่หน้า Login',
        '2. Refresh หน้าเว็บ (F5)',
        '3. ลองเข้าสู่ระบบใหม่',
        '4. หากยังไม่ได้ ให้ล้าง Browser Cache'
    ]
];

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>