<?php
// SECURE LOGIN MODULE
// Mitigasi: Rate Limiting, Password Hashing, Account Lockout

$error = '';
$success = '';

// Initialize security tracking
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = 0;
}

// SECURITY CONTROL 1: Rate Limiting & Account Lockout
$max_attempts = 5;
$lockout_duration = 900; // 15 minutes in seconds
$is_locked = false;

if ($_SESSION['login_attempts'] >= $max_attempts) {
    $time_passed = time() - $_SESSION['lockout_time'];
    if ($time_passed < $lockout_duration) {
        $is_locked = true;
        $remaining = ceil(($lockout_duration - $time_passed) / 60);
        $error = "Account terkunci. Coba lagi dalam {$remaining} menit.";
    } else {
        // Reset after lockout period
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lockout_time'] = 0;
    }
}

// SECURITY CONTROL 2: Strong Password with Hashing
$users = [
    'admin' => [
        'password' => password_hash('Admin@123!Strong', PASSWORD_BCRYPT),
        'name' => 'Administrator'
    ],
    'user' => [
        'password' => password_hash('User@2024!Secure', PASSWORD_BCRYPT),
        'name' => 'Regular User'
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // SECURITY CONTROL 3: Input Validation
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi.";
    } else {
        // Check if user exists and verify password
        if (isset($users[$username]) && password_verify($password, $users[$username]['password'])) {
            // Success - Reset attempts
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_name'] = $users[$username]['name'];
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lockout_time'] = 0;
            
            // SECURITY CONTROL 4: Session Regeneration
            session_regenerate_id(true);
            
            $success = "Login berhasil! Selamat datang, " . htmlspecialchars($users[$username]['name']) . "!";
        } else {
            // Failed login
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] == 1) {
                $_SESSION['lockout_time'] = time();
            }
            
            $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
            
            // SECURITY CONTROL 5: Generic Error Message (no username enumeration)
            if ($remaining_attempts > 0) {
                $error = "Login gagal. Anda memiliki {$remaining_attempts} percobaan lagi.";
            } else {
                $_SESSION['lockout_time'] = time();
                $error = "Account terkunci selama 15 menit karena terlalu banyak percobaan gagal.";
                $is_locked = true;
            }
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ?version=secure&module=login');
    exit;
}
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">🔐</div>
            <h2 class="text-3xl font-bold text-green-700">Login Module</h2>
            <p class="text-sm text-gray-600 mt-2">Versi Secure</p>
        </div>

        <!-- Security Features Badge -->
        <div class="bg-green-50 border border-green-300 rounded-lg p-4 mb-6">
            <p class="text-sm font-semibold text-green-800 mb-2">✅ Fitur Keamanan Aktif:</p>
            <ul class="text-xs text-green-700 space-y-1">
                <li>• Rate limiting (max <?= $max_attempts ?> percobaan)</li>
                <li>• Password hashing dengan bcrypt</li>
                <li>• Account lockout (15 menit)</li>
                <li>• Generic error messages</li>
                <li>• Session regeneration</li>
            </ul>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center mb-2">
                <span class="text-2xl mr-2">✅</span>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
            <div class="mt-3 flex gap-2">
                <a href="?version=secure&module=index" 
                   class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                    Dashboard
                </a>
                <a href="?version=secure&module=login&logout=1" 
                   class="text-sm bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700">
                    Logout
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <span class="text-2xl mr-2">⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Login Attempts Status -->
        <?php if ($_SESSION['login_attempts'] > 0 && !$is_locked && !$success): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4 text-sm">
            <div class="flex items-center">
                <span class="mr-2">⚡</span>
                <span>Percobaan login: <?= $_SESSION['login_attempts'] ?> / <?= $max_attempts ?></span>
            </div>
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
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       placeholder="Masukkan username"
                       <?= $is_locked ? 'disabled' : '' ?>
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input type="password" 
                       name="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       placeholder="Masukkan password"
                       <?= $is_locked ? 'disabled' : '' ?>
                       required>
            </div>

            <button type="submit" 
                    <?= $is_locked ? 'disabled' : '' ?>
                    class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold <?= $is_locked ? 'opacity-50 cursor-not-allowed' : '' ?>">
                <?= $is_locked ? '🔒 Account Terkunci' : 'Login' ?>
            </button>
        </form>

        <!-- Password Requirements Info -->
        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <p class="text-xs font-semibold text-blue-700 mb-2">🔑 Password Requirements (Strong):</p>
            <div class="text-xs text-blue-600 space-y-1">
                <p>• Minimal 8 karakter</p>
                <p>• Kombinasi huruf besar & kecil</p>
                <p>• Minimal 1 angka</p>
                <p>• Minimal 1 karakter special (!@#$%)</p>
            </div>
        </div>

        <!-- Testing Credentials -->
        <div class="mt-4 bg-gray-100 rounded-lg p-4">
            <p class="text-xs font-semibold text-gray-700 mb-2">💡 Kredensial untuk Testing:</p>
            <div class="text-xs text-gray-600 space-y-1">
                <p>• <strong>Username:</strong> admin | <strong>Password:</strong> Admin@123!Strong</p>
                <p>• <strong>Username:</strong> user | <strong>Password:</strong> User@2024!Secure</p>
                <p class="text-green-600 font-semibold mt-2">✅ Password sudah di-hash dengan bcrypt</p>
                <p class="text-yellow-700 font-semibold">⚡ Coba login salah 5x untuk test lockout</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Back Button -->
        <div class="mt-6">
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
                <p class="font-semibold mb-1">1. Rate Limiting Implementation:</p>
                <pre class="bg-white p-2 rounded text-xs overflow-x-auto">if ($attempts >= 5) {
    if (time() - $lockout_time < 900) {
        die("Account locked for 15 minutes");
    }
}</pre>
            </div>
            
            <div>
                <p class="font-semibold mb-1">2. Password Hashing:</p>
                <pre class="bg-white p-2 rounded text-xs overflow-x-auto">$hashed = password_hash($password, PASSWORD_BCRYPT);
password_verify($input, $hashed);</pre>
            </div>

            <div>
                <p class="font-semibold mb-1">3. Generic Error Messages:</p>
                <p class="text-xs text-green-700">Tidak memberikan informasi apakah username valid atau tidak (mencegah username enumeration)</p>
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
                        <td class="px-3 py-2 font-semibold">Rate Limit</td>
                        <td class="px-3 py-2 text-red-600">❌ Unlimited</td>
                        <td class="px-3 py-2 text-green-600">✅ 5 attempts/15min</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">Password</td>
                        <td class="px-3 py-2 text-red-600">❌ Plaintext (123456)</td>
                        <td class="px-3 py-2 text-green-600">✅ Bcrypt hashed</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">Lockout</td>
                        <td class="px-3 py-2 text-red-600">❌ No lockout</td>
                        <td class="px-3 py-2 text-green-600">✅ 15 min lockout</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">Error Message</td>
                        <td class="px-3 py-2 text-red-600">❌ Informative</td>
                        <td class="px-3 py-2 text-green-600">✅ Generic</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>x