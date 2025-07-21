<?php
require_once 'config.php';

// บันทึก Log การออกจากระบบ
if (isLoggedIn()) {
    logActivity('LOGOUT', 'User logged out');
}

// ทำการ Logout
logoutUser();

// Redirect ไปหน้า Login พร้อมข้อความ
header('Location: login.php?message=logout_success');
exit();
?>