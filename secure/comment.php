<?php
// SECURE COMMENT MODULE
// Mitigasi: Input Sanitization, CSRF Token, Output Encoding, Content Length Limit

// Initialize comments array in session
if (!isset($_SESSION['comments_secure'])) {
    $_SESSION['comments_secure'] = [];
}

// SECURITY CONTROL 1: Generate CSRF Token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$error = '';

// SECURITY CONTROL 2: CSRF Token Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token. Request ditolak untuk keamanan.";
    } else {
        $comment = $_POST['comment'] ?? '';
        $username = $_SESSION['username'] ?? 'Anonymous';
        
        // SECURITY CONTROL 3: Input Validation - Length Limit
        $max_length = 500;
        if (empty(trim($comment))) {
            $error = "Komentar tidak boleh kosong.";
        } elseif (strlen($comment) > $max_length) {
            $error = "Komentar terlalu panjang. Maksimal {$max_length} karakter.";
        } else {
            // SECURITY CONTROL 4: Input Sanitization (strip tags, keep safe content)
            $sanitized_comment = strip_tags($comment);
            
            // SECURITY CONTROL 5: Additional XSS prevention
            $sanitized_comment = htmlspecialchars($sanitized_comment, ENT_QUOTES, 'UTF-8');
            
            // SECURITY CONTROL 6: Generate UUID for comment
            $comment_id = uniqid('comment_', true);
            
            $_SESSION['comments_secure'][] = [
                'id' => $comment_id,
                'text' => $sanitized_comment, // Stored sanitized
                'user' => htmlspecialchars($username, ENT_QUOTES, 'UTF-8'),
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $message = "Komentar berhasil ditambahkan dengan aman!";
            
            // Regenerate CSRF token after successful submission
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}

// Delete comment (with CSRF protection)
if (isset($_GET['delete']) && isset($_GET['csrf'])) {
    if (hash_equals($_SESSION['csrf_token'], $_GET['csrf'])) {
        $deleteId = $_GET['delete'];
        $_SESSION['comments_secure'] = array_filter($_SESSION['comments_secure'], function($c) use ($deleteId) {
            return $c['id'] != $deleteId;
        });
        $_SESSION['comments_secure'] = array_values($_SESSION['comments_secure']);
        
        // Regenerate token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        header('Location: ?version=secure&module=comment');
        exit;
    } else {
        $error = "Invalid CSRF token untuk delete operation.";
    }
}
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">💬</div>
            <h2 class="text-3xl font-bold text-blue-700">Comment Module</h2>
            <p class="text-sm text-gray-600 mt-2">Versi Secure</p>
        </div>

        <!-- Security Features Badge -->
        <div class="bg-blue-50 border border-blue-300 rounded-lg p-4 mb-6">
            <p class="text-sm font-semibold text-blue-800 mb-2">✅ Fitur Keamanan Aktif:</p>
            <ul class="text-xs text-blue-700 space-y-1">
                <li>• Input sanitization dengan htmlspecialchars()</li>
                <li>• CSRF token validation</li>
                <li>• Content length limit (<?= $max_length ?> chars)</li>
                <li>• Output encoding (XSS prevention)</li>
                <li>• UUID untuk identifikasi comment</li>
            </ul>
        </div>

        <!-- Success Message -->
        <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <span class="text-xl mr-2">✅</span>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <span class="text-xl mr-2">⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Comment Form -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4">✍️ Tulis Komentar</h3>
            <form method="POST" class="space-y-4">
                <!-- CSRF Token (Hidden Field) -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Komentar Anda:
                        <span class="text-gray-500 text-xs">(Max <?= $max_length ?> karakter)</span>
                    </label>
                    <textarea name="comment" 
                              rows="4"
                              maxlength="<?= $max_length ?>"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Tulis komentar Anda di sini... (HTML tags akan dihapus untuk keamanan)"
                              required></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        ℹ️ Catatan: Semua input akan disanitasi untuk mencegah XSS attack
                    </p>
                </div>

                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Kirim Komentar
                </button>
            </form>

            <!-- XSS Prevention Demo -->
            <div class="mt-4 bg-gray-100 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-700 mb-2">🛡️ Test XSS Prevention:</p>
                <div class="text-xs text-gray-600 space-y-2">
                    <p>Coba input payload berikut, dan lihat hasilnya disanitasi:</p>
                    <div class="bg-white p-2 rounded font-mono space-y-1">
                        <p>&lt;script&gt;alert('XSS')&lt;/script&gt;</p>
                        <p>&lt;img src=x onerror="alert('XSS')"&gt;</p>
                        <p>&lt;b&gt;Bold Text&lt;/b&gt;</p>
                    </div>
                    <p class="text-green-600 font-semibold">✅ Semua tag HTML akan di-strip dan di-escape</p>
                </div>
            </div>
        </div>

        <!-- Comments List -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg">💭 Daftar Komentar (<?= count($_SESSION['comments_secure']) ?>)</h3>
                <?php if (!empty($_SESSION['comments_secure'])): ?>
                <span class="text-xs text-green-600 bg-green-50 px-3 py-1 rounded-full">
                    ✅ Semua komentar telah disanitasi
                </span>
                <?php endif; ?>
            </div>
            
            <?php if (empty($_SESSION['comments_secure'])): ?>
                <div class="text-center py-8 text-gray-500">
                    <p class="text-4xl mb-2">📭</p>
                    <p>Belum ada komentar. Jadilah yang pertama!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_reverse($_SESSION['comments_secure']) as $comment): ?>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gradient-to-r from-blue-50 to-white hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-800">
                                    <?= $comment['user'] ?>
                                </span>
                                <span class="text-xs text-gray-500">
                                    <?= htmlspecialchars($comment['timestamp']) ?>
                                </span>
                            </div>
                            <a href="?version=secure&module=comment&delete=<?= urlencode($comment['id']) ?>&csrf=<?= urlencode($_SESSION['csrf_token']) ?>" 
                               class="text-red-600 hover:text-red-800 text-sm"
                               onclick="return confirm('Hapus komentar ini?')">
                                🗑️ Hapus
                            </a>
                        </div>
                        
                        <!-- SECURE OUTPUT: Already sanitized during input -->
                        <div class="text-gray-700 mt-2 bg-white p-3 rounded border-l-4 border-blue-400">
                            <?= $comment['text'] ?>
                        </div>
                        
                        <div class="mt-2 text-xs text-gray-400">
                            ID: <?= htmlspecialchars($comment['id']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
                <p class="font-semibold mb-1">1. CSRF Token Implementation:</p>
                <pre class="bg-white p-2 rounded text-xs overflow-x-auto">// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed");
}</pre>
            </div>
            
            <div>
                <p class="font-semibold mb-1">2. Input Sanitization:</p>
                <pre class="bg-white p-2 rounded text-xs overflow-x-auto">$clean = strip_tags($input);
$clean = htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');</pre>
            </div>

            <div>
                <p class="font-semibold mb-1">3. XSS Prevention:</p>
                <p class="text-xs text-green-700">Semua input di-sanitasi sebelum disimpan dan output sudah di-encode</p>
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
                        <td class="px-3 py-2 font-semibold">Input Sanitization</td>
                        <td class="px-3 py-2 text-red-600">❌ No sanitization</td>
                        <td class="px-3 py-2 text-green-600">✅ htmlspecialchars + strip_tags</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">CSRF Protection</td>
                        <td class="px-3 py-2 text-red-600">❌ No token</td>
                        <td class="px-3 py-2 text-green-600">✅ Token validation</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">Output Encoding</td>
                        <td class="px-3 py-2 text-red-600">❌ Raw output (XSS)</td>
                        <td class="px-3 py-2 text-green-600">✅ Encoded output</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold">Length Limit</td>
                        <td class="px-3 py-2 text-red-600">❌ No limit</td>
                        <td class="px-3 py-2 text-green-600">✅ 500 chars max</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>