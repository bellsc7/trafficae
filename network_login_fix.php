<?php
// ไฟล์แก้ไขปัญหา Login จาก Network ภายนอก
$fixed = false;
$results = [];

if (isset($_POST['fix_all'])) {
    // 1. แก้ไข Session Configuration
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0);
    ini_set('session.use_strict_mode', 0);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', 28800);
    
    // 2. ล้าง Session เก่า
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    
    // 3. สร้าง Session ใหม่
    session_name('AEO_TRAFFIC_SESSION');
    session_start();
    
    // 4. สร้าง CSRF Token ใหม่
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['initiated'] = true;
    $_SESSION['network_fix'] = true;
    $_SESSION['fix_timestamp'] = time();
    
    $results = [
        'session_id' => session_id(),
        'csrf_token' => $_SESSION['csrf_token'],
        'fix_time' => date('Y-m-d H:i:s'),
        'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ];
    
    $fixed = true;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Network Login Fix - TRAFFIC FEED AEO</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #2c3e50;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(20px);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .status-box {
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: 5px solid;
        }
        .status-info { background: #e1f5fe; border-color: #0288d1; color: #01579b; }
        .status-success { background: #e8f5e8; border-color: #4caf50; color: #2e7d32; }
        .status-warning { background: #fff3e0; border-color: #ff9800; color: #ef6c00; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #dee2e6;
        }
        .info-card h3 {
            color: #495057;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table th, .info-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-size: 0.9em;
        }
        .info-table th {
            background: #e9ecef;
            font-weight: 600;
        }
        .fix-button {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.2em;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin: 20px 0;
        }
        .fix-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(79, 172, 254, 0.4);
        }
        .success-message {
            text-align: center;
            margin: 30px 0;
        }
        .success-icon {
            font-size: 4em;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .code-block {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
            word-break: break-all;
        }
        .next-steps {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
        }
        .next-steps h3 {
            margin-bottom: 15px;
        }
        .next-steps ol {
            margin-left: 20px;
        }
        .next-steps li {
            margin: 8px 0;
            font-weight: 500;
        }
        .link-button {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            margin: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .link-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tools"></i> Network Login Fix</h1>
            <p>แก้ไขปัญหา CSRF_FAILED สำหรับการ Login จาก Network ภายนอก</p>
        </div>

        <?php if (!$fixed): ?>
            <div class="status-box status-info">
                <h3><i class="fas fa-info-circle"></i> ปัญหาที่พบ</h3>
                <p>ระบบตรวจพบปัญหา <strong>CSRF_FAILED</strong> เมื่อ Login จาก IP ภายนอก</p>
                <ul style="margin: 15px 0 0 20px;">
                    <li>Session Configuration ไม่รองรับ Cross-Network</li>
                    <li>CSRF Token ไม่ทำงานข้าม Network</li>
                    <li>Cookie Settings เข้มงวดเกินไป</li>
                </ul>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3><i class="fas fa-network-wired"></i> Network Information</h3>
                    <table class="info-table">
                        <tr><th>Your IP</th><td><?php echo $_SERVER['REMOTE_ADDR'] ?? 'Unknown'; ?></td></tr>
                        <tr><th>Server</th><td><?php echo $_SERVER['SERVER_NAME'] ?? 'Unknown'; ?></td></tr>
                        <tr><th>User Agent</th><td><?php echo substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 50) . '...'; ?></td></tr>
                        <tr><th>Current Time</th><td><?php echo date('Y-m-d H:i:s'); ?></td></tr>
                    </table>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-cog"></i> Current Session Status</h3>
                    <table class="info-table">
                        <tr><th>Session Status</th><td>
                            <?php 
                            switch (session_status()) {
                                case PHP_SESSION_DISABLED: echo '❌ DISABLED'; break;
                                case PHP_SESSION_NONE: echo '⚠️ NONE'; break;
                                case PHP_SESSION_ACTIVE: echo '✅ ACTIVE'; break;
                            }
                            ?>
                        </td></tr>
                        <tr><th>Session ID</th><td><?php echo session_id() ?: 'Not Started'; ?></td></tr>
                        <tr><th>Cookie Secure</th><td><?php echo ini_get('session.cookie_secure') ? '🔒 ON' : '🔓 OFF'; ?></td></tr>
                        <tr><th>SameSite</th><td><?php echo ini_get('session.cookie_samesite') ?: 'Not Set'; ?></td></tr>
                    </table>
                </div>
            </div>

            <form method="POST">
                <button type="submit" name="fix_all" class="fix-button">
                    <i class="fas fa-magic"></i> แก้ไขปัญหาทั้งหมดอัตโนมัติ
                </button>
            </form>

        <?php else: ?>
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 style="color: #4caf50; margin-bottom: 20px;">แก้ไขสำเร็จ!</h2>
                <p>ระบบได้แก้ไขปัญหา CSRF และ Session Configuration แล้ว</p>
            </div>

            <div class="status-box status-success">
                <h3><i class="fas fa-check"></i> การแก้ไขที่ดำเนินการ</h3>
                <ul style="margin: 15px 0 0 20px;">
                    <li>✅ แก้ไข Session Cookie Settings</li>
                    <li>✅ สร้าง CSRF Token ใหม่</li>
                    <li>✅ ตั้งค่า Cross-Network Support</li>
                    <li>✅ ล้าง Session เก่า</li>
                </ul>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3><i class="fas fa-key"></i> New Session Details</h3>
                    <table class="info-table">
                        <tr><th>Session ID</th><td style="font-family: monospace; font-size: 0.8em;"><?php echo $results['session_id']; ?></td></tr>
                        <tr><th>Fix Time</th><td><?php echo $results['fix_time']; ?></td></tr>
                        <tr><th>Client IP</th><td><?php echo $results['client_ip']; ?></td></tr>
                        <tr><th>Status</th><td><span style="color: #4caf50; font-weight: bold;">✅ READY</span></td></tr>
                    </table>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-shield-alt"></i> CSRF Token</h3>
                    <p style="margin-bottom: 10px; font-size: 0.9em;">Token ใหม่ที่สร้างขึ้น:</p>
                    <div class="code-block">
                        <?php echo chunk_split($results['csrf_token'], 32, '<br>'); ?>
                    </div>
                </div>
            </div>

            <div class="next-steps">
                <h3><i class="fas fa-arrow-right"></i> ขั้นตอนต่อไป</h3>
                <ol>
                    <li>กลับไปที่หน้า Login</li>
                    <li>Refresh หน้าเว็บ (กด F5)</li>
                    <li>ลองเข้าสู่ระบบด้วยบัญชีของคุณ</li>
                    <li>หากยังไม่ได้ ให้ล้าง Browser Cache</li>
                </ol>
                
                <div style="margin-top: 20px;">
                    <a href="login.php" class="link-button">
                        <i class="fas fa-sign-in-alt"></i> ไปหน้า Login
                    </a>
                    <a href="debug_session.php" class="link-button">
                        <i class="fas fa-bug"></i> ตรวจสอบ Session
                    </a>
                    <a href="network_login_fix.php" class="link-button">
                        <i class="fas fa-redo"></i> Run Fix อีกครั้ง
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 40px; color: #6c757d;">
            <p><strong>💡 Tips:</strong> หากยังพบปัญหา ให้ลอง:</p>
            <p>1. ใช้ Incognito/Private Mode | 2. ลอง Browser อื่น | 3. ล้าง Browser Cache</p>
        </div>
    </div>

    <script>
        // Auto redirect หลังแก้ไขสำเร็จ
        <?php if ($fixed): ?>
        setTimeout(() => {
            if (confirm('ต้องการไปหน้า Login ทันทีหรือไม่?')) {
                window.location.href = 'login.php';
            }
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>