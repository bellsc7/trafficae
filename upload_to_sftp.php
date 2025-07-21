<?php
// ตรวจสอบสิทธิ์การเข้าใช้งาน
require_once 'auth_check.php';

// ตั้งค่าให้ PHP แสดง Error ทั้งหมดเพื่อช่วยในการแก้ไขปัญหา
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า header เพื่อให้ตอบกลับเป็น JSON
header('Content-Type: application/json');

// --- กรุณากรอกข้อมูล SFTP ของคุณที่นี่ ---
$sftp_server = "58.97.35.237";
$sftp_port   = 22;
$sftp_user   = "AEOftp";
$sftp_pass   = "AEO@2014";
$sftp_path   = "/Data feed/Traffic feed/"; // path ที่ต้องการวางไฟล์บน SFTP

// ฟังก์ชันสำหรับส่ง error กลับไปในรูปแบบ JSON
function send_error($message) {
    logActivity('SFTP_ERROR', $message);
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

// ตรวจสอบว่า cURL มี SFTP support หรือไม่
if (!function_exists('curl_init')) {
    send_error('cURL ไม่ได้ถูกติดตั้งบนเซิร์ฟเวอร์นี้');
}

$curl_version = curl_version();
if (!in_array('sftp', $curl_version['protocols'])) {
    send_error('cURL ไม่รองรับ SFTP Protocol บนเซิร์ฟเวอร์นี้');
}

// ตรวจสอบว่าได้รับข้อมูลจาก POST หรือไม่
if (!isset($_POST['filename']) || !isset($_POST['content'])) {
    send_error('ไม่ได้รับข้อมูลไฟล์ที่ถูกต้อง');
}

$filename = $_POST['filename'];
$content  = $_POST['content'];

// ตรวจสอบความปลอดภัยของชื่อไฟล์
if (!preg_match('/^AEOthailand_TRAFFIC_\d{8}\.txt$/', $filename)) {
    send_error('ชื่อไฟล์ไม่ถูกต้องตามรูปแบบที่กำหนด (ต้องเป็น AEOthailand_TRAFFIC_YYYYMMDD.txt)');
}

// ตรวจสอบขนาดไฟล์ (จำกัดที่ 10MB)
if (strlen($content) > 10 * 1024 * 1024) {
    send_error('ขนาดไฟล์เกินขีดจำกัด (10MB)');
}

// ตรวจสอบว่าข้อมูลไม่ว่างเปล่า
if (empty(trim($content))) {
    send_error('ไฟล์ว่างเปล่า ไม่สามารถอัปโหลดได้');
}

// บันทึก Log ก่อนเริ่มอัปโหลด
logActivity('SFTP_UPLOAD_START', "File: $filename, Size: " . strlen($content) . " bytes");

// เริ่มกระบวนการอัปโหลดด้วย cURL
try {
    // สร้างไฟล์ชั่วคราวสำหรับเก็บข้อมูล
    $temp_file = tempnam(sys_get_temp_dir(), 'sftp_upload_');
    if ($temp_file === false) {
        throw new Exception('ไม่สามารถสร้างไฟล์ชั่วคราวได้');
    }
    
    // เขียนข้อมูลลงไฟล์ชั่วคราว
    if (file_put_contents($temp_file, $content) === false) {
        throw new Exception('ไม่สามารถเขียนข้อมูลลงไฟล์ชั่วคราวได้');
    }

    // เปิดไฟล์สำหรับอ่าน
    $file_handle = fopen($temp_file, 'r');
    if ($file_handle === false) {
        throw new Exception('ไม่สามารถเปิดไฟล์ชั่วคราวสำหรับอ่านได้');
    }

    // สร้าง URL สำหรับ SFTP
    $sftp_url = "sftp://{$sftp_server}:{$sftp_port}{$sftp_path}{$filename}";

    // ตั้งค่า cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $sftp_url,
        CURLOPT_USERPWD => "{$sftp_user}:{$sftp_pass}",
        CURLOPT_UPLOAD => true,
        CURLOPT_INFILE => $file_handle,
        CURLOPT_INFILESIZE => filesize($temp_file),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60, // เพิ่ม timeout เป็น 60 วินาที
        CURLOPT_CONNECTTIMEOUT => 30, // เพิ่ม connection timeout
        CURLOPT_SSH_HOST_PUBLIC_KEY_MD5 => null, // ข้ามการตรวจสอบ host key
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_VERBOSE => false, // ปิด verbose เพื่อความปลอดภัย
    ]);

    // ทำการอัปโหลด
    $result = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $curl_error = curl_error($curl);
    $curl_info = curl_getinfo($curl);
    
    // ปิด cURL และไฟล์
    curl_close($curl);
    fclose($file_handle);
    
    // ลบไฟล์ชั่วคราว
    unlink($temp_file);

    // ตรวจสอบผลลัพธ์
    if ($result === false || !empty($curl_error)) {
        throw new Exception("cURL Error: " . $curl_error);
    }

    // ตรวจสอบ Response Code (SFTP ไม่ใช้ HTTP codes แต่เช็คเพื่อความปลอดภัย)
    if ($http_code !== 0 && $http_code >= 400) {
        throw new Exception("SFTP Error: HTTP Code $http_code");
    }

    // บันทึก Log สำเร็จ
    logActivity('SFTP_UPLOAD_SUCCESS', "File: $filename uploaded successfully");

    // ถ้าทุกอย่างสำเร็จ
    echo json_encode([
        'success' => true, 
        'message' => 'File uploaded successfully via SFTP using cURL.',
        'filename' => $filename,
        'size' => strlen($content),
        'upload_time' => date('Y-m-d H:i:s')
    ]);

} catch (Throwable $e) {
    // ทำความสะอาดไฟล์ชั่วคราวในกรณีที่เกิด error
    if (isset($temp_file) && file_exists($temp_file)) {
        unlink($temp_file);
    }
    if (isset($file_handle) && is_resource($file_handle)) {
        fclose($file_handle);
    }
    
    // บันทึก Log ล้มเหลว
    logActivity('SFTP_UPLOAD_FAILED', "File: $filename, Error: " . $e->getMessage());
    
    send_error($e->getMessage());
}

?>