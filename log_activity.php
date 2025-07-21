<?php
require_once 'auth_check.php';

header('Content-Type: application/json');

// รับข้อมูล JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$action = $input['action'];
$details = $input['details'] ?? '';

// บันทึก Activity Log
logActivity($action, $details);

echo json_encode(['success' => true]);
?>