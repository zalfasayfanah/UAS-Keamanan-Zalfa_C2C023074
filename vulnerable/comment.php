<?php
// VULNERABLE COMMENT MODULE
// Kerentanan: XSS, No CSRF Protection, No Input Sanitization

// Initialize comments array in session
if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [];
}

$message = '';

// VULNERABILITY: No CSRF token validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'] ?? '';
    $username = $_SESSION['username'] ?? 'Anonymous';
    
    // VULNERABILITY: No input sanitization - XSS possible
    if (!empty(trim($comment))) {
        $_SESSION['comments'][] = [
            'id' => time() . rand(1000, 9999),
            'text' => $comment, // Stored without sanitization
            'user' => $username,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $message = "Komentar berhasil ditambahkan!";
    }
}

// Delete comment
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $_SESSION['comments'] = array_filter($_SESSION['comments'], function($c) use ($deleteId) {
        return $c['id'] != $deleteId;
    });
    $_SESSION['comments'] = array_values($_SESSION['comments']);
    header('Location: ?version=vulnerable&module=comment');
    exit;
}
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">💬</div>
            <h2 class="text-3xl font-bold text-orange-700">Comment Module</h2>
            <p class="text-sm text-gray-600 mt-2">Versi Vulnerable</p>
        </div>

        <!-- Vulnerability Warning -->
        <div class="bg-orange-50 border border-orange-300 rounded-lg p-4 mb-6">
            <p class="text-sm font-semibold text-orange-800 mb-2">⚠️ Kerentanan yang Ada:</p>
            <ul class="text-xs text-orange-700 space-y-1">
                <li>• Tidak ada sanitasi input (XSS)</li>
                <li>• Tidak ada CSRF token</li>
                <li>• HTML/JavaScript langsung di-render</li>
                <li>• Tidak ada validasi content-type</li>
            </ul>
        </div>

        <!-- Success Message -->
        <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <!-- Comment Form -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4">✍️ Tulis Komentar</h3>
            <form method="POST" class="space-y-4">
                <!-- VULNERABILITY: No CSRF token -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Komentar Anda:
                    </label>
                    <textarea name="comment" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                              placeholder="Tulis komentar... (coba input XSS payload untuk testing)"
                              required></textarea>
                </div>

                <button type="submit" 
                        class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition font-semibold">
                    Kirim Komentar
                </button>
            </form>

            <!-- XSS Testing Hints -->
            <div class="mt-4 bg-gray-100 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-700 mb-2">💡 Payload untuk Testing XSS:</p>
                <div class="text-xs text-gray-600 space-y-1 font-mono">
                    <p class="bg-white p-2 rounded">&lt;script&gt;alert('XSS Test')&lt;/script&gt;</p>
                    <p class="bg-white p-2 rounded">&lt;img src=x onerror="alert('XSS')"&gt;</p>
                    <p class="bg-white p-2 rounded">&lt;b&gt;Bold Text&lt;/b&gt; - Test HTML rendering</p>
                    <p class="bg-white p-2 rounded">&lt;h1&gt;Large Header&lt;/h1&gt;</p>
                </div>
            </div>
        </div>

        <!-- Comments List -->
        <div>
            <h3 class="font-semibold text-lg mb-4">💭 Daftar Komentar (<?= count($_SESSION['comments']) ?>)</h3>
            
            <?php if (empty($_SESSION['comments'])): ?>
                <div class="text-center py-8 text-gray-500">
                    <p class="text-4xl mb-2">📭</p>
                    <p>Belum ada komentar. Jadilah yang pertama!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_reverse($_SESSION['comments']) as $comment): ?>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-800">
                                    <?= htmlspecialchars($comment['user']) ?>
                                </span>
                                <span class="text-xs text-gray-500">
                                    <?= htmlspecialchars($comment['timestamp']) ?>
                                </span>
                            </div>
                            <a href="?version=vulnerable&module=comment&delete=<?= $comment['id'] ?>" 
                               class="text-red-600 hover:text-red-800 text-sm"
                               onclick="return confirm('Hapus komentar ini?')">
                                🗑️ Hapus
                            </a>
                        </div>
                        
                        <!-- VULNERABILITY: Output tidak di-escape - XSS! -->
                        <div class="text-gray-700 mt-2">
                            <?= $comment['text'] ?> 
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
                <li>Input 'comment' - tidak ada sanitasi</li>
                <li>Output rendering - langsung tampilkan HTML</li>
                <li>Tidak ada CSRF token di form</li>
            </ul>
            <p class="mt-3"><strong>Flow Serangan XSS:</strong></p>
            <ol class="list-decimal list-inside space-y-1 ml-2">
                <li>Attacker input script tag di komentar</li>
                <li>Sistem simpan tanpa sanitasi</li>
                <li>Saat di-render, script dieksekusi</li>
                <li>Browser victim menjalankan JavaScript attacker</li>
                <li>Cookie/session bisa dicuri, atau redirect</li>
            </ol>
        </div>
    </div>
</div>