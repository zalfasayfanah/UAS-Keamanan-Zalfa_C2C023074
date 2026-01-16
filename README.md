# 🔐 UAS Keamanan Data dan Informasi (Praktikum)

**Nama:** Zalfaulislam Sayfanah  
**NIM:** C2C023074  
**Kelas:** B  
**Mata Kuliah:** IF2350073 - Keamanan Data dan Informasi (Praktikum)  
**Dosen:** Dr. Dhendra Marutho, S.Kom., M.Kom  
**Tanggal:** 13 Februari 2025

---

## 📋 Deskripsi Project

Aplikasi web sederhana dengan **dua versi** (`/vulnerable` dan `/secure`) untuk demonstrasi dan pembelajaran keamanan aplikasi web. Project ini dibuat sebagai bagian dari Ujian Akhir Semester (UAS) untuk menunjukkan pemahaman tentang:

- Identifikasi kerentanan keamanan web
- Implementasi kontrol keamanan
- Teknik pengujian keamanan non-destruktif
- Analisis risiko keamanan

---

## 🗂️ Struktur Project

```
uas-keamanan-zalfa_C2C023074/
├── vulnerable/              # Versi dengan kerentanan (untuk testing)
│   ├── index.php           # Dashboard vulnerable
│   ├── login.php           # Login module (Brute Force vulnerable)
│   ├── comment.php         # Comment module (XSS vulnerable)
│   └── fileviewer.php      # File viewer (LFI vulnerable)
├── secure/                 # Versi aman (dengan mitigasi)
│   ├── index.php           # Dashboard secure
│   ├── login.php           # Login module (dengan rate limiting)
│   ├── comment.php         # Comment module (dengan sanitasi)
│   └── fileviewer.php      # File viewer (dengan validasi)
├── index.php               # Entry point / Router utama
└── README.md               # Dokumentasi ini
```

---

## 🎯 Modul yang Diimplementasikan

### 1. **Login Module** 🔑
- **Vulnerable Version:**
  - Kerentanan: Brute Force Attack, Weak Password
  - Password lemah: `admin:123456`, `user:password`
  - Tidak ada rate limiting
  - Informative error messages

- **Secure Version:**
  - Rate limiting (max 5 percobaan per 15 menit)
  - Password hashing dengan bcrypt
  - Account lockout temporary
  - Generic error messages

### 2. **Comment Module** 💬
- **Vulnerable Version:**
  - Kerentanan: XSS (Cross-Site Scripting)
  - Tidak ada sanitasi input
  - Tidak ada CSRF protection
  - HTML/JavaScript langsung di-render

- **Secure Version:**
  - Input sanitization dengan `htmlspecialchars()`
  - CSRF token validation
  - Content Security Policy headers
  - Output encoding

### 3. **File Viewer Module** 📁
- **Vulnerable Version:**
  - Kerentanan: LFI (Local File Inclusion)
  - Path traversal allowed (`../`)
  - Tidak ada validasi path
  - Akses file sistem sensitif

- **Secure Version:**
  - Whitelist file yang diperbolehkan
  - Path validation & sanitization
  - Basename validation
  - File storage di direktori non-public

---

## 🚀 Cara Menjalankan

### Prasyarat
- PHP 7.4 atau lebih tinggi
- Web server (Apache/Nginx) atau PHP built-in server
- Browser modern (Chrome, Firefox, Edge)

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   git clone [repository-url]
   cd uas-keamanan-zalfa_C2C023074
   ```

2. **Jalankan PHP Built-in Server**
   ```bash
   php -S localhost:8000
   ```

3. **Buka di Browser**
   ```
   http://localhost:8000
   ```

4. **Pilih Versi untuk Testing**
   - Versi Vulnerable: `http://localhost:8000?version=vulnerable&module=index`
   - Versi Secure: `http://localhost:8000?version=secure&module=index`

---

## 🧪 Panduan Testing

### A. Testing Login Module (Brute Force)

**Vulnerable Version:**
1. Buka Login Module di versi vulnerable
2. Coba login dengan kredensial salah 10-20 kali
3. Perhatikan tidak ada blocking atau delay
4. Screenshot: Error message menunjukkan jumlah percobaan
5. Test dengan: `admin:123456` (berhasil login dengan password lemah)

**Secure Version:**
1. Coba login dengan kredensial salah 5 kali
2. Perhatikan account lockout setelah 5 percobaan
3. Tunggu 15 menit atau reset session
4. Screenshot: Pesan "Account locked" muncul

---

### B. Testing Comment Module (XSS)

**Vulnerable Version:**
1. Buka Comment Module di versi vulnerable
2. Test payloads:
   ```html
   <script>alert('XSS')</script>
   <img src=x onerror="alert('XSS')">
   <b>Bold Text</b>
   ```
3. Screenshot: Alert popup atau HTML ter-render
4. Perhatikan script dieksekusi

**Secure Version:**
1. Coba payload XSS yang sama
2. Perhatikan input di-sanitasi
3. Screenshot: Tag HTML ditampilkan sebagai text
4. Script tidak dieksekusi

---

### C. Testing File Viewer (LFI)

**Vulnerable Version:**
1. Buka File Viewer di versi vulnerable
2. Test file normal: `welcome.txt`, `data.txt`
3. Test path traversal:
   ```
   ../config.txt
   ../../etc/passwd
   logs/app.log
   ```
4. Screenshot: File sensitif berhasil diakses
5. Perhatikan credential dan data sistem ter-expose

**Secure Version:**
1. Coba akses file dengan path traversal
2. Perhatikan akses ditolak
3. Hanya file dalam whitelist yang bisa diakses
4. Screenshot: Error message "File tidak diizinkan"

---

## 📊 Soal 1: Analisis Kerentanan

### 1. Jenis Kerentanan pada Tiap Modul

| Modul | Kerentanan | Deskripsi |
|-------|-----------|-----------|
| Login | Brute Force | Tidak ada pembatasan percobaan login |
| Login | Weak Password | Password lemah mudah ditebak |
| Comment | XSS | Input tidak disanitasi, script dieksekusi |
| Comment | No CSRF | Tidak ada token validasi |
| File Viewer | LFI | Path traversal memungkinkan akses file sensitif |

### 2. Parameter/Flow yang Menyebabkan Kerentanan

**Login Module:**
- Parameter: `username`, `password` (POST)
- Flow: Input → No validation → Direct comparison → No rate limit
- Vulnerable code: Password plaintext, no hashing

**Comment Module:**
- Parameter: `comment` (POST)
- Flow: Input → No sanitization → Direct output → Script execution
- Vulnerable code: `echo $comment` tanpa `htmlspecialchars()`

**File Viewer Module:**
- Parameter: `filename` (POST)
- Flow: Input → No path validation → Direct file access → Data exposure
- Vulnerable code: Penggunaan `../` untuk traversal

### 3. Screenshot Pengujian Non-Destruktif

Screenshot yang diperlukan:
1. ✅ Login gagal berkali-kali tanpa blocking
2. ✅ XSS payload berhasil dieksekusi
3. ✅ File sensitif berhasil diakses via LFI
4. ✅ Perbandingan antara vulnerable vs secure

---

## 🔒 Soal 2: Implementasi Kontrol Keamanan

### Kontrol Keamanan yang Diterapkan

| Kontrol | Implementasi | Modul |
|---------|-------------|-------|
| Input Validation | `htmlspecialchars()`, whitelist | Comment, File Viewer |
| CSRF Token | Session-based token | Comment |
| Password Hashing | bcrypt dengan `password_hash()` | Login |
| Rate Limiting | Session tracking, lockout | Login |
| UUID | Unique identifiers untuk resources | Comment |
| Non-public Storage | File di luar webroot | File Viewer |

### Perbedaan Kode: Vulnerable vs Secure

**Login - Vulnerable:**
```php
if ($username === 'admin' && $password === '123456') {
    $_SESSION['logged_in'] = true;
}
```

**Login - Secure:**
```php
if ($attempts >= 5) {
    die("Account locked. Try again in 15 minutes.");
}
if (password_verify($password, $hashedPassword)) {
    $_SESSION['logged_in'] = true;
}
```

**Comment - Vulnerable:**
```php
echo $comment; // XSS!
```

**Comment - Secure:**
```php
echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
```

**File Viewer - Vulnerable:**
```php
$file = $_POST['filename']; // LFI!
readfile($file);
```

**File Viewer - Secure:**
```php
$filename = basename($_POST['filename']);
$whitelist = ['welcome.txt', 'data.txt'];
if (!in_array($filename, $whitelist)) {
    die("Access denied");
}
```

---

## 🎯 Soal 3: Analisis Risiko

### Analisis Risiko per Modul

#### Login Module

| Aspek | Vulnerable | Secure |
|-------|-----------|--------|
| **Dampak** | HIGH - Account takeover, data breach | LOW - Limited access attempts |
| **Kemungkinan** | HIGH - Easy to exploit | LOW - Protected by rate limiting |
| **Prioritas** | CRITICAL - Patch immediately | ✅ MITIGATED |

**Temuan Sebelum Mitigasi:**
- Unlimited login attempts
- Weak password accepted
- No account lockout

**Temuan Setelah Mitigasi:**
- Rate limiting aktif (max 5 attempts)
- Strong password required
- Temporary account lockout

---

#### Comment Module

| Aspek | Vulnerable | Secure |
|-------|-----------|--------|
| **Dampak** | HIGH - Session hijacking, malware | LOW - XSS prevented |
| **Kemungkinan** | HIGH - Direct XSS exploitation | LOW - Input sanitized |
| **Prioritas** | CRITICAL - Immediate fix needed | ✅ MITIGATED |

**Temuan Sebelum Mitigasi:**
- XSS injection possible
- No CSRF protection
- Cookie theft risk

**Temuan Setelah Mitigasi:**
- Input sanitization aktif
- CSRF token validation
- XSS attack blocked

---

#### File Viewer Module

| Aspek | Vulnerable | Secure |
|-------|-----------|--------|
| **Dampak** | CRITICAL - System file exposure | LOW - Limited file access |
| **Kemungkinan** | HIGH - Simple path traversal | LOW - Whitelist enforcement |
| **Prioritas** | CRITICAL - Major security risk | ✅ MITIGATED |

**Temuan Sebelum Mitigasi:**
- Path traversal possible
- System files accessible
- Credential exposure

**Temuan Setelah Mitigasi:**
- Whitelist validation
- Path sanitization
- Restricted file access

---

## 🛡️ Teknik Mitigasi yang Diterapkan

### 1. Input Validation & Sanitization
```php
// Sanitize user input
$clean_input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

// Validate against whitelist
$allowed = ['file1.txt', 'file2.txt'];
if (!in_array($input, $allowed)) {
    die("Invalid input");
}
```

### 2. CSRF Token
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed");
}
```

### 3. Password Hashing
```php
// Hash password
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Verify password
if (password_verify($input_password, $hashed)) {
    // Login success
}
```

### 4. Rate Limiting
```php
// Track attempts
$_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
$_SESSION['lockout_time'] = time();

// Check lockout
if ($_SESSION['login_attempts'] >= 5) {
    if (time() - $_SESSION['lockout_time'] < 900) {
        die("Account locked for 15 minutes");
    }
}
```

---

## 📸 Screenshot & Dokumentasi

### Screenshot yang Diperlukan:

1. **Homepage:**
   - [ ] Tampilan pilihan versi vulnerable & secure

2. **Login Module:**
   - [ ] Vulnerable: Multiple failed attempts tanpa blocking
   - [ ] Secure: Account lockout setelah 5 percobaan

3. **Comment Module:**
   - [ ] Vulnerable: XSS payload berhasil dieksekusi
   - [ ] Secure: XSS payload di-sanitasi

4. **File Viewer:**
   - [ ] Vulnerable: File sensitif berhasil diakses
   - [ ] Secure: Access denied untuk path traversal

5. **Burp Suite Testing:**
   - [ ] Intercept request vulnerable
   - [ ] Show security headers di secure version

---

## 🔍 Teknik Pengujian (Burp Suite / Manual)

### Testing dengan Burp Suite:

1. **Setup Proxy:**
   - Configure browser ke Burp (127.0.0.1:8080)
   - Enable intercept

2. **Login Testing:**
   - Intercept POST request ke login
   - Send to Repeater
   - Modify parameters untuk brute force

3. **XSS Testing:**
   - Intercept comment submission
   - Inject payload di parameter
   - Observe response

4. **LFI Testing:**
   - Intercept file request
   - Modify filename parameter
   - Test various traversal payloads

### Manual Testing:
- Browser Developer Tools (F12)
- Inspect Network requests
- Check Response headers
- Test input validation

---

## 📚 Referensi

- OWASP Top 10 Web Application Security Risks
- CWE (Common Weakness Enumeration)
- PHP Security Best Practices
- CSRF Prevention Cheat Sheet
- XSS Prevention Cheat Sheet

---

## ⚠️ Disclaimer

**PENTING:** Aplikasi ini dibuat untuk tujuan **PEMBELAJARAN** dan **SIMULASI PENGUJIAN KEAMANAN** di lingkungan lokal. 

- ❌ **JANGAN** gunakan di production/live server
- ❌ **JANGAN** gunakan untuk menyerang sistem orang lain
- ✅ **HANYA** untuk pembelajaran dan pengujian lokal
- ✅ **PAHAMI** bahwa versi vulnerable sengaja tidak aman

---

## 👤 Informasi Mahasiswa

**Nama:** Zalfaulislam Sayfanah  
**NIM:** C2C023074  
**Kelas:** B  
**Email:** zalfaulislam@gmail.com  


---

## 📝 Catatan Pengumpulan

**Output yang Dikumpulkan:**
1. ✅ Folder project lengkap (`/vulnerable` & `/secure`)
2. ✅ Screenshot pengujian semua modul
3. ✅ README.md dengan dokumentasi lengkap
4. ✅ Link GitHub repository (public)

**Deadline:** 13 Februari 2025  
**Metode Pengumpulan:** [Sesuai instruksi dosen]

---


**© 2025 - Dibuat untuk UAS Keamanan Data & Informasi**

