# 🔐 TRAFFIC FEED AEO - ระบบที่มีความปลอดภัย

ระบบจัดการข้อมูล Traffic Feed พร้อมระบบ Login ที่ปลอดภัย

## 📋 ไฟล์ในระบบ

```
📁 Project Root/
├── 🔑 config.php          # ตั้งค่าระบบและผู้ใช้
├── 🔐 login.php           # หน้า Login
├── 🚪 logout.php          # ออกจากระบบ
├── 🛡️ auth_check.php      # ตรวจสอบสิทธิ์
├── 📊 index.php           # หน้าหลัก (มีระบบรักษาความปลอดภัย)
├── 📤 upload_to_sftp.php  # อัปโหลดไฟล์ผ่าน SFTP (cURL)
├── 📝 log_activity.php    # บันทึกกิจกรรม
├── 📁 logs/               # โฟลเดอร์เก็บ Log (สร้างอัตโนมัติ)
└── 📖 README.md           # คำแนะนำนี้
```

## 🚀 การติดตั้ง

### 1. **อัปโหลดไฟล์**
```bash
# อัปโหลดไฟล์ทั้งหมดไปยัง Web Server
# ตรวจสอบว่า Web Server รองรับ PHP 7.4+
```

### 2. **ตั้งค่าสิทธิ์โฟลเดอร์**
```bash
# ให้สิทธิ์เขียนโฟลเดอร์ logs
chmod 755 logs/
# หรือหากยังไม่มีโฟลเดอร์
mkdir logs && chmod 755 logs/
```

### 3. **ตรวจสอบ PHP Extensions**
- ✅ **cURL** (สำหรับ SFTP)
- ✅ **JSON** (สำหรับ API responses)
- ✅ **Session** (สำหรับ Login)

### 4. **แก้ไขการตั้งค่า SFTP**
เปิดไฟล์ `upload_to_sftp.php` และแก้ไขข้อมูล SFTP:
```php
$sftp_server = "YOUR_SFTP_SERVER";     // เซิร์ฟเวอร์ SFTP
$sftp_port   = 22;                     // Port (ปกติคือ 22)
$sftp_user   = "YOUR_USERNAME";        // ชื่อผู้ใช้
$sftp_pass   = "YOUR_PASSWORD";        // รหัสผ่าน
$sftp_path   = "/path/to/upload/";     // โฟลเดอร์ปลายทาง
```

## 👥 บัญชีผู้ใช้เริ่มต้น

| ชื่อผู้ใช้ | รหัสผ่าน | บทบาท | คำอธิบาย |
|-----------|---------|--------|----------|
| `admin` | `AEO@2024!` | Administrator | ผู้ดูแลระบบ |
| `traffic` | `Traffic123!` | User | ผู้จัดการ Traffic |
| `aeo` | `AEO@Traffic2024` | User | ผู้ปฏิบัติงาน AEO |

### 🔧 เปลี่ยนรหัสผ่าน
แก้ไขในไฟล์ `config.php`:
```php
$users = [
    'admin' => [
        'password' => password_hash('YOUR_NEW_PASSWORD', PASSWORD_DEFAULT),
        'name' => 'Administrator',
        'role' => 'admin'
    ],
    // ... ผู้ใช้อื่นๆ
];
```

### ➕ เพิ่มผู้ใช้ใหม่
```php
'new_username' => [
    'password' => password_hash('new_password', PASSWORD_DEFAULT),
    'name' => 'Display Name',
    'role' => 'user'
],
```

## 🔒 ฟีเจอร์ความปลอดภัย

### ✅ **Authentication & Authorization**
- ระบบ Login/Logout ที่ปลอดภัย
- Password Hashing (PHP password_hash)
- Session Management ที่มีความปลอดภัย
- Session Timeout (8 ชั่วโมง)

### ✅ **CSRF Protection**
- CSRF Token สำหรับทุก Form
- Validation ข้อมูลที่ส่งมา

### ✅ **Input Validation**
- ตรวจสอบรูปแบบชื่อไฟล์
- จำกัดขนาดไฟล์ (10MB)
- Sanitize ข้อมูลทั้งหมด

### ✅ **Activity Logging**
- บันทึกการ Login/Logout
- บันทึกการอัปโหลดไฟล์
- บันทึก Error ต่างๆ
- บันทึก IP Address

### ✅ **Brute Force Protection**
- Delay 2 วินาทีเมื่อ Login ผิด
- Log การพยายาม Login ผิด

## 📊 การใช้งาน

### 1. **เข้าสู่ระบบ**
- เปิดเว็บไซต์ จะ redirect ไปหน้า Login อัตโนมัติ
- กรอกชื่อผู้ใช้และรหัสผ่าน
- คลิกที่บัญชี Demo เพื่อกรอกข้อมูลอัตโนมัติ

### 2. **อัปโหลดไฟล์**
- อัปโหลดไฟล์ Excel (.xlsx, .xls)
- ต้องมีชีต 'Calenda'
- เลือกวันที่ที่ต้องการ

### 3. **ส่งข้อมูล**
- **Export** - ดาวน์โหลดไฟล์ .txt
- **Send to SFTP** - ส่งไฟล์ไปยัง SFTP Server

### 4. **ออกจากระบบ**
- คลิกปุ่ม "ออกจากระบบ" ที่มุมขวาบน

## 🔧 การแก้ไขปัญหา

### ❗ **ไม่สามารถ Login ได้**
1. ตรวจสอบชื่อผู้ใช้และรหัสผ่าน
2. ตรวจสอบไฟล์ `logs/activity.log`
3. ตรวจสอบว่า Session ทำงานถูกต้อง

### ❗ **SFTP ไม่ทำงาน**
1. ตรวจสอบการตั้งค่าใน `upload_to_sftp.php`
2. ตรวจสอบว่า cURL รองรับ SFTP:
   ```bash
   php -m | grep curl
   php -r "print_r(curl_version());"
   ```
3. ทดสอบการเชื่อมต่อ SFTP

### ❗ **ไม่สามารถเขียน Log ได้**
```bash
# ตรวจสอบและแก้ไขสิทธิ์
ls -la logs/
chmod 755 logs/
chmod 644 logs/*.log  # ถ้ามีไฟล์ log แล้ว
```

### ❗ **Session หมดอายุเร็วเกินไป**
แก้ไขใน `auth_check.php`:
```php
$session_timeout = 8 * 60 * 60; // เปลี่ยนเป็นเวลาที่ต้องการ (วินาที)
```

## 📈 การปรับแต่งระบบ

### 🎨 **เปลี่ยนธีม/สี**
แก้ไข CSS Variables ใน `index.php` และ `login.php`:
```css
:root {
    --primary-gradient: linear-gradient(135deg, #YOUR_COLOR1, #YOUR_COLOR2);
    --secondary-gradient: linear-gradient(135deg, #YOUR_COLOR3, #YOUR_COLOR4);
    /* ... */
}
```

### 📝 **เปลี่ยนข้อมูลสาขา**
แก้ไขใน `index.php`:
```javascript
const branchCodes = [
    'YOUR_BRANCH_CODE1', 'YOUR_BRANCH_CODE2',
    // ... เพิ่มรหัสสาขาของคุณ
];
```

### 🔧 **เปลี่ยน Excel Columns**
แก้ไขใน `index.php`:
```javascript
const trafficStartCol = excelColToIndex('YOUR_START_COL');
const trafficEndCol = excelColToIndex('YOUR_END_COL');
// ...
```

## 🛡️ ข้อแนะนำด้านความปลอดภัย

### ✅ **การผลิต (Production)**
1. **เปลี่ยนรหัสผ่านเริ่มต้น** ทั้งหมด
2. **ใช้ HTTPS** เท่านั้น
3. **ซ่อนข้อมูล Demo** ในหน้า Login
4. **ตั้งค่า Firewall** ให้เหมาะสม
5. **สำรองข้อมูล Log** เป็นประจำ

### ✅ **การบำรุงรักษา**
1. **ตรวจสอบ Log** เป็นประจำ
2. **อัปเดต PHP** เป็นเวอร์ชันล่าสุด
3. **เปลี่ยนรหัสผ่าน** เป็นประจำ
4. **ทำความสะอาด Log เก่า** ประจำเดือน

## 📞 การสนับสนุน

หากพบปัญหาหรือต้องการความช่วยเหลือ:
1. ตรวจสอบไฟล์ `logs/activity.log`
2. ตรวจสอบ PHP Error Log
3. ทดสอบระบบตามขั้นตอนในคู่มือนี้

---

**🎉 ระบบพร้อมใช้งาน! ปลอดภัย มีประสิทธิภาพ และใช้งานง่าย**