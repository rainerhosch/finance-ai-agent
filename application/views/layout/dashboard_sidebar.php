<!-- Sidebar -->
<aside id="sidebar"
    class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-50">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="p-6 border-b">
            <a href="<?= site_url('dashboard') ?>" class="flex items-center space-x-2">
                <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-wallet text-white text-lg"></i>
                </div>
                <span class="text-xl font-bold text-slate-800">incatat.id</span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1">
            <a href="<?= site_url('dashboard') ?>"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 border-l-4 border-transparent transition <?= (isset($page) && $page == 'dashboard') ? 'active font-semibold text-primary-600' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Dashboard
            </a>

            <a href="<?= site_url('dashboard/transactions') ?>"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 border-l-4 border-transparent transition <?= (isset($page) && $page == 'transactions') ? 'active font-semibold text-primary-600' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                Transaksi
            </a>

            <!-- Pengaturan Dropdown -->
            <div class="settings-dropdown">
                <button type="button" onclick="toggleSettingsDropdown()"
                    class="w-full sidebar-link flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 border-l-4 border-transparent transition <?= (isset($page) && in_array($page, ['settings', 'profile'])) ? 'active font-semibold text-primary-600' : '' ?>">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan
                    </span>
                    <svg id="settings-arrow"
                        class="w-4 h-4 transition-transform <?= (isset($page) && in_array($page, ['settings', 'profile'])) ? 'rotate-180' : '' ?>"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="settings-submenu"
                    class="pl-8 space-y-1 mt-1 <?= (isset($page) && in_array($page, ['settings', 'profile'])) ? '' : 'hidden' ?>">
                    <a href="<?= site_url('dashboard/profile') ?>"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition <?= (isset($page) && $page == 'profile') ? 'bg-slate-50 font-medium text-primary-600' : '' ?>">
                        <i class="fa-solid fa-user w-4"></i>
                        Profil
                    </a>
                    <a href="<?= site_url('dashboard/settings') ?>"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition <?= (isset($page) && $page == 'settings') ? 'bg-slate-50 font-medium text-primary-600' : '' ?>">
                        <i class="fa-solid fa-building w-4"></i>
                        Bisnis & Lainnya
                    </a>
                </div>
            </div>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t">
            <div class="flex items-center gap-3">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= $user['avatar'] ?>" alt="Avatar" class="w-10 h-10 rounded-full">
                <?php else: ?>
                    <div
                        class="w-10 h-10 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center font-semibold">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-slate-800 truncate">
                        <?= $user['name'] ?? 'User' ?>
                    </div>
                    <div class="text-sm text-slate-500 truncate">
                        <?= $user['email'] ?? '' ?>
                    </div>
                </div>
            </div>
            <a href="<?= site_url('logout') ?>"
                class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                Keluar
            </a>
        </div>
    </div>
</aside>