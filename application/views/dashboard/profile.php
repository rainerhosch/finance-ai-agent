<?php $this->load->view('layout/dashboard_header'); ?>

<div class="max-w-2xl mx-auto">
    <!-- Profile Completion Alert -->
    <?php if (!$user_detail->profile_completed): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-6 h-6 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
            <div>
                <h4 class="font-semibold text-yellow-800">Lengkapi Profil Anda</h4>
                <p class="text-yellow-700 text-sm">Mohon lengkapi data profil untuk menggunakan semua fitur.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Header with Avatar -->
        <div class="gradient-bg p-8 text-center">
            <?php if (!empty($user_detail->avatar)): ?>
                <img src="<?= base_url($user_detail->avatar) ?>" alt="Avatar"
                    class="w-24 h-24 rounded-full mx-auto border-4 border-white shadow-lg">
            <?php else: ?>
                <div
                    class="w-24 h-24 bg-white text-primary-600 rounded-full mx-auto flex items-center justify-center text-3xl font-bold shadow-lg">
                    <?= strtoupper(substr($user_detail->name ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <h2 class="text-white text-xl font-semibold mt-4">
                <?= $user_detail->name ?: 'User' ?>
            </h2>
            <p class="text-white/80">
                <?= $user_detail->email ?>
            </p>
        </div>

        <!-- Form -->
        <form action="<?= site_url('dashboard/profile/update') ?>" method="POST" class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="<?= set_value('name', $user_detail->name) ?>" required
                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input type="email" value="<?= $user_detail->email ?>" disabled
                    class="w-full px-4 py-3 border rounded-xl bg-slate-50 text-slate-500">
                <p class="text-xs text-slate-500 mt-1">Email tidak dapat diubah (dari akun Google)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nomor Telepon</label>
                <input type="tel" name="phone" value="<?= set_value('phone', $user_detail->phone) ?>" required
                    placeholder="08xxxxxxxxxx"
                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Telegram User ID</label>
                <input type="text" name="telegram_user_id"
                    value="<?= set_value('telegram_user_id', $user_detail->telegram_user_id) ?>"
                    placeholder="Opsional - untuk menghubungkan bot"
                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <p class="text-xs text-slate-500 mt-1">Dapatkan ID Anda dengan mengirim /start ke @userinfobot di
                    Telegram</p>
            </div>

            <button type="submit"
                class="w-full gradient-bg text-white py-3 rounded-xl font-semibold hover:opacity-90 transition">
                Simpan Profil
            </button>
        </form>
    </div>

    <!-- Account Info -->
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Informasi Akun</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-500">Terdaftar sejak</span>
                <span class="text-slate-800">
                    <?= date('d M Y', strtotime($user_detail->created_at)) ?>
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Status profil</span>
                <?php if ($user_detail->profile_completed): ?>
                    <span class="text-green-600 font-medium">✓ Lengkap</span>
                <?php else: ?>
                    <span class="text-yellow-600 font-medium">Belum lengkap</span>
                <?php endif; ?>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Telegram</span>
                <?php if ($user_detail->telegram_user_id): ?>
                    <span class="text-green-600 font-medium">✓ Terhubung</span>
                <?php else: ?>
                    <span class="text-slate-400">Belum terhubung</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/dashboard_footer'); ?>