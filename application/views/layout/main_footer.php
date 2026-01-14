<!-- Footer -->
<footer class="bg-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <!-- Brand -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl">💰</span>
                    </div>
                    <span class="text-2xl font-bold">incatat.id</span>
                </div>
                <p class="text-slate-400 max-w-md">
                    Kelola keuangan Anda dengan mudah menggunakan AI dan Telegram Bot.
                    Catat pemasukan dan pengeluaran hanya dengan chat atau upload foto.
                </p>
            </div>

            <!-- Links -->
            <div>
                <h4 class="font-semibold mb-4">Produk</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="#features" class="hover:text-white transition">Fitur</a></li>
                    <li><a href="#how-it-works" class="hover:text-white transition">Cara Kerja</a></li>
                    <li><a href="#pricing" class="hover:text-white transition">Harga</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-semibold mb-4">Hubungi Kami</h4>
                <ul class="space-y-2 text-slate-400">
                    <li>📧 support@incatat.id</li>
                    <li>💬 @incatat_bot</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-slate-400 text-sm">
                ©
                <?= date('Y') ?> incatat.id. All rights reserved.
            </p>
            <div class="flex space-x-6 mt-4 md:mt-0">
                <a href="#" class="text-slate-400 hover:text-white transition">Privacy Policy</a>
                <a href="#" class="text-slate-400 hover:text-white transition">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar scroll effect
    window.addEventListener('scroll', function () {
        const nav = document.querySelector('nav');
        if (window.scrollY > 50) {
            nav.classList.add('bg-white/95', 'shadow-lg');
            nav.classList.remove('bg-transparent');
        } else {
            nav.classList.remove('bg-white/95', 'shadow-lg');
            nav.classList.add('bg-transparent');
        }
    });
</script>
</body>

</html>