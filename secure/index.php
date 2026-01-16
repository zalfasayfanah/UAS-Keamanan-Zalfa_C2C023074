<?php
// Dashboard untuk versi secure
?>

<div class="bg-white rounded-lg shadow-lg p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-green-700">
            🔒 Dashboard Versi Secure
        </h2>
        <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">
            ✅ AMAN
        </span>
    </div>

    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8">
        <p class="text-green-800">
            <strong>Informasi:</strong> Versi ini mengimplementasikan kontrol keamanan yang proper untuk mencegah serangan umum.
        </p>
    </div>

    <!-- Security Features Overview -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h3 class="font-bold text-blue-900 mb-4 text-lg">🛡️ Fitur Keamanan yang Diterapkan:</h3>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white p-4 rounded-lg">
                <div class="font-semibold text-blue-800 mb-2">✓ Input Validation</div>
                <p class="text-gray-600">Semua input divalidasi dan disanitasi</p>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="font-semibold text-blue-800 mb-2">✓ CSRF Protection</div>
                <p class="text-gray-600">Token validasi untuk setiap form</p>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="font-semibold text-blue-800 mb-2">✓ Rate Limiting</div>
                <p class="text-gray-600">Pembatasan percobaan login</p>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="font-semibold text-blue-800 mb-2">✓ Password Hashing</div>
                <p class="text-gray-600">Bcrypt untuk enkripsi password</p>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="font-semibold text-blue-800 mb-2">✓ Path Validation</div>
                <p class="text-gray-600">Whitelist untuk akses file</p>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="font-semibold text-blue-800 mb-2">✓ Output Encoding</div>
                <p class="text-gray-600">HTML escaping untuk XSS prevention</p>
            </div>
        </div>
    </div>

    <!-- Module Cards -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <!-- Login Module -->
        <div class="border-2 border-green-200 rounded-lg p-6 hover:shadow-lg transition-shadow bg-gradient-to-br from-green-50 to-white">
            <div class="text-5xl mb-4 text-center">🔐</div>
            <h3 class="text-xl font-bold text-green-700 mb-3 text-center">Login Module</h3>
            <div class="bg-green-50 rounded p-3 mb-4 text-sm">
                <p class="font-semibold text-green-900 mb-2">Mitigasi:</p>
                <ul class="space-y-1 text-green-700">
                    <li>✅ Rate Limiting (5 attempts/15min)</li>
                    <li>✅ Password Hashing (bcrypt)</li>
                    <li>✅ Account Lockout</li>
                    <li>✅ Generic Error Messages</li>
                </ul>
            </div>
            <a href="?version=secure&module=login" 
               class="block text-center bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
                Buka Modul →
            </a>
        </div>

        <!-- Comment Module -->
        <div class="border-2 border-blue-200 rounded-lg p-6 hover:shadow-lg transition-shadow bg-gradient-to-br from-blue-50 to-white">
            <div class="text-5xl mb-4 text-center">💬</div>
            <h3 class="text-xl font-bold text-blue-700 mb-3 text-center">Comment Module</h3>
            <div class="bg-blue-50 rounded p-3 mb-4 text-sm">
                <p class="font-semibold text-blue-900 mb-2">Mitigasi:</p>
                <ul class="space-y-1 text-blue-700">
                    <li>✅ Input Sanitization</li>
                    <li>✅ CSRF Token Validation</li>
                    <li>✅ Output Encoding (XSS Prevention)</li>
                    <li>✅ Content Length Limit</li>
                </ul>
            </div>
            <a href="?version=secure&module=comment" 
               class="block text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Buka Modul →
            </a>
        </div>

        <!-- File Viewer Module -->
        <div class="border-2 border-indigo-200 rounded-lg p-6 hover:shadow-lg transition-shadow bg-gradient-to-br from-indigo-50 to-white">
            <div class="text-5xl mb-4 text-center">📁</div>
            <h3 class="text-xl font-bold text-indigo-700 mb-3 text-center">File Viewer Module</h3>
            <div class="bg-indigo-50 rounded p-3 mb-4 text-sm">
                <p class="font-semibold text-indigo-900 mb-2">Mitigasi:</p>
                <ul class="space-y-1 text-indigo-700">
                    <li>✅ Whitelist File Validation</li>
                    <li>✅ Path Sanitization (basename)</li>
                    <li>✅ No Path Traversal</li>
                    <li>✅ Non-public File Storage</li>
                </ul>
            </div>
            <a href="?version=secure&module=fileviewer" 
               class="block text-center bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
                Buka Modul →
            </a>
        </div>
    </div>

    <!-- Comparison: Vulnerable vs Secure -->
    <div class="bg-gray-50 rounded-lg p-6 border border-gray-300">
        <h3 class="font-bold text-lg mb-4 text-gray-800">⚖️ Perbandingan: Vulnerable vs Secure</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left">Aspek</th>
                        <th class="px-4 py-2 text-left text-red-700">❌ Vulnerable</th>
                        <th class="px-4 py-2 text-left text-green-700">✅ Secure</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-2 font-semibold">Login</td>
                        <td class="px-4 py-2 text-red-600">No rate limit, weak password</td>
                        <td class="px-4 py-2 text-green-600">Rate limiting, password hashing</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-semibold">Comment</td>
                        <td class="px-4 py-2 text-red-600">XSS vulnerable, no CSRF</td>
                        <td class="px-4 py-2 text-green-600">Input sanitized, CSRF protected</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-semibold">File Viewer</td>
                        <td class="px-4 py-2 text-red-600">LFI vulnerable, path traversal</td>
                        <td class="px-4 py-2 text-green-600">Whitelist validation, no traversal</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Testing Guide for Secure Version -->
    <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-300 mt-6">
        <h3 class="font-bold text-lg mb-4 text-yellow-800">🧪 Panduan Testing Versi Secure</h3>
        <div class="space-y-3 text-sm">
            <div class="border-l-4 border-green-500 pl-4 bg-white p-3 rounded">
                <p class="font-semibold text-green-700">Login Module:</p>
                <p class="text-gray-600">Coba login salah 5 kali → Account akan ter-lock selama 15 menit</p>
            </div>
            <div class="border-l-4 border-blue-500 pl-4 bg-white p-3 rounded">
                <p class="font-semibold text-blue-700">Comment Module:</p>
                <p class="text-gray-600">Input XSS payload → Script akan di-escape dan ditampilkan sebagai text</p>
            </div>
            <div class="border-l-4 border-indigo-500 pl-4 bg-white p-3 rounded">
                <p class="font-semibold text-indigo-700">File Viewer Module:</p>
                <p class="text-gray-600">Input path traversal → Akses ditolak, hanya file dalam whitelist yang bisa dibuka</p>
            </div>
        </div>
    </div>
</div>