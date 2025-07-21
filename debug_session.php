<?php
// ไฟล์ debug สำหรับตรวจสอบปัญหา Session และ CSRF
require_once 'config.php';

// สำหรับการพัฒนาเท่านั้น - ลบออกในการใช้งานจริง
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Session Information</title>
    <style>
        body { font-family: 'Courier New', monospace; padding: 20px; background: #f5f5f5; }
        .debug-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .debug-title { background: #2c3e50; color: white; padding: 10px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        .status-ok { color: #27ae60; font-weight: bold; }
        .status-error { color: #e74c3c; font-weight: bold; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table th, .info-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .info-table th { background: #f8f9fa; }
        .clear-session { background: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .clear-session:hover { background: #c0392b; }
    </style>
</head>
<body>
    <h1>🔍 Debug Session Information</h1>
    <p><strong>เวลาปัจจุบัน:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    <p><strong>IP Address:</strong> <?php echo $_SERVER['REMOTE_ADDR'] ?? 'Unknown'; ?></p>
    <p><strong>User Agent:</strong> <?php echo $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'; ?></p>

    <!-- Session Information -->
    <div class="debug-box">
        <div class="debug-title">📋 Session Information</div>
        <table class="info-table">
            <tr>
                <th>Session ID</th>
                <td><?php echo session_id(); ?></td>
            </tr>
            <tr>
                <th>Session Status</th>
                <td>
                    <?php 
                    $session_status = session_status();
                    switch ($session_status) {
                        case PHP_SESSION_DISABLED:
                            echo '<span class="status-error">DISABLED</span>';
                            break;
                        case PHP_SESSION_NONE:
                            echo '<span class="status-error">NONE</span>';
                            break;
                        case PHP_SESSION_ACTIVE:
                            echo '<span class="status-ok">ACTIVE</span>';
                            break;
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Session Name</th>
                <td><?php echo session_name(); ?></td>
            </tr>
            <tr>
                <th>Login Status</th>
                <td>
                    <?php echo isLoggedIn() ? '<span class="status-ok">LOGGED IN</span>' : '<span class="status-error">NOT LOGGED IN</span>'; ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- CSRF Information -->
    <div class="debug-box">
        <div class="debug-title">🔐 CSRF Token Information</div>
        <table class="info-table">
            <tr>
                <th>Current CSRF Token</th>
                <td style="word-break: break-all; font-family: monospace;">
                    <?php echo $_SESSION['csrf_token'] ?? '<span class="status-error">NOT SET</span>'; ?>
                </td>
            </tr>
            <tr>
                <th>Token Length</th>
                <td>
                    <?php 
                    if (isset($_SESSION['csrf_token'])) {
                        echo strlen($_SESSION['csrf_token']) . ' characters';
                    } else {
                        echo '<span class="status-error">N/A</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>New Token Generated</th>
                <td style="word-break: break-all; font-family: monospace;">
                    <?php echo getCSRFToken(); ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- Session Data -->
    <div class="debug-box">
        <div class="debug-title">💾 Session Data</div>
        <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
<?php 
if (empty($_SESSION)) {
    echo "Session is empty";
} else {
    print_r($_SESSION);
}
?>
        </pre>
    </div>

    <!-- PHP Configuration -->
    <div class="debug-box">
        <div class="debug-title">⚙️ PHP Session Configuration</div>
        <table class="info-table">
            <tr>
                <th>session.cookie_httponly</th>
                <td><?php echo ini_get('session.cookie_httponly') ? 'ON' : 'OFF'; ?></td>
            </tr>
            <tr>
                <th>session.cookie_secure</th>
                <td><?php echo ini_get('session.cookie_secure') ? 'ON' : 'OFF'; ?></td>
            </tr>
            <tr>
                <th>session.use_strict_mode</th>
                <td><?php echo ini_get('session.use_strict_mode') ? 'ON' : 'OFF'; ?></td>
            </tr>
            <tr>
                <th>session.cookie_samesite</th>
                <td><?php echo ini_get('session.cookie_samesite') ?: 'Not Set'; ?></td>
            </tr>
            <tr>
                <th>session.gc_maxlifetime</th>
                <td><?php echo ini_get('session.gc_maxlifetime'); ?> seconds (<?php echo round(ini_get('session.gc_maxlifetime')/3600, 2); ?> hours)</td>
            </tr>
        </table>
    </div>

    <!-- Cookie Information -->
    <div class="debug-box">
        <div class="debug-title">🍪 Cookie Information</div>
        <table class="info-table">
            <?php if (empty($_COOKIE)): ?>
                <tr><td colspan="2">No cookies found</td></tr>
            <?php else: ?>
                <?php foreach ($_COOKIE as $name => $value): ?>
                    <tr>
                        <th><?php echo htmlspecialchars($name); ?></th>
                        <td style="word-break: break-all; font-family: monospace;">
                            <?php echo htmlspecialchars(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : ''); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

    <!-- Test CSRF Form -->
    <div class="debug-box">
        <div class="debug-title">🧪 Test CSRF Validation</div>
        <form method="POST" style="margin: 20px 0;">
            <input type="hidden" name="test_csrf" value="1">
            <input type="hidden" name="csrf_token" value="<?php echo getCSRFToken(); ?>">
            <button type="submit" style="background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                Test CSRF Token
            </button>
        </form>
        
        <?php
        if (isset($_POST['test_csrf'])) {
            $test_token = $_POST['csrf_token'] ?? '';
            echo "<p><strong>Test Result:</strong> ";
            if (validateCSRF($test_token)) {
                echo '<span class="status-ok">✅ CSRF Token Valid</span>';
            } else {
                echo '<span class="status-error">❌ CSRF Token Invalid</span>';
            }
            echo "</p>";
            echo "<p><strong>Posted Token:</strong> <code>" . htmlspecialchars($test_token) . "</code></p>";
            echo "<p><strong>Session Token:</strong> <code>" . htmlspecialchars($_SESSION['csrf_token'] ?? 'NOT_SET') . "</code></p>";
        }
        ?>
    </div>

    <!-- Clear Session -->
    <div class="debug-box">
        <div class="debug-title">🗑️ Clear Session</div>
        <form method="POST" style="margin: 20px 0;">
            <input type="hidden" name="clear_session" value="1">
            <button type="submit" class="clear-session" onclick="return confirm('Are you sure you want to clear the session?')">
                Clear Session Data
            </button>
        </form>
        
        <?php
        if (isset($_POST['clear_session'])) {
            session_unset();
            session_destroy();
            echo '<p class="status-ok">✅ Session cleared successfully! <a href="debug_session.php">Refresh page</a></p>';
        }
        ?>
    </div>

    <hr style="margin: 40px 0;">
    <p><strong>🔗 Quick Links:</strong></p>
    <p>
        <a href="login.php">← Back to Login</a> | 
        <a href="index.php">Go to Main Page</a> | 
        <a href="debug_session.php">🔄 Refresh Debug</a>
    </p>

    <script>
        // Auto refresh every 30 seconds
        setTimeout(() => {
            if (confirm('Auto refresh debug page?')) {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>