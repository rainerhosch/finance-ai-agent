<?php $this->load->view('layout/dashboard_header'); ?>

<div class="max-w-2xl mx-auto space-y-6">
    <?php if (isset($business) && $business && $user_detail->role === 'owner'): ?>
        <!-- Business Settings Section (Owner Only) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-slate-800">Info Bisnis</h3>
                <p class="text-slate-500 text-sm mt-1">Kelola informasi bisnis/toko Anda</p>
            </div>

            <form action="<?= site_url('dashboard/update_business') ?>" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Bisnis</label>
                    <input type="text" name="business_name" value="<?= htmlspecialchars($business->name) ?>" required
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                        placeholder="Nama toko/usaha Anda">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Telepon Bisnis</label>
                    <input type="text" name="business_phone" value="<?= htmlspecialchars($business->phone ?? '') ?>"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                        placeholder="Nomor telepon bisnis">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                    <textarea name="business_address" rows="2"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                        placeholder="Alamat bisnis"><?= htmlspecialchars($business->address ?? '') ?></textarea>
                </div>

                <button type="submit"
                    class="px-6 py-2.5 gradient-bg text-white rounded-xl font-medium hover:opacity-90 transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- API Token Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-slate-800">API Token</h3>
            <p class="text-slate-500 text-sm mt-1">Token ini digunakan untuk menghubungkan akun dengan Telegram Bot</p>
        </div>

        <div class="p-6">
            <?php if ($user_detail->api_token): ?>
                <div class="relative">
                    <input type="text" id="api-token" value="<?= $user_detail->api_token ?>" readonly
                        class="w-full px-4 py-3 pr-24 border rounded-xl bg-slate-50 font-mono text-sm">
                    <button onclick="copyToken()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-primary-500 text-white text-sm rounded-lg hover:bg-primary-600 transition">
                        Copy
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    ⚠️ Jangan bagikan token ini kepada siapapun!
                </p>
            <?php else: ?>
                <p class="text-slate-500">Token belum tersedia</p>
            <?php endif; ?>

            <form action="<?= site_url('dashboard/regenerate_token') ?>" method="POST" class="mt-4">
                <button type="submit"
                    onclick="return confirm('Yakin ingin membuat token baru? Token lama tidak akan berfungsi lagi.')"
                    class="px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition text-sm font-medium">
                    Regenerate Token
                </button>
            </form>
        </div>
    </div>

    <!-- Telegram Bot Connection -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-slate-800">Telegram Bot</h3>
            <p class="text-slate-500 text-sm mt-1">Hubungkan akun dengan Telegram Bot untuk mencatat transaksi via chat
            </p>
        </div>

        <div class="p-6">
            <?php if ($user_detail->telegram_user_id): ?>
                <div class="flex items-center gap-3 p-4 bg-green-50 rounded-xl">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold text-green-800">Terhubung</div>
                        <div class="text-sm text-green-600">Telegram ID:
                            <?= $user_detail->telegram_user_id ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                        <span class="text-2xl">1️⃣</span>
                        <div>
                            <div class="font-medium text-blue-800">Buka Telegram Bot</div>
                            <div class="text-sm text-blue-600">Cari dan buka @incatat_bot di Telegram</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                        <span class="text-2xl">2️⃣</span>
                        <div>
                            <div class="font-medium text-blue-800">Kirim perintah /start</div>
                            <div class="text-sm text-blue-600">Bot akan meminta Anda untuk login</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                        <span class="text-2xl">3️⃣</span>
                        <div>
                            <div class="font-medium text-blue-800">Masukkan email Anda</div>
                            <div class="text-sm text-blue-600">Gunakan email: <strong>
                                    <?= $user_detail->email ?>
                                </strong></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
        <div class="p-6 border-b border-red-200 bg-red-50">
            <h3 class="text-lg font-semibold text-red-800">Zona Berbahaya</h3>
            <p class="text-red-600 text-sm mt-1">Tindakan ini tidak dapat dibatalkan</p>
        </div>

        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium text-slate-800">Hapus Semua Data</div>
                    <div class="text-sm text-slate-500">Hapus semua transaksi dan data Anda</div>
                </div>
                <button
                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition font-medium text-sm"
                    disabled>
                    Coming Soon
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToken() {
        const input = document.getElementById('api-token');
        input.select();
        document.execCommand('copy');

        // Show feedback
        const btn = event.target;
        const originalText = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.remove('bg-primary-500');
        btn.classList.add('bg-green-500');

        setTimeout(() => {
            btn.textContent = originalText;
            btn.classList.remove('bg-green-500');
            btn.classList.add('bg-primary-500');
        }, 2000);
    }
</script>

<?php $this->load->view('layout/dashboard_footer'); ?>