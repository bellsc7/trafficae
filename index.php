<?php
// ตรวจสอบสิทธิ์การเข้าใช้งาน
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรแกรมสร้างข้อมูลสำหรับสาขาจากไฟล์</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --edit-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --ftp-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            
            --primary-color: #667eea;
            --secondary-color: #2c3e50;
            --success-color: #00f2fe;
            --warning-color: #f5576c;
            --edit-color: #a8edea;
            --ftp-color: #764ba2;
            
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 20px 40px rgba(0,0,0,0.1);
            --card-shadow-hover: 0 30px 60px rgba(0,0,0,0.15);
            
            --white-color: #ffffff;
            --text-color: #2c3e50;
            --light-text: #718096;
            --border-color: #e2e8f0;
            --bg-light: #f7fafc;
        }

        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        html, body { 
            height: 100%; 
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .app-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-color);
            padding: 20px 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-title h1 {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            font-size: 2.2em;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(103, 126, 234, 0.1);
            padding: 12px 20px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        .user-details {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-color);
            font-size: 1em;
        }

        .user-role {
            font-size: 0.85em;
            color: var(--light-text);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logout-btn {
            background: var(--warning-gradient);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.4);
        }

        main {
            flex-grow: 1;
            padding: 40px 20px;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .info-panel {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 30px;
            padding: 25px;
            margin-bottom: 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            font-size: 1.1em;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-panel:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }

        .info-item { 
            display: flex; 
            align-items: center; 
            gap: 10px;
            font-weight: 500;
        }

        .info-item i {
            color: var(--primary-color);
            font-size: 1.2em;
        }

        #daySelector {
            padding: 10px 15px;
            font-size: 1em;
            border-radius: 12px;
            border: 2px solid var(--primary-color);
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-weight: 500;
        }

        #daySelector:focus {
            outline: none;
            border-color: var(--ftp-color);
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
        }
        
        .main-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 40px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--card-shadow-hover);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 25px;
            text-align: center;
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary-gradient);
            border-radius: 2px;
        }

        .card-header h2 { 
            margin: 0; 
            font-size: 1.5em; 
            color: var(--text-color);
            font-weight: 600;
        }

        .card-body {
            padding: 30px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .file-upload-wrapper {
            border: 3px dashed var(--primary-color);
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .file-upload-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(103, 126, 234, 0.1), transparent);
            transition: left 0.5s;
        }

        .file-upload-wrapper:hover::before {
            left: 100%;
        }

        .file-upload-wrapper:hover {
            background: linear-gradient(135deg, rgba(103, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            border-color: var(--ftp-color);
            transform: scale(1.02);
        }

        .file-upload-wrapper .icon {
            font-size: 4.5em;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .file-upload-wrapper:hover .icon {
            transform: scale(1.1) rotate(5deg);
        }

        .file-upload-wrapper p {
            margin-top: 15px;
            font-size: 1.3em;
            color: var(--text-color);
            font-weight: 500;
        }

        #fileUploader { 
            display: none; 
        }

        #fileStatus {
            margin-top: 25px;
            font-size: 1.1em;
            font-weight: 600;
            color: var(--text-color);
            padding: 12px 20px;
            border-radius: 12px;
            background: var(--bg-light);
            transition: all 0.3s ease;
        }

        #finalOutput {
            width: 100%;
            flex-grow: 1;
            min-height: 450px;
            padding: 20px;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            font-family: 'JetBrains Mono', 'Consolas', 'Menlo', monospace;
            font-size: 1.0em; 
            line-height: 1.7;
            background: linear-gradient(135deg, #fcfdff 0%, #f8fafc 100%);
            transition: all 0.3s ease;
            resize: vertical;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        #finalOutput:not([readonly]) {
            border-color: var(--edit-color);
            box-shadow: 0 0 20px rgba(168, 237, 234, 0.3), inset 0 2px 4px rgba(0,0,0,0.05);
            background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%);
        }
        
        #finalOutput.error-message {
            color: var(--warning-color);
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-family: 'Sarabun', sans-serif;
            font-size: 1.3em;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
            width: 100%;
        }

        .action-button {
            flex: 1 1 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 18px 25px;
            font-size: 1.1em;
            font-weight: 600;
            color: var(--white-color);
            border: none;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .action-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .action-button:hover::before {
            left: 100%;
        }

        .action-button:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .action-button:active {
            transform: translateY(-1px) scale(1.02);
        }

        .save-button {
            background: var(--success-gradient);
            box-shadow: 0 8px 25px rgba(0, 242, 254, 0.3);
        }

        .edit-button {
            background: var(--edit-gradient);
            color: var(--text-color);
            box-shadow: 0 8px 25px rgba(168, 237, 234, 0.3);
        }

        .lock-button {
            background: var(--warning-gradient);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.3);
        }

        .ftp-button {
            background: var(--ftp-gradient);
            box-shadow: 0 8px 25px rgba(118, 75, 162, 0.4);
        }

        .action-button i {
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }

        .action-button:hover i {
            transform: scale(1.2);
        }
        
        .app-footer {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            color: var(--light-text);
            text-align: center;
            padding: 25px;
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: 500;
        }

        /* Loading Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loading {
            animation: pulse 1.5s infinite;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .user-info {
                order: -1;
            }
            
            .main-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .info-panel {
                flex-direction: column;
                gap: 20px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .action-button {
                flex: 1 1 auto;
            }
            
            .header-title h1 {
                font-size: 1.8em;
            }
            
            .file-upload-wrapper {
                padding: 40px 20px;
            }
        }

        /* Custom scrollbar */
        #finalOutput::-webkit-scrollbar {
            width: 8px;
        }

        #finalOutput::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        #finalOutput::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        #finalOutput::-webkit-scrollbar-thumb:hover {
            background: var(--ftp-color);
        }

        /* Security Info */
        .security-info {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #10b981;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            color: #047857;
        }

        .security-info i {
            font-size: 1.2em;
            color: #10b981;
        }
    </style>
</head>
<body>
    <header class="app-header">
        <div class="header-content">
            <div class="header-title">
                <h1><i class="fas fa-database"></i> TRAFFIC FEED AEO</h1>
            </div>
            <div class="user-info">
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($current_user['name']); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($current_user['role']); ?></div>
                </div>
                <a href="logout.php" class="logout-btn" onclick="return confirm('คุณต้องการออกจากระบบหรือไม่?')">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </a>
            </div>
        </div>
    </header>

    <main>
        <div class="security-info">
            <i class="fas fa-shield-alt"></i>
            <span>ระบบมีความปลอดภัย - คุณได้เข้าสู่ระบบในนาม: <strong><?php echo htmlspecialchars($current_user['name']); ?></strong></span>
        </div>

        <div class="info-panel">
            <span class="info-item">
                <i class="fas fa-store"></i> 
                <strong>ข้อมูลคงที่:</strong> เลขคลังสาขา
            </span>
            <span class="info-item">
                <i class="far fa-calendar-alt"></i> 
                <strong>เดือนปัจจุบัน:</strong> 
                <span id="currentMonthInfo"></span>
            </span>
            <div class="info-item">
                <label for="daySelector">
                    <i class="far fa-calendar-check"></i> 
                    <strong>เลือกวันที่:</strong>
                </label>
                <select id="daySelector" onchange="processSelectedDay()"></select>
            </div>
        </div>

        <div class="main-grid">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-file-excel"></i> อัปโหลดไฟล์ข้อมูล</h2>
                </div>
                <div class="card-body">
                    <label for="fileUploader" class="file-upload-wrapper">
                        <div class="icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <p>คลิกเพื่อเลือกไฟล์ Excel (.xlsx, .xls)</p>
                    </label>
                    <input type="file" id="fileUploader" accept=".xlsx, .xls">
                    <p id="fileStatus">ยังไม่ได้เลือกไฟล์</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                     <h2><i class="fas fa-file-alt"></i> Data (ผลลัพธ์)</h2>
                </div>
                <div class="card-body">
                    <textarea id="finalOutput" placeholder="ผลลัพธ์จะแสดงที่นี่หลังจากอัปโหลดไฟล์และเลือกวันที่"></textarea>
                    
                    <div class="button-group">
                        <button id="editButton" class="action-button edit-button">
                            <i class="fas fa-pencil-alt"></i> แก้ไข
                        </button>
                        <button id="saveButton" class="action-button save-button">
                            <i class="fas fa-save"></i> Export
                        </button>
                        <button id="ftpButton" class="action-button ftp-button">
                            <i class="fas fa-upload"></i> Send to SFTP
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <textarea id="trafficInput" style="display: none;"></textarea>
        <textarea id="transInput" style="display: none;"></textarea>
    </main>

    <footer class="app-footer">
        <p>© <span id="current-year"></span> PACIFICA GROUP » TRAFFIC FEED AMERICAN EAGLE</p>
    </footer>

<script>
    // ข้อมูลสาขา
    const branchCodes = [
        '390082025-', '390122025-', '390152025-', '390112025-',
        '399992025-', '390102025-', '390142025-', '390092025-',
        '390062025-', '390052025-', '390032025-', '390042025-',
        '390072025-', '393022025-', '390132025-', '393002025-'
    ];

    // ตัวแปรสำหรับเก็บข้อมูล
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonthFormatted = String(now.getMonth() + 1).padStart(2, '0');
    const daySelector = document.getElementById('daySelector');
    const trafficInputElement = document.getElementById('trafficInput');
    const transInputElement = document.getElementById('transInput');
    const finalOutputElement = document.getElementById('finalOutput');
    const fileStatusElement = document.getElementById('fileStatus');
    const editButton = document.getElementById('editButton');
    const ftpButton = document.getElementById('ftpButton');

    let sheetData = [];

    // เตรียมข้อมูลเริ่มต้น
    document.getElementById('currentMonthInfo').textContent = currentMonthFormatted;
    document.getElementById('current-year').textContent = currentYear;

    // สร้างตัวเลือกวันที่
    for (let i = 1; i <= 31; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.textContent = i;
        daySelector.appendChild(option);
    }
    daySelector.value = now.getDate();

    // ฟังก์ชันแปลงคอลัมน์ Excel เป็น Index
    function excelColToIndex(colStr) {
        let index = 0;
        for (let i = 0; i < colStr.length; i++) {
            index = index * 26 + colStr.toUpperCase().charCodeAt(i) - 64;
        }
        return index - 1;
    }

    // ฟังก์ชันสลับโหมดแก้ไข
    function toggleEditMode() {
        const isReadOnly = finalOutputElement.readOnly;
        if (isReadOnly) {
            if (finalOutputElement.classList.contains('error-message')) {
                showNotification("ไม่มีข้อมูลที่ถูกต้องให้แก้ไข", "warning");
                return;
            }
            finalOutputElement.readOnly = false;
            finalOutputElement.focus();
            editButton.innerHTML = '<i class="fas fa-lock"></i> ล็อคการแก้ไข';
            editButton.classList.remove('edit-button');
            editButton.classList.add('lock-button');
        } else {
            finalOutputElement.readOnly = true;
            editButton.innerHTML = '<i class="fas fa-pencil-alt"></i> แก้ไข';
            editButton.classList.remove('lock-button');
            editButton.classList.add('edit-button');
        }
    }

    // ฟังก์ชันจัดการไฟล์ที่เลือก
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        // แสดงสถานะการโหลด
        fileStatusElement.textContent = `กำลังอ่านไฟล์: ${file.name}...`;
        fileStatusElement.style.color = 'var(--text-color)';
        fileStatusElement.classList.add('loading');
        
        finalOutputElement.value = '';
        finalOutputElement.classList.remove('error-message');

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                if (!workbook.SheetNames.includes('Calenda')) {
                    throw new Error("ไม่พบชีตชื่อ 'Calenda' ในไฟล์");
                }

                const worksheet = workbook.Sheets['Calenda'];
                sheetData = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: '' });

                if (!sheetData || sheetData.length === 0) {
                    throw new Error("ชีต 'Calenda' ว่างเปล่าหรือไม่มีข้อมูล");
                }
                
                fileStatusElement.classList.remove('loading');
                fileStatusElement.textContent = `ไฟล์ "${file.name}" อ่านสำเร็จ! ✓`;
                fileStatusElement.style.color = 'var(--success-color)';
                
                showNotification(`อ่านไฟล์ ${file.name} สำเร็จ!`, "success");
                processSelectedDay(); 

            } catch (error) {
                console.error("เกิดข้อผิดพลาดในการอ่านไฟล์:", error);
                fileStatusElement.classList.remove('loading');
                fileStatusElement.textContent = `เกิดข้อผิดพลาด: ${error.message}`;
                fileStatusElement.style.color = 'var(--warning-color)';
                sheetData = [];
                finalOutputElement.value = '';
                showNotification(error.message, "error");
            }
        };
        
        reader.onerror = function() {
            fileStatusElement.classList.remove('loading');
            fileStatusElement.textContent = 'ไม่สามารถอ่านไฟล์ได้';
            fileStatusElement.style.color = 'var(--warning-color)';
            showNotification("ไม่สามารถอ่านไฟล์ได้", "error");
        };
        
        reader.readAsArrayBuffer(file);
    }

    // ฟังก์ชันประมวลผลวันที่เลือก
    function processSelectedDay() {
        trafficInputElement.value = '';
        transInputElement.value = '';
        finalOutputElement.value = '';
        finalOutputElement.classList.remove('error-message');
        finalOutputElement.readOnly = true;
        editButton.innerHTML = '<i class="fas fa-pencil-alt"></i> แก้ไข';
        editButton.classList.remove('lock-button');
        editButton.classList.add('edit-button');

        if (sheetData.length === 0) {
            finalOutputElement.value = 'กรุณาอัปโหลดไฟล์ข้อมูลก่อน';
            finalOutputElement.classList.add('error-message');
            return;
        }

        const selectedDayValue = daySelector.value;
        const selectedDayFormatted = String(selectedDayValue).padStart(2, '0');
        const targetDateString = `${currentYear}${currentMonthFormatted}${selectedDayFormatted}`;

        const targetRow = sheetData.find(row => String(row[0]).trim() === targetDateString);

        if (targetRow) {
            const trafficStartCol = excelColToIndex('BZ');
            const trafficEndCol = excelColToIndex('CQ');
            const transStartCol = excelColToIndex('CS');
            const transEndCol = excelColToIndex('DJ');

            const trafficValues = targetRow.slice(trafficStartCol, trafficEndCol + 1);
            const transValues = targetRow.slice(transStartCol, transEndCol + 1);

            trafficInputElement.value = trafficValues.join(' ');
            transInputElement.value = transValues.join(' ');
            
            generateFinalOutput();
        } else {
            finalOutputElement.value = "ไม่มีข้อมูลที่อ่านได้";
            finalOutputElement.classList.add('error-message');
        }
    }

    // ฟังก์ชันสร้างผลลัพธ์สุดท้าย
    function generateFinalOutput() {
        const selectedDayValue = daySelector.value;
        const selectedDayFormatted = String(selectedDayValue).padStart(2, '0');
        
        const rawTrafficString = trafficInputElement.value.trim();
        const rawTransString = transInputElement.value.trim();
        
        if (!rawTrafficString && !rawTransString) {
            return;
        }

        const trafficValues = rawTrafficString ? rawTrafficString.split(/\s+/) : [];
        const transValues = rawTransString ? rawTransString.split(/\s+/) : [];
        
        const finalOutputLines = [];
        const numberOfLines = branchCodes.length;

        for (let i = 0; i < numberOfLines; i++) {
            const trafficValue = (trafficValues[i] || '').toString().trim();
            const transValue = (transValues[i] || '').toString().trim();
            
            const paddedTraffic = trafficValue.padStart(7, '0');
            const paddedTrans = transValue.padStart(7, '0');
            
            const combinedLine = `${branchCodes[i]}${currentMonthFormatted}-${selectedDayFormatted}${paddedTraffic}${paddedTrans}`;
            finalOutputLines.push(combinedLine);
        }
        
        finalOutputElement.value = finalOutputLines.join('\n');
    }

    // ฟังก์ชันบันทึกไฟล์
    function saveToFile() {
        if (!finalOutputElement.readOnly) {
            showNotification("กรุณากด 'ล็อคการแก้ไข' ก่อนทำการ Export", "warning");
            return;
        }
        
        const content = finalOutputElement.value;
        if (finalOutputElement.classList.contains('error-message')) {
            showNotification("ไม่สามารถบันทึกได้ เนื่องจากไม่มีข้อมูลที่ถูกต้อง", "error");
            return;
        }
        
        if (!content.trim() || content.includes("00000000000000")) {
            showNotification("ไม่มีข้อมูลให้บันทึก หรือข้อมูลไม่สมบูรณ์", "warning");
            return;
        }

        const selectedDay = String(daySelector.value).padStart(2, '0');
        const filename = `AEOthailand_TRAFFIC_${currentYear}${currentMonthFormatted}${selectedDay}.txt`;
        
        const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
        
        showNotification(`ดาวน์โหลดไฟล์ ${filename} สำเร็จ!`, "success");
    }

    // ฟังก์ชันส่งไฟล์ไปยัง SFTP (ใช้ cURL)
    function sendFileToFTP() {
        if (!finalOutputElement.readOnly) {
            showNotification("กรุณากด 'ล็อคการแก้ไข' ก่อนทำการส่ง SFTP", "warning");
            return;
        }
        
        const content = finalOutputElement.value;
        if (finalOutputElement.classList.contains('error-message') || !content.trim()) {
            showNotification("ไม่มีข้อมูลที่ถูกต้องสำหรับส่งไป SFTP", "error");
            return;
        }

        const selectedDay = String(daySelector.value).padStart(2, '0');
        const filename = `AEOthailand_TRAFFIC_${currentYear}${currentMonthFormatted}${selectedDay}.txt`;

        // แสดงสถานะกำลังส่ง
        const originalStatus = fileStatusElement.textContent;
        fileStatusElement.textContent = `กำลังส่งไฟล์ ${filename} ไปยัง SFTP...`;
        fileStatusElement.style.color = 'var(--edit-color)';
        fileStatusElement.classList.add('loading');
        
        // ปิดการใช้งานปุ่ม SFTP ชั่วคราว
        ftpButton.disabled = true;
        ftpButton.style.opacity = '0.6';
        
        const formData = new FormData();
        formData.append('filename', filename);
        formData.append('content', content);

        // ใช้ cURL method
        fetch('upload_to_sftp.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            fileStatusElement.classList.remove('loading');
            ftpButton.disabled = false;
            ftpButton.style.opacity = '1';
            
            if (data.success) {
                showNotification(`อัปโหลดไฟล์ ${filename} ผ่าน SFTP สำเร็จ!`, "success");
                fileStatusElement.textContent = `อัปโหลด SFTP สำเร็จ! ✓`;
                fileStatusElement.style.color = 'var(--success-color)';
            } else {
                showNotification(`เกิดข้อผิดพลาดในการอัปโหลด SFTP: ${data.message}`, "error");
                fileStatusElement.textContent = `อัปโหลด SFTP ล้มเหลว`;
                fileStatusElement.style.color = 'var(--warning-color)';
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            fileStatusElement.classList.remove('loading');
            ftpButton.disabled = false;
            ftpButton.style.opacity = '1';
            
            showNotification('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์', "error");
            fileStatusElement.textContent = originalStatus;
            fileStatusElement.style.color = 'var(--secondary-color)';
        });
    }

    // ฟังก์ชันแสดงการแจ้งเตือน
    function showNotification(message, type = 'info') {
        // สร้าง notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            max-width: 400px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        `;

        // กำหนดสีตามประเภท
        switch(type) {
            case 'success':
                notification.style.background = 'var(--success-gradient)';
                break;
            case 'error':
                notification.style.background = 'var(--warning-gradient)';
                break;
            case 'warning':
                notification.style.background = 'var(--edit-gradient)';
                notification.style.color = 'var(--text-color)';
                break;
            default:
                notification.style.background = 'var(--primary-gradient)';
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        // แสดง notification
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);

        // ซ่อน notification หลัง 5 วินาที
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    // Event Listeners
    document.getElementById('fileUploader').addEventListener('change', handleFileSelect);
    document.getElementById('saveButton').addEventListener('click', saveToFile);
    editButton.addEventListener('click', toggleEditMode);
    ftpButton.addEventListener('click', sendFileToFTP);

    // เรียกใช้ฟังก์ชันเริ่มต้น
    processSelectedDay();

    // Smooth scroll และ effects เพิ่มเติม
    document.addEventListener('DOMContentLoaded', function() {
        // Add entrance animations
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });
    });

    // เพิ่มการบันทึก Activity Log ผ่าน JavaScript (ตัวเลือก)
    function logUserActivity(action, details = '') {
        fetch('log_activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                details: details
            })
        }).catch(error => {
            console.log('Log error:', error);
        });
    }

    // Log activities
    document.getElementById('fileUploader').addEventListener('change', function() {
        if (this.files[0]) {
            logUserActivity('FILE_UPLOAD', 'File: ' + this.files[0].name);
        }
    });

    ftpButton.addEventListener('click', function() {
        logUserActivity('SFTP_UPLOAD_ATTEMPT', 'Attempting SFTP upload');
    });

</script>

</body>
</html>