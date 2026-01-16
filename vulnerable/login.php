<?php
// VULNERABLE LOGIN MODULE
// Kerentanan: Brute Force, Weak Password, No Rate Limiting

$error = '';
$success = '';
$attempts = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // VULNERABILITY 1: Increment attempts tanpa batas (No Rate Limiting)
    $_SESSION['login_attempts'] = ++$attempts;
    
    // VULNERABILITY 2: Hardcoded credentials dengan weak password
    // VULNERABILITY 3: Password tidak di-hash
    if ($username === 'admin' && $password === '123456') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $success = "Login berhasil! Selamat datang, Admin!";
    } elseif ($username === 'user' && $password === 'password') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $success = "Login berhasil! Selamat datang, User!";
    } else {
        // VULNERABILITY 4: Informative error message (Username Enumeration)
        $error = "Login gagal! Percobaan ke-{$attempts}. Username atau password salah.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ?version=vulnerable&module=login');
    exit;
}
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">🔑</div>
            <h2 class="text-3xl font-bold text-red-700">Login Module</h2>
            <p class="text-sm text-gray-600 mt-2">Versi Vulnerable</p>
        </div>

        <!-- Vulnerability Warning -->
        <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <p class="text-sm font-semibold text-red-800 mb-2">⚠️ Kerentanan yang Ada:</p>
            <ul class="text-xs text-red-700 space-y-1">
                <li>• Tidak ada rate limiting (brute force)</li>
                <li>• Password lemah (123456, password)</li>
                <li>• Password tidak di-hash</li>
                <li>• Informative error messages</li>
            </ul>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
            <div class="mt-3">
                <a href="?version=vulnerable&module=login&logout=1" 
                   class="text-sm underline hover:text-green-900">
                    Logout
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Username
                </label>
                <input type="text" 
                       name="username" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       placeholder="Coba: admin atau user"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input type="password" 
                       name="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       placeholder="Coba: 123456 atau password"
                       required>
            </div>

            <button type="submit" 
                    class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                Login
            </button>
        </form>

        <!-- Testing Hints -->
        <div class="mt-6 bg-gray-100 rounded-lg p-4">
            <p class="text-xs font-semibold text-gray-700 mb-2">💡 Hints untuk Testing:</p>
            <div class="text-xs text-gray-600 space-y-1">
                <p>• <strong>Username:</strong> admin | <strong>Password:</strong> 123456</p>
                <p>• <strong>Username:</strong> user | <strong>Password:</strong> password</p>
                <p>• Total percobaan login saat ini: <strong><?= $attempts ?></strong></p>
                <p class="text-red-600 font-semibold mt-2">⚠️ Tidak ada pembatasan percobaan login!</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Back Button -->
        <div class="mt-6">
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
                <li>Input username & password (tidak ada validasi kuat)</li>
                <li>Session login_attempts (tidak ada reset atau limit)</li>
                <li>Tidak ada delay antar percobaan</li>
            </ul>
            <p class="mt-3"><strong>Flow Serangan:</strong></p>
            <ol class="list-decimal list-inside space-y-1 ml-2">
                <li>Attacker mencoba berbagai kombinasi password</li>
                <li>Sistem tidak membatasi jumlah percobaan</li>
                <li>Error message membantu attacker</li>
                <li>Akhirnya password lemah berhasil ditebak</li>
            </ol>
        </div>
    </div>
</div>