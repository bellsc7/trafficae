<?php
// ตั้งค่าการรักษาความปลอดภัย Session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// เริ่ม Session
session_start();

// ตั้งค่าผู้ใช้งาน (สามารถเพิ่มผู้ใช้ได้ที่นี่)
$users = [
    'admin' => [
        'password' => password_hash('AEO@2024!', PASSWORD_DEFAULT),
        'name' => 'Administrator',
        'role' => 'admin'
    ],
    'traffic' => [
        'password' => password_hash('Traffic123!', PASSWORD_DEFAULT), 
        'name' => 'Traffic Manager',
        'role' => 'user'
    ],
    'aeo' => [
        'password' => password_hash('AEO@Traffic2024', PASSWORD_DEFAULT),
        'name' => 'AEO Operator',
        'role' => 'user'
    ]
];

// ฟังก์ชันตรวจสอบการ Login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_name']);
}

// ฟังก์ชันตรวจสอบสิทธิ์ผู้ใช้
function authenticate($username, $password) {
    global $users;
    
    if (isset($users[$username])) {
        if (password_verify($password, $users[$username]['password'])) {
            return $users[$username];
        }
    }
    return false;
}

// ฟังก์ชัน Login ผู้ใช้
function loginUser($username, $userInfo) {
    $_SESSION['user_id'] = $username;
    $_SESSION['user_name'] = $userInfo['name'];
    $_SESSION['user_role'] = $userInfo['role'];
    $_SESSION['login_time'] = time();
    
    // สร้าง CSRF Token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ฟังก์ชัน Logout ผู้ใช้
function logoutUser() {
    session_unset();
    session_destroy();
}

// ฟังก์ชันตรวจสอบ CSRF Token
function validateCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ฟังก์ชันสร้าง CSRF Token สำหรับ Form
function getCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// ตั้งค่า Timezone
date_default_timezone_set('Asia/Bangkok');

// ฟังก์ชันบันทึก Log การเข้าสู่ระบบ
function logActivity($action, $details = '') {
    $logFile = 'logs/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $user = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $logEntry = "[$timestamp] [$ip] [$user] $action";
    if ($details) {
        $logEntry .= " - $details";
    }
    $logEntry .= PHP_EOL;
    
    // สร้างโฟลเดอร์ logs หากไม่มี
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>