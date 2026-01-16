<?php
// SECURE FILE VIEWER MODULE
// Mitigasi: Whitelist Validation, Path Sanitization, Basename Check, Non-public Storage

$fileContent = '';
$filename = '';
$error = '';
$success = '';

// SECURITY CONTROL 1: Whitelist - Only allowed files
$whitelist = [
    'welcome.txt' => 'Selamat datang di File Viewer System yang Aman!

Sistem ini menggunakan whitelist validation untuk mencegah akses file yang tidak diizinkan.
Hanya file yang terdaftar dalam whitelist yang dapat diakses.

Fitur Keamanan:
- Whitelist validation
- Path sanitization
- Basename verification
- No path traversal allowed',
    
    'public-data.txt' => 'Data Publik yang Aman untuk Diakses
=====================================

Daftar Pengguna:
1. John Doe - john@example.com
2. Jane Smith - jane@example.com
3. Bob Wilson - bob@example.com

Informasi ini adalah data publik yang aman untuk dibaca.
Tidak ada informasi sensitif yang ter-expose.',
    
    'info.txt' => 'Informasi Sistem File Viewer
============================

Versi: 2.0 (Secure)
Last Update: 2025-01-16
Status: Production Ready

Keamanan yang Diterapkan:
✓ Whitelist file validation
✓ Path traversal prevention
✓ Input sanitization
✓ Restricted file access'
];

// SECURITY CONTROL 2: Generate CSRF Token for form
if (!isset($_SESSION['file_csrf_token'])) {
    $_SESSION['file_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    
    // SECURITY CONTROL 3: CSRF Token Validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['file_csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token. Request ditolak.";
    } else {
        $input_filename = $_POST['filename'] ?? '';
        
        // SECURITY CONTROL 4: Input Validation - Not empty
        if (empty(trim($input_filename))) {
            $error = "Nama file tidak boleh kosong.";
        } else {
            // SECURITY CONTROL 5: Path Sanitization - Use basename to prevent traversal
            $sanitized_filename = basename($input_filename);
            
            // SECURITY CONTROL 6: Remove any remaining dangerous characters
            $sanitized_filename = str_replace(['..', '/', '\\', "\0"], '', $sanitized_filename);
            
            // SECURITY CONTROL 7: Whitelist Validation
            if (!array_key_exists($sanitized_filename, $whitelist)) {
                $error = "Akses ditolak! File '{$sanitized_filename}' tidak ada dalam whitelist atau tidak diizinkan.";
                
                // Log attempt (in production, log to file)
                if (strpos($input_filename, '..') !== false || strpos($input_filename, '/') !== false) {
                    $error .= " <br><span class='text-red-700 font-semibold'>⚠️ Path traversal attempt detected!</span>";
                }
            } else {
                // File is in whitelist, safe to read
                $filename = $sanitized_filename;
                $fileContent = $whitelist[$sanitized_filename];
                $success = "File berhasil dibaca dengan aman!";
            }
        }
        
        // Regenerate CSRF token
        $_SESSION['file_csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">📁</div>
            <h2 class="text-3xl font-bold text-indigo-700">File Viewer Module</h2>
            <p class="text-sm text-gray-600 mt-2">Versi Secure</p>
        </div>

        <!-- Security Features Badge -->
        <div class="bg-indigo-50 border border-indigo-300 rounded-lg p-4 mb-6">
            <p class="text-sm font-semibold text-indigo-800 mb-2">✅ Fitur Keamanan Aktif:</p>
            <ul class="text-xs text-indigo-700 space-y-1">
                <li>• Whitelist file validation (hanya <?= count($whitelist) ?> file diizinkan)</li>
                <li>• Path sanitization dengan basename()</li>
                <li>• Path traversal prevention (../ blocked)</li>
                <li>• CSRF token protection</li>
                <li>• Input validation & filtering</li>
            </ul>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <span class="text-xl mr-2">✅</span>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <span class="text-xl mr-2">🚫</span>
                <div><?= $error ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- File Viewer Form -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4">📂 Pilih File untuk Dilihat</h3>
            <form method="POST" class="space-y-4">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['file_csrf_token']) ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama File:
                        <span class="text-gray-500 text-xs">(Hanya file dalam whitelist)</span>
                    </label>
                    <input type="text" 
                           name="filename" 
                           value="<?= htmlspecialchars($filename) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Contoh: welcome.txt"
                           required>
                    <p class="text-xs text-gray-500 mt-1">
                        ℹ️ Path traversal (../) akan diblokir secara otomatis
                    </p>
                </div>

                <button type="submit" 
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                    🔍 Lihat File
                </button>
            </form>

            <!-- LFI Prevention Demo -->
            <div class="mt-4 bg-gray-100 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-700 mb-2">🛡️ Test LFI Prevention:</p>
                <div class="grid md:grid-cols-2 gap-2 text-xs">
                    <div>
                        <p class="font-semibold text-green-700 mb-1">✅ File yang Diizinkan:</p>
                        <ul class="space-y-1 font-mono text-gray-600">
                            <?php foreach (array_keys($whitelist) as $file): ?>
                            <li class="bg-white p-1 rounded"><?= htmlspecialchars($file) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold text-red-700 mb-1">🚫 Akan Diblokir:</p>
                        <ul class="space-y-1 font-mono text-gray-600">
                            <li class="bg-white p-1 rounded">../config.txt</li>
                            <li class="bg-white p-1 rounded">../../etc/passwd</li>
                            <li class="bg-white p-1 rounded">logs/app.log</li>
                            <li class="bg-white p-1 rounded">/etc/shadow</li>
                        </ul>
                    </div>
                </div>
                <p class="text-green-600 font-semibold mt-2 text-xs">✅ Coba input path traversal untuk test protection</p>
            </div>
        </div>

        <!-- File Content Display -->
        <?php if ($fileContent): ?>
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-lg">📄 Isi File: <?= htmlspecialchars($filename) ?></h3>
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                    ✅ SAFE
                </span>
            </div>
            
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 text-green-400 rounded-lg p-6 font-mono text-sm overflow-x-auto shadow-inner">
                <pre class="whitespace-pre-wrap"><?= htmlspecialchars($fileContent) ?></pre>
            </div>

            <div class="mt-3 bg-green-50 border border-green-300 rounded p-3">
                <p class="text-sm text-green-800">
                    <strong>✅ File Aman!</strong> 
                    File ini ada dalam whitelist dan telah melewati validasi keamanan.
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Whitelist Files - Quick Access -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-3">📋 File yang Tersedia (Whitelist)</h3>
            <p class="text-xs text-gray-600 mb-4">Hanya file berikut yang dapat diakses untuk keamanan:</p>
            <div class="grid md:grid-cols-3 gap-3">
                <?php foreach (array_keys($whitelist) as $file): ?>
                <div class="bg-white border border-green-200 rounded-lg p-3 hover:shadow-md transition">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-green-500">✅</span>
                        <span class="font-mono text-sm font-semibold"><?= htmlspecialchars($file) ?></span>
                    </div>
                    <form method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['file_csrf_token']) ?>">
                        <input type="hidden" name="filename" value="<?= htmlspecialchars($file) ?>">
                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                            Buka File →
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="?version=secure&module=index" 
               class="block text-center text-gray-600 hover:text-gray-800 text-sm">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Security Analysis -->
    <div class="bg-green-50 border border-green-300 rounded-lg p-6 mt-6">
        <h3 class="font-bold text-green-800 mb-3">🛡️ Analisis Kontrol Keamanan</h3>
        <div class="text-sm text-green-900 space-y-3">
            <div>
                <p class="font-semibold mb-1">1. Whitelist Validation:</p>
                <pre class="bg-white p-2 rounded text-xs overflow-x-auto">$whitelist = ['welcome.txt', 'public-data.txt', 'info.txt'];

if (!array_key_exists($filename, $whitelist)) {
    die("Access denied - File not in whitelist");
}</pre>
            </div>
            
            <div>
                <p class="font-semibold mb-1">2. Path Sanitization:</p>
                <pre class="bg-white p-2 rounded text-xs overflow-x-auto">// Remove path traversal
$clean = basename($input);
$clean = str_replace(['..', '/', '\\'], '', $clean);</pre>
            </div>

            <div>
                <p class="font-semibold mb-1">3. Path Traversal Prevention:</p>
                <p class="text-xs text-green-700">Karakter "../" dan "/" dihapus, sehingga tidak bisa naik ke direktori parent</p>
            </div>

            <div>
                <p class="font-semibold mb-1">4. CSRF Protection:</p>
                <p class="text-xs text-green-700">Setiap request dilindungi dengan CSRF token untuk mencegah unauthorized access</p>
            </div>
        </div>
    </div>

    <!-- Comparison with Vulnerable Version -->
    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-6 mt-6">
        <h3 class="font-bold text-yellow-800 mb-3">⚖️ Perbedaan dengan Versi Vulnerable</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Aspek</th>
                        <th class="px-3 py-2 text-left">Vulnerable</th>
                        <th class="px-3 py-2 text-left">Secure</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-3 py-2 font-semibold">Path Validation</td>
                        <td class="px-3 py-2 text-red-600">❌ No validation</td>
                        <td class="px-3 py-2 text-green-600">✅ Whitelist + basename()</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">Path Traversal</td>
                        <td class="px-3 py-2 text-red-600">❌ Allowed (../ works)</td>
                        <td class="px-3 py-2 text-green-600">✅ Blocked (stripped)</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">File Access</td>
                        <td class="px-3 py-2 text-red-600">❌ Any file accessible</td>
                        <td class="px-3 py-2 text-green-600">✅ Whitelist only</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">CSRF Protection</td>
                        <td class="px-3 py-2 text-red-600">❌ No token</td>
                        <td class="px-3 py-2 text-green-600">✅ Token validation</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">Sensitive Files</td>
                        <td class="px-3 py-2 text-red-600">❌ config, passwd exposed</td>
                        <td class="px-3 py-2 text-green-600">✅ Not accessible</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Testing Demo -->
    <div class="bg-blue-50 border border-blue-300 rounded-lg p-6 mt-6">
        <h3 class="font-bold text-blue-800 mb-3">🧪 Demo: Test LFI Prevention</h3>
        <div class="space-y-3 text-sm">
            <div class="bg-white p-3 rounded">
                <p class="font-semibold text-gray-800 mb-2">Scenario 1: Akses file normal</p>
                <p class="text-xs text-gray-600 mb-1">Input: <code class="bg-gray-200 px-2 py-1 rounded">welcome.txt</code></p>
                <p class="text-xs text-green-600">✅ Result: File berhasil dibaca (ada di whitelist)</p>
            </div>
            
            <div class="bg-white p-3 rounded">
                <p class="font-semibold text-gray-800 mb-2">Scenario 2: Path traversal attempt</p>
                <p class="text-xs text-gray-600 mb-1">Input: <code class="bg-gray-200 px-2 py-1 rounded">../config.txt</code></p>
                <p class="text-xs text-red-600">🚫 Result: Akses ditolak + path traversal detected</p>
            </div>
            
            <div class="bg-white p-3 rounded">
                <p class="font-semibold text-gray-800 mb-2">Scenario 3: System file attempt</p>
                <p class="text-xs text-gray-600 mb-1">Input: <code class="bg-gray-200 px-2 py-1 rounded">../../etc/passwd</code></p>
                <p class="text-xs text-red-600">🚫 Result: Akses ditolak (tidak di whitelist)</p>
            </div>
        </div>
    </div>
</div>