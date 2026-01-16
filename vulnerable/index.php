<?php
// Dashboard untuk versi vulnerable
?>

<div class="bg-white rounded-lg shadow-lg p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-red-700">
            🔓 Dashboard Versi Vulnerable
        </h2>
        <span class="bg-red-100 text-red-700 px-4 py-2 rounded-full text-sm font-semibold">
            ⚠️ TIDAK AMAN
        </span>
    </div>

    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8">
        <p class="text-red-800">
            <strong>Peringatan:</strong> Versi ini sengaja dibuat dengan kerentanan keamanan untuk tujuan pembelajaran dan pengujian.
        </p>
    </div>

    <!-- Module Cards -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <!-- Login Module -->
        <div class="border-2 border-red-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="text-5xl mb-4 text-center">🔑</div>
            <h3 class="text-xl font-bold text-red-700 mb-3 text-center">Login Module</h3>
            <div class="bg-red-50 rounded p-3 mb-4 text-sm">
                <p class="font-semibold text-red-900 mb-2">Kerentanan:</p>
                <ul class="space-y-1 text-red-700">
                    <li>❌ Brute Force Attack</li>
                    <li>❌ Weak Password</li>
                    <li>❌ No Rate Limiting</li>
                </ul>
            </div>
            <a href="?version=vulnerable&module=login" 
               class="block text-center bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                Buka Modul →
            </a>
        </div>

        <!-- Comment Module -->
        <div class="border-2 border-orange-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="text-5xl mb-4 text-center">💬</div>
            <h3 class="text-xl font-bold text-orange-700 mb-3 text-center">Comment Module</h3>
            <div class="bg-orange-50 rounded p-3 mb-4 text-sm">
                <p class="font-semibold text-orange-900 mb-2">Kerentanan:</p>
                <ul class="space-y-1 text-orange-700">
                    <li>❌ XSS Attack</li>
                    <li>❌ No CSRF Protection</li>
                    <li>❌ No Input Sanitization</li>
                </ul>
            </div>
            <a href="?version=vulnerable&module=comment" 
               class="block text-center bg-orange-600 text-white py-2 rounded hover:bg-orange-700 transition">
                Buka Modul →
            </a>
        </div>

        <!-- File Viewer Module -->
        <div class="border-2 border-purple-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="text-5xl mb-4 text-center">📁</div>
            <h3 class="text-xl font-bold text-purple-700 mb-3 text-center">File Viewer Module</h3>
            <div class="bg-purple-50 rounded p-3 mb-4 text-sm">
                <p class="font-semibold text-purple-900 mb-2">Kerentanan:</p>
                <ul class="space-y-1 text-purple-700">
                    <li>❌ LFI (Local File Inclusion)</li>
                    <li>❌ Path Traversal</li>
                    <li>❌ No Path Validation</li>
                </ul>
            </div>
            <a href="?version=vulnerable&module=fileviewer" 
               class="block text-center bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition">
                Buka Modul →
            </a>
        </div>
    </div>

    <!-- Testing Guide -->
    <div class="bg-gray-50 rounded-lg p-6 border border-gray-300">
        <h3 class="font-bold text-lg mb-4 text-gray-800">🧪 Panduan Testing</h3>
        <div class="space-y-3 text-sm">
            <div class="border-l-4 border-red-500 pl-4">
                <p class="font-semibold text-red-700">Login Module:</p>
                <p class="text-gray-600">Coba login berkali-kali dengan password salah, tidak ada pembatasan percobaan</p>
            </div>
            <div class="border-l-4 border-orange-500 pl-4">
                <p class="font-semibold text-orange-700">Comment Module:</p>
                <p class="text-gray-600">Input: <code class="bg-gray-200 px-1">&lt;script&gt;alert('XSS')&lt;/script&gt;</code> untuk test XSS</p>
            </div>
            <div class="border-l-4 border-purple-500 pl-4">
                <p class="font-semibold text-purple-700">File Viewer Module:</p>
                <p class="text-gray-600">Input: <code class="bg-gray-200 px-1">../config.txt</code> untuk test path traversal</p>
            </div>
        </div>
    </div>
</div>