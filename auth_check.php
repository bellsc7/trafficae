<?php
// รวมไฟล์ config
require_once 'config.php';

// ตรวจสอบว่าผู้ใช้ได้ Login แล้วหรือไม่
if (!isLoggedIn()) {
    // ถ้ายังไม่ได้ Login ให้ redirect ไปหน้า Login
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// ตรวจสอบ Session Timeout (ตัวเลือก: 8 ชั่วโมง)
$session_timeout = 8 * 60 * 60; // 8 hours
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $session_timeout) {
    logActivity('SESSION_TIMEOUT', 'Session expired');
    logoutUser();
    header('Location: login.php?message=session_expired');
    exit();
}

// ต่ออายุ Session
$_SESSION['last_activity'] = time();

// ตัวแปรสำหรับใช้ในหน้าอื่นๆ
$current_user = [
    'id' => $_SESSION['user_id'],
    'name' => $_SESSION['user_name'],
    'role' => $_SESSION['user_role'],
    'login_time' => $_SESSION['login_time']
];
?>