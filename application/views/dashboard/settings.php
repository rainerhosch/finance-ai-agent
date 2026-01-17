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

    <!-- Telegram Accounts Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="fa-brands fa-telegram text-blue-500 mr-2"></i>Akun Telegram
                    </h3>
                    <p class="text-slate-500 text-sm mt-1">Hubungkan akun Telegram untuk mencatat transaksi via chat</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <?php if (!empty($telegram_accounts)): ?>
                <!-- List of connected accounts -->
                <div class="space-y-3">
                    <?php foreach ($telegram_accounts as $account): ?>
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fa-brands fa-telegram text-white text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-green-800">
                                        <?= htmlspecialchars($account->telegram_first_name ?: $account->telegram_username ?: 'Telegram') ?>
                                        <?php if ($account->is_primary): ?>
                                            <span
                                                class="ml-2 text-xs bg-green-200 text-green-700 px-2 py-0.5 rounded-full">Utama</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm text-green-600">
                                        <?php if ($account->telegram_username): ?>
                                            @<?= htmlspecialchars($account->telegram_username) ?> ·
                                        <?php endif; ?>
                                        <?php if ($account->label): ?>
                                            <?= htmlspecialchars($account->label) ?>
                                        <?php else: ?>
                                            ID: <?= $account->telegram_user_id ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <form action="<?= site_url('dashboard/remove_telegram/' . $account->id) ?>" method="POST"
                                class="inline">
                                <button type="submit" onclick="return confirm('Yakin ingin melepas akun Telegram ini?')"
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="pt-2 border-t">
                    <p class="text-sm text-slate-500 mb-3">
                        <i class="fa-solid fa-plus-circle mr-1"></i>Tambah akun Telegram lain? <br>
                        Buka <a href="https://t.me/incatatbot" target="_blank" class="text-blue-500">@incatatbot</a> dan
                        ketik
                        <i>/hubungkan bussinesmail@mail.com</i>
                    </p>
                </div>
            <?php else: ?>
                <!-- Instructions to connect -->
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                        <span
                            class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        <div>
                            <div class="font-medium text-blue-800">Buka Telegram Bot</div>
                            <div class="text-sm text-blue-600">Klik disini <i class="fa-brands fa-telegram mr-1"></i><a
                                    href="https://t.me/incatatbot" target="_blank">incatatbot</a></div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                        <span
                            class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        <div>
                            <div class="font-medium text-blue-800">Kirim perintah /start</div>
                            <div class="text-sm text-blue-600">Bot akan meminta Anda untuk login</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                        <span
                            class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                        <div>
                            <div class="font-medium text-blue-800">Masukkan email Anda</div>
                            <div class="text-sm text-blue-600">Gunakan email: <strong><?= $user_detail->email ?></strong>
                            </div>
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