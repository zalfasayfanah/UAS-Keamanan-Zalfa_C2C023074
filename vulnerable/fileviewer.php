<?php
// VULNERABLE FILE VIEWER MODULE
// Kerentanan: LFI (Local File Inclusion), Path Traversal, No Validation

$fileContent = '';
$filename = '';
$error = '';

// Simulasi file system (untuk demo)
$simulatedFiles = [
    'welcome.txt' => 'Selamat datang di File Viewer System!
    
Ini adalah contoh file yang aman untuk dibaca.
Sistem file viewer memungkinkan Anda melihat konten file.',
    
    'data.txt' => 'Data User Database:
==================
1. admin - admin@example.com
2. user - user@example.com
3. guest - guest@example.com

Last backup: 2025-01-15',
    
    '../config.txt' => '# Configuration File (SENSITIVE!)
# Database Configuration
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=admin123
DB_NAME=security_app

# API Keys
API_KEY=sk_live_51JXY2345678abcdef
SECRET_KEY=secret_key_12345

# Debug Mode
DEBUG=true',
    
    '../../etc/passwd' => '# System Password File (CRITICAL!)
root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
admin:x:1000:1000:Admin User:/home/admin:/bin/bash
www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin
mysql:x:111:116:MySQL Server:/var/lib/mysql:/bin/false',
    
    'logs/app.log' => '[2025-01-16 10:30:15] INFO: Application started
[2025-01-16 10:30:20] ERROR: Database connection failed
[2025-01-16 10:30:25] INFO: Admin login from IP: 192.168.1.100
[2025-01-16 10:35:42] WARNING: Multiple failed login attempts detected
[2025-01-16 10:40:18] ERROR: File upload failed - size too large
[2025-01-16 11:00:05] INFO: Backup process completed successfully'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $filename = $_POST['filename'] ?? '';
    
    // VULNERABILITY: No path validation - Direct use of user input
    // VULNERABILITY: Path traversal allowed (../)
    if (!empty($filename)) {
        if (isset($simulatedFiles[$filename])) {
            $fileContent = $simulatedFiles[$filename];
        } else {
            // VULNERABILITY: Informative error message
            $error = "File tidak ditemukan: " . htmlspecialchars($filename);
        }
    }
}
?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">📁</div>
            <h2 class="text-3xl font-bold text-purple-700">File Viewer Module</h2>
            <p class="text-sm text-gray-600 mt-2">Versi Vulnerable</p>
        </div>

        <!-- Vulnerability Warning -->
        <div class="bg-purple-50 border border-purple-300 rounded-lg p-4 mb-6">
            <p class="text-sm font-semibold text-purple-800 mb-2">⚠️ Kerentanan yang Ada:</p>
            <ul class="text-xs text-purple-700 space-y-1">
                <li>• LFI (Local File Inclusion)</li>
                <li>• Path Traversal (../ allowed)</li>
                <li>• Tidak ada validasi path</li>
                <li>• Tidak ada whitelist file</li>
            </ul>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <!-- File Viewer Form -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4">📂 Pilih File untuk Dilihat</h3>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama File:
                    </label>
                    <input type="text" 
                           name="filename" 
                           value="<?= htmlspecialchars($filename) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Contoh: welcome.txt atau ../config.txt"
                           required>
                </div>

                <button type="submit" 
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-semibold">
                    🔍 Lihat File
                </button>
            </form>

            <!-- LFI Testing Hints -->
            <div class="mt-4 bg-gray-100 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-700 mb-2">💡 File untuk Testing LFI:</p>
                <div class="grid md:grid-cols-2 gap-2 text-xs">
                    <div>
                        <p class="font-semibold text-green-700 mb-1">✅ File Normal:</p>
                        <ul class="space-y-1 font-mono text-gray-600">
                            <li class="bg-white p-1 rounded">welcome.txt</li>
                            <li class="bg-white p-1 rounded">data.txt</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold text-red-700 mb-1">⚠️ Path Traversal:</p>
                        <ul class="space-y-1 font-mono text-gray-600">
                            <li class="bg-white p-1 rounded">../config.txt</li>
                            <li class="bg-white p-1 rounded">../../etc/passwd</li>
                            <li class="bg-white p-1 rounded">logs/app.log</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Content Display -->
        <?php if ($fileContent): ?>
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-lg">📄 Isi File: <?= htmlspecialchars($filename) ?></h3>
                <?php if (strpos($filename, '../') !== false || strpos($filename, 'passwd') !== false || strpos($filename, 'config') !== false): ?>
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                    🚨 FILE SENSITIF!
                </span>
                <?php endif; ?>
            </div>
            
            <div class="bg-gray-900 text-green-400 rounded-lg p-6 font-mono text-sm overflow-x-auto">
                <pre class="whitespace-pre-wrap"><?= htmlspecialchars($fileContent) ?></pre>
            </div>

            <?php if (strpos($filename, '../') !== false || strpos($filename, 'etc/passwd') !== false): ?>
            <div class="mt-3 bg-red-50 border border-red-300 rounded p-3">
                <p class="text-sm text-red-800">
                    <strong>⚠️ Path Traversal Terdeteksi!</strong> 
                    File di luar direktori normal berhasil diakses. Ini adalah kerentanan LFI.
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Available Files List -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-3">📋 Daftar File Tersedia</h3>
            <div class="grid md:grid-cols-3 gap-3">
                <?php foreach (array_keys($simulatedFiles) as $file): ?>
                <div class="bg-white border rounded-lg p-3 hover:shadow-md transition">
                    <div class="flex items-center gap-2 mb-2">
                        <?php if (strpos($file, '../') !== false || strpos($file, 'passwd') !== false): ?>
                        <span class="text-red-500">🔒</span>
                        <?php else: ?>
                        <span class="text-green-500">📄</span>
                        <?php endif; ?>
                        <span class="font-mono text-sm"><?= htmlspecialchars($file) ?></span>
                    </div>
                    <form method="POST" class="inline">
                        <input type="hidden" name="filename" value="<?= htmlspecialchars($file) ?>">
                        <button type="submit" class="text-xs text-purple-600 hover:text-purple-800">
                            Buka →
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="?version=vulnerable&module=index" 
               class="block text-center text-gray-600 hover:text-gray-800 text-sm">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Analysis Section -->
    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-6 mt-6">
        <h3 class="font-bold text-yellow-800 mb-3">📊 Analisis Kerentanan</h3>
        <div class="text-sm text-yellow-900 space-y-2">
            <p><strong>Parameter Rentan:</strong></p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Input 'filename' - tidak ada validasi path</li>
                <li>Karakter "../" diperbolehkan (path traversal)</li>
                <li>Tidak ada whitelist file yang boleh diakses</li>
            </ul>
            <p class="mt-3"><strong>Flow Serangan LFI:</strong></p>
            <ol class="list-decimal list-inside space-y-1 ml-2">
                <li>Attacker input filename dengan "../"</li>
                <li>Sistem tidak validasi path</li>
                <li>Path traversal naik ke direktori parent</li>
                <li>File sensitif berhasil diakses (config, passwd)</li>
                <li>Data credentials atau sistem information ter-expose</li>
            </ol>
        </div>
    </div>
</div>