<?php $this->load->view('layout/main_header'); ?>

<div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 via-white to-accent-50 py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo -->
        <div class="text-center">
            <div class="inline-flex items-center space-x-2 mb-4">
                <div class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-wallet text-white text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-slate-800">incatat.id</span>
            </div>
            <h2 class="text-3xl font-bold text-slate-800">Masuk ke Akun</h2>
            <p class="text-slate-500 mt-2">Kelola keuangan Anda dengan mudah</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                <i class="fa-solid fa-check-circle mr-2"></i>
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Login Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-100">
            <!-- Email/Password Form -->
            <form action="<?= site_url('login/password') ?>" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" required
                            class="w-full pl-12 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                            placeholder="nama@email.com" value="<?= set_value('email') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                            class="w-full pl-12 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                            placeholder="Masukkan password">
                    </div>
                </div>

                <button type="submit"
                    class="w-full gradient-bg text-white py-3 rounded-xl font-semibold hover:opacity-90 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Masuk
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-slate-500">atau</span>
                </div>
            </div>

            <!-- Google Login -->
            <a href="<?= site_url('login/google') ?>"
                class="w-full bg-white border-2 border-slate-200 text-slate-700 py-3 rounded-xl font-semibold hover:bg-slate-50 transition flex items-center justify-center gap-3">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        fill="#4285F4" />
                    <path
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        fill="#34A853" />
                    <path
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        fill="#FBBC05" />
                    <path
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        fill="#EA4335" />
                </svg>
                Masuk dengan Google
            </a>
        </div>

        <!-- Footer -->
        <p class="text-center text-slate-500 text-sm">
            Belum punya akun?
            <a href="<?= site_url('login/google') ?>" class="text-primary-600 font-medium hover:underline">
                Daftar dengan Google
            </a>
        </p>
    </div>
</div>

<?php $this->load->view('layout/main_footer'); ?>