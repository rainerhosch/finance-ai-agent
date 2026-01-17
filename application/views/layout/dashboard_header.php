<!-- Top Bar -->
<header class="sticky top-0 bg-white/95 backdrop-blur shadow-sm z-30">
    <div class="flex items-center justify-between px-4 lg:px-8 py-4">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="lg:hidden p-2 hover:bg-slate-100 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <h1 class="text-xl font-semibold text-slate-800">
                <?= isset($title) ? str_replace(' - incatat.id', '', $title) : 'Dashboard' ?>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <!-- Quick Add Button -->
            <button onclick="openAddModal()"
                class="gradient-bg text-white px-4 py-2 rounded-lg font-medium hover:opacity-90 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Tambah</span>
            </button>
        </div>
    </div>
</header>