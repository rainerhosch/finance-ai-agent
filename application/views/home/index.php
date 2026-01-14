<?php $this->load->view('layout/main_header'); ?>

<!-- Navigation -->
<nav class="fixed w-full z-50 transition-all duration-300 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white text-xl">💰</span>
                </div>
                <span class="text-2xl font-bold gradient-text">incatat.id</span>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-slate-600 hover:text-slate-900 font-medium transition">Fitur</a>
                <a href="#how-it-works" class="text-slate-600 hover:text-slate-900 font-medium transition">Cara
                    Kerja</a>
                <a href="#pricing" class="text-slate-600 hover:text-slate-900 font-medium transition">Harga</a>
                <?php if ($is_logged_in): ?>
                    <a href="<?= site_url('dashboard') ?>"
                        class="gradient-bg text-white px-6 py-2.5 rounded-full font-semibold hover:opacity-90 transition shadow-lg">
                        Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('login') ?>"
                        class="gradient-bg text-white px-6 py-2.5 rounded-full font-semibold hover:opacity-90 transition shadow-lg">
                        Masuk dengan Google
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t shadow-lg">
        <div class="px-4 py-4 space-y-3">
            <a href="#features" class="block text-slate-600 hover:text-slate-900 font-medium py-2">Fitur</a>
            <a href="#how-it-works" class="block text-slate-600 hover:text-slate-900 font-medium py-2">Cara Kerja</a>
            <a href="#pricing" class="block text-slate-600 hover:text-slate-900 font-medium py-2">Harga</a>
            <?php if ($is_logged_in): ?>
                <a href="<?= site_url('dashboard') ?>"
                    class="block gradient-bg text-white px-6 py-3 rounded-full font-semibold text-center">
                    Dashboard
                </a>
            <?php else: ?>
                <a href="<?= site_url('login') ?>"
                    class="block gradient-bg text-white px-6 py-3 rounded-full font-semibold text-center">
                    Masuk dengan Google
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center overflow-hidden">
    <!-- Background gradients -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary-50 via-white to-accent-50"></div>
    <div class="absolute top-20 right-0 w-96 h-96 bg-primary-200/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 left-0 w-96 h-96 bg-accent-200/30 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-center lg:text-left">
                <div
                    class="inline-flex items-center px-4 py-2 rounded-full bg-primary-100 text-primary-700 text-sm font-medium mb-6">
                    <span class="mr-2">🚀</span> Powered by AI & Telegram Bot
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    Kelola Keuangan
                    <span class="gradient-text">Lebih Cerdas</span>
                    dengan AI
                </h1>

                <p class="text-xl text-slate-600 mb-8 max-w-lg mx-auto lg:mx-0">
                    Catat pemasukan dan pengeluaran hanya dengan chat di Telegram atau upload foto struk.
                    Semudah ngobrol dengan teman!
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="<?= site_url('login') ?>"
                        class="gradient-bg text-white px-8 py-4 rounded-full font-semibold text-lg hover:opacity-90 transition shadow-xl flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Daftar dengan Google
                    </a>
                    <a href="#how-it-works"
                        class="bg-white text-slate-700 px-8 py-4 rounded-full font-semibold text-lg border-2 border-slate-200 hover:border-primary-300 hover:text-primary-600 transition flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Lihat Demo
                    </a>
                </div>

                <!-- Trust badges -->
                <div class="mt-12 flex items-center gap-8 justify-center lg:justify-start text-slate-500 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>Gratis Selamanya</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>Aman & Terenkripsi</span>
                    </div>
                </div>
            </div>

            <!-- Right Content - Hero Image/Illustration -->
            <div class="relative animate-float">
                <div class="relative bg-white rounded-3xl shadow-2xl p-6 max-w-md mx-auto">
                    <!-- Mock Phone Screen -->
                    <div class="bg-slate-100 rounded-2xl p-4">
                        <!-- Chat Header -->
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200">
                            <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center">
                                <span class="text-white">🤖</span>
                            </div>
                            <div>
                                <div class="font-semibold">incatat Bot</div>
                                <div class="text-xs text-green-500">● Online</div>
                            </div>
                        </div>

                        <!-- Chat Messages -->
                        <div class="space-y-3 text-sm">
                            <div class="bg-primary-100 text-primary-800 p-3 rounded-2xl rounded-tl-none max-w-[80%]">
                                Halo! Apa yang ingin kamu catat hari ini? 💰
                            </div>
                            <div class="bg-white p-3 rounded-2xl rounded-tr-none max-w-[80%] ml-auto shadow-sm">
                                Baru beli makan siang 35rb
                            </div>
                            <div class="bg-primary-100 text-primary-800 p-3 rounded-2xl rounded-tl-none max-w-[80%]">
                                ✅ Tercatat pengeluaran Rp 35.000 untuk Makanan & Minuman
                            </div>
                            <div
                                class="bg-white p-3 rounded-2xl rounded-tr-none max-w-[80%] ml-auto shadow-sm flex items-center gap-2">
                                <span>📷</span> Struk belanja.jpg
                            </div>
                            <div class="bg-primary-100 text-primary-800 p-3 rounded-2xl rounded-tl-none max-w-[80%]">
                                🔍 Menganalisis struk...<br>
                                ✅ Ditemukan 5 item, total Rp 287.500
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating elements -->
                <div
                    class="absolute -top-4 -right-4 bg-green-500 text-white px-4 py-2 rounded-full text-sm font-medium shadow-lg animate-pulse-slow">
                    +Rp 5.000.000
                </div>
                <div
                    class="absolute -bottom-4 -left-4 bg-red-500 text-white px-4 py-2 rounded-full text-sm font-medium shadow-lg">
                    -Rp 287.500
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Fitur Unggulan</h2>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                Semua yang Anda butuhkan untuk mengelola keuangan dengan mudah dan cerdas
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div
                class="feature-card bg-gradient-to-br from-primary-50 to-white p-8 rounded-3xl border border-primary-100">
                <div class="w-14 h-14 gradient-bg rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg">
                    💬
                </div>
                <h3 class="text-xl font-bold mb-3">Chat untuk Catat</h3>
                <p class="text-slate-600">
                    Ketik pemasukan atau pengeluaran seperti ngobrol biasa. AI kami akan otomatis memahami dan
                    mencatatnya.
                </p>
            </div>

            <!-- Feature 2 -->
            <div
                class="feature-card bg-gradient-to-br from-accent-50 to-white p-8 rounded-3xl border border-accent-100">
                <div
                    class="w-14 h-14 bg-gradient-to-r from-accent-500 to-pink-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg">
                    📷
                </div>
                <h3 class="text-xl font-bold mb-3">Scan Struk Otomatis</h3>
                <p class="text-slate-600">
                    Upload foto struk belanja atau slip gaji, AI akan membaca dan mencatat secara otomatis.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card bg-gradient-to-br from-green-50 to-white p-8 rounded-3xl border border-green-100">
                <div
                    class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg">
                    📊
                </div>
                <h3 class="text-xl font-bold mb-3">Laporan Visual</h3>
                <p class="text-slate-600">
                    Lihat ringkasan keuangan dengan grafik yang mudah dipahami. Tahu kemana uang Anda pergi.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card bg-gradient-to-br from-blue-50 to-white p-8 rounded-3xl border border-blue-100">
                <div
                    class="w-14 h-14 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg">
                    🤖
                </div>
                <h3 class="text-xl font-bold mb-3">Telegram Bot</h3>
                <p class="text-slate-600">
                    Akses kapan saja dari Telegram. Tidak perlu buka aplikasi, cukup chat bot kami.
                </p>
            </div>

            <!-- Feature 5 -->
            <div
                class="feature-card bg-gradient-to-br from-orange-50 to-white p-8 rounded-3xl border border-orange-100">
                <div
                    class="w-14 h-14 bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg">
                    🎯
                </div>
                <h3 class="text-xl font-bold mb-3">Kategorisasi Cerdas</h3>
                <p class="text-slate-600">
                    AI otomatis mengkategorikan transaksi Anda. Tidak perlu repot pilih kategori manual.
                </p>
            </div>

            <!-- Feature 6 -->
            <div
                class="feature-card bg-gradient-to-br from-purple-50 to-white p-8 rounded-3xl border border-purple-100">
                <div
                    class="w-14 h-14 bg-gradient-to-r from-purple-500 to-violet-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg">
                    🔒
                </div>
                <h3 class="text-xl font-bold mb-3">Aman & Privat</h3>
                <p class="text-slate-600">
                    Data Anda terenkripsi dan hanya bisa diakses oleh Anda. Keamanan adalah prioritas kami.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-24 bg-gradient-to-b from-slate-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Cara Kerja</h2>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                Hanya 3 langkah mudah untuk mulai mengelola keuangan Anda
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="text-center">
                <div
                    class="w-20 h-20 mx-auto mb-6 gradient-bg rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-xl">
                    1
                </div>
                <h3 class="text-xl font-bold mb-3">Daftar Gratis</h3>
                <p class="text-slate-600">
                    Daftar dengan akun Google Anda. Hanya butuh beberapa detik, tanpa verifikasi ribet.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div
                    class="w-20 h-20 mx-auto mb-6 gradient-bg rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-xl">
                    2
                </div>
                <h3 class="text-xl font-bold mb-3">Hubungkan Telegram</h3>
                <p class="text-slate-600">
                    Sambungkan akun dengan bot Telegram kami untuk akses yang lebih mudah.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div
                    class="w-20 h-20 mx-auto mb-6 gradient-bg rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-xl">
                    3
                </div>
                <h3 class="text-xl font-bold mb-3">Mulai Catat</h3>
                <p class="text-slate-600">
                    Chat atau upload foto untuk mencatat transaksi. Dashboard akan update otomatis.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="gradient-bg rounded-3xl p-12 md:p-16 text-center text-white relative overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full"></div>
                <div class="absolute bottom-10 right-10 w-48 h-48 bg-white rounded-full"></div>
            </div>

            <div class="relative">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Mulai Kelola Keuangan Anda Sekarang
                </h2>
                <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">
                    Bergabung dengan ribuan pengguna yang sudah mengelola keuangan dengan lebih mudah
                </p>
                <a href="<?= site_url('login') ?>"
                    class="inline-flex items-center gap-2 bg-white text-primary-600 px-8 py-4 rounded-full font-semibold text-lg hover:bg-opacity-90 transition shadow-xl">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    Daftar dengan Google - Gratis!
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Flash Messages -->
<?php if ($this->session->flashdata('error')): ?>
    <div id="flash-error"
        class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3 z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <?= $this->session->flashdata('error') ?>
        <button onclick="this.parentElement.remove()" class="ml-2 hover:opacity-75">×</button>
    </div>
<?php endif; ?>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function () {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    // Auto hide flash messages
    setTimeout(() => {
        const flash = document.getElementById('flash-error');
        if (flash) flash.remove();
    }, 5000);
</script>

<?php $this->load->view('layout/main_footer'); ?>