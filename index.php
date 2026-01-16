<?php
session_start();

// Konstanta untuk path
define('BASE_PATH', __DIR__);
define('VULNERABLE_PATH', BASE_PATH . '/vulnerable');
define('SECURE_PATH', BASE_PATH . '/secure');

// Helper function untuk load view
function loadView($version, $module) {
    $path = ($version === 'vulnerable') ? VULNERABLE_PATH : SECURE_PATH;
    $file = $path . '/' . $module . '.php';
    
    if (file_exists($file)) {
        include $file;
    } else {
        echo "Module tidak ditemukan!";
    }
}

// Routing sederhana
$version = isset($_GET['version']) ? $_GET['version'] : 'home';
$module = isset($_GET['module']) ? $_GET['module'] : 'index';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAS Keamanan Data & Informasi - Zalfa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">UAS Keamanan Data & Informasi</h1>
                    <p class="text-sm opacity-90 mt-1">Praktikum - IF2350073 - Zalfa C2C023074</p>
                </div>
                <a href="index.php" class="bg-white text-purple-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    🏠 Home
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <?php
        if ($version === 'home') {
            // Homepage
            ?>
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Selamat Datang! 👋</h2>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">⚠️</span>
                        <div>
                            <h3 class="font-bold text-yellow-800 text-lg mb-2">Peringatan Penting</h3>
                            <p class="text-yellow-700">
                                Aplikasi ini dibuat untuk tujuan <strong>PEMBELAJARAN</strong> dan <strong>SIMULASI PENGUJIAN KEAMANAN</strong>. 
                                Versi vulnerable sengaja memiliki kerentanan untuk demonstrasi. Jangan gunakan di production!
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <!-- Vulnerable Version Card -->
                    <div class="border-2 border-red-300 rounded-lg p-6 hover:shadow-xl transition-shadow bg-red-50">
                        <div class="flex items-center mb-4">
                            <span class="text-4xl mr-3">🔓</span>
                            <h3 class="text-2xl font-bold text-red-700">Versi Vulnerable</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Aplikasi dengan kerentanan keamanan untuk pengujian dan pembelajaran.
                        </p>
                        <div class="space-y-2 mb-4 text-sm">
                            <p class="flex items-center text-red-600">
                                <span class="mr-2">❌</span> Login: Brute Force & Weak Password
                            </p>
                            <p class="flex items-center text-red-600">
                                <span class="mr-2">❌</span> Comment: XSS & No CSRF
                            </p>
                            <p class="flex items-center text-red-600">
                                <span class="mr-2">❌</span> File Viewer: LFI Vulnerability
                            </p>
                        </div>
                        <a href="?version=vulnerable&module=index" 
                           class="block text-center bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                            Buka Versi Vulnerable →
                        </a>
                    </div>

                    <!-- Secure Version Card -->
                    <div class="border-2 border-green-300 rounded-lg p-6 hover:shadow-xl transition-shadow bg-green-50">
                        <div class="flex items-center mb-4">
                            <span class="text-4xl mr-3">🔒</span>
                            <h3 class="text-2xl font-bold text-green-700">Versi Secure</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Aplikasi dengan implementasi kontrol keamanan yang proper.
                        </p>
                        <div class="space-y-2 mb-4 text-sm">
                            <p class="flex items-center text-green-600">
                                <span class="mr-2">✅</span> Login: Rate Limiting & Strong Password
                            </p>
                            <p class="flex items-center text-green-600">
                                <span class="mr-2">✅</span> Comment: Input Validation & CSRF Token
                            </p>
                            <p class="flex items-center text-green-600">
                                <span class="mr-2">✅</span> File Viewer: Path Validation & Whitelist
                            </p>
                        </div>
                        <a href="?version=secure&module=index" 
                           class="block text-center bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                            Buka Versi Secure →
                        </a>
                    </div>
                </div>

                <!-- Info Project -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h3 class="font-bold text-lg mb-3 text-gray-800">📋 Informasi Project</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><strong>Nama:</strong> Zalfa</p>
                            <p><strong>NIM:</strong> C2C023074</p>
                            <p><strong>Kelas:</strong> B</p>
                        </div>
                        <div>
                            <p><strong>Mata Kuliah:</strong> Keamanan Data & Informasi (Praktikum)</p>
                            <p><strong>Kode MK:</strong> IF2350073 - MK07</p>
                            <p><strong>Dosen:</strong> Dr. Dhendra Marutho, S.Kom., M.Kom</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } elseif ($version === 'vulnerable' || $version === 'secure') {
            loadView($version, $module);
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    Halaman tidak ditemukan!
                  </div>';
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12 py-6">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm">
                © 2025 UAS Keamanan Data & Informasi - Zalfa C2C023074
            </p>
            <p class="text-xs mt-2 text-gray-400">
                ⚠️ Aplikasi ini untuk tujuan pembelajaran - Jangan gunakan di production
            </p>
        </div>
    </footer>
</body>
</html>