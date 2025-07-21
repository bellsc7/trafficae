<?php
// ตั้งค่าให้ PHP แสดง Error ทั้งหมดบนหน้าจอ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>SFTP Upload Methods Test</h1>";
echo "<hr>";

// --- ทดสอบ cURL ---
echo "<h2>1. Testing cURL Support</h2>";

if (!function_exists('curl_init')) {
    echo "<p style='color: red; font-weight: bold;'>❌ cURL ไม่ได้ถูกติดตั้งบนเซิร์ฟเวอร์นี้</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ cURL ถูกติดตั้งแล้ว</p>";
    
    $curl_version = curl_version();
    echo "<p>cURL Version: " . $curl_version['version'] . "</p>";
    echo "<p>Supported Protocols: " . implode(', ', $curl_version['protocols']) . "</p>";
    
    if (in_array('sftp', $curl_version['protocols'])) {
        echo "<p style='color: green; font-weight: bold;'>✅ cURL รองรับ SFTP Protocol</p>";
        echo "<p style='background: #e8f5e8; padding: 10px; border-radius: 5px;'><strong>แนะนำ:</strong> ใช้ไฟล์ upload_to_sftp.php (วิธี cURL)</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ cURL ไม่รองรับ SFTP Protocol</p>";
    }
}

echo "<hr>";

// --- ทดสอบ SSH2 Extension ---
echo "<h2>2. Testing SSH2 Extension</h2>";

if (!extension_loaded('ssh2')) {
    echo "<p style='color: red; font-weight: bold;'>❌ SSH2 extension ไม่ได้ถูกติดตั้งบนเซิร์ฟเวอร์นี้</p>";
    echo "<p style='color: orange;'>หากต้องการใช้วิธีนี้ ให้ติดตั้ง php-ssh2 extension</p>";
    echo "<p>Ubuntu/Debian: <code>sudo apt-get install php-ssh2</code></p>";
    echo "<p>CentOS/RHEL: <code>sudo yum install php-ssh2</code></p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ SSH2 extension ถูกติดตั้งแล้ว</p>";
    
    // ตรวจสอบฟังก์ชันที่จำเป็น
    $required_functions = ['ssh2_connect', 'ssh2_auth_password', 'ssh2_sftp'];
    $missing_functions = [];
    
    foreach ($required_functions as $func) {
        if (!function_exists($func)) {
            $missing_functions[] = $func;
        }
    }
    
    if (empty($missing_functions)) {
        echo "<p style='color: green; font-weight: bold;'>✅ ฟังก์ชัน SSH2 ที่จำเป็นพร้อมใช้งาน</p>";
        echo "<p style='background: #e8f5e8; padding: 10px; border-radius: 5px;'><strong>พร้อมใช้งาน:</strong> ใช้ไฟล์ upload_to_sftp_ssh2.php (วิธี SSH2)</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ ฟังก์ชันที่ขาดหาย: " . implode(', ', $missing_functions) . "</p>";
    }
}

echo "<hr>";

// --- ทดสอบ FTP Functions (สำหรับ FTP ธรรมดา) ---
echo "<h2>3. Testing FTP Functions (สำหรับ FTP ธรรมดา)</h2>";

if (!function_exists('ftp_connect')) {
    echo "<p style='color: red; font-weight: bold;'>❌ FTP functions ไม่พร้อมใช้งาน</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ FTP functions พร้อมใช้งาน</p>";
    echo "<p style='color: orange;'>⚠️ หมายเหตุ: วิธีนี้ใช้ได้เฉพาะ FTP ธรรมดา ไม่ใช่ SFTP (ความปลอดภัยต่ำกว่า)</p>";
}

echo "<hr>";

// --- สรุปและคำแนะนำ ---
echo "<h2>4. สรุปและคำแนะนำ</h2>";

echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff;'>";
echo "<h3>ลำดับความแนะนำ:</h3>";
echo "<ol>";
echo "<li><strong>cURL with SFTP</strong> - วิธีที่แนะนำมากที่สุด เพราะมีมากับ PHP ส่วนใหญ่</li>";
echo "<li><strong>SSH2 Extension</strong> - ทางเลือกที่ดี แต่ต้องติดตั้ง extension เพิ่ม</li>";
echo "<li><strong>phpseclib</strong> - ทางเลือกสำรอง ใช้ได้ดี แต่ต้องดาวน์โหลด library</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin-top: 15px;'>";
echo "<h3>วิธีการใช้งาน:</h3>";
echo "<p>1. เลือกไฟล์ที่เหมาะสมจากผลการทดสอบข้างต้น</p>";
echo "<p>2. แทนที่ไฟล์ <code>upload_to_ftp.php</code> เดิมด้วยไฟล์ใหม่</p>";
echo "<p>3. แก้ไขชื่อไฟล์ใน <code>index.php</code> ให้ตรงกับไฟล์ที่เลือก</p>";
echo "<p>4. กรอกข้อมูล SFTP Server ให้ถูกต้อง</p>";
echo "</div>";

?>

<style>
    body { font-family: 'Sarabun', sans-serif; margin: 20px; line-height: 1.6; }
    code { background: #f1f1f1; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    h1, h2, h3 { color: #333; }
    hr { margin: 30px 0; }
</style>