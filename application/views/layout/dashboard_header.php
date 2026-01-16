<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($title) ? $title : 'Dashboard - incatat.id' ?>
    </title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon/logo.png') ?>" type="image/png">
    <link rel="icon" href="<?= base_url('assets/img/favicon/logo.png') ?>" type="image/png">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome-free-7.1.0-web/css/all.min.css') ?>">

    <!-- Chart.js for charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            font-family: 'Inter', system-ui, sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 50%, #d946ef 100%);
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.1) 0%, transparent 100%);
            border-left-color: #0ea5e9;
        }

        /* Skeleton Loading Animation */
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 0.5rem;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-text {
            height: 1rem;
            margin-bottom: 0.5rem;
        }

        .skeleton-text.sm {
            height: 0.75rem;
        }

        .skeleton-text.lg {
            height: 1.5rem;
        }

        .skeleton-circle {
            border-radius: 50%;
        }

        .skeleton-card {
            padding: 1.5rem;
            background: white;
            border-radius: 1rem;
            margin-bottom: 1rem;
        }
    </style>

    <script>
        function toggleSettingsDropdown() {
            const submenu = document.getElementById('settings-submenu');
            const arrow = document.getElementById('settings-arrow');
            submenu.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }
    </script>
</head>

<body class="bg-slate-100 antialiased overflow-x-hidden">
    <div class="flex h-screen overflow-hidden">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
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

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()">
        </div>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 overflow-y-auto h-screen">
            <!-- Top Bar -->
            <header class="sticky top-0 bg-white/95 backdrop-blur shadow-sm z-30">
                <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 hover:bg-slate-100 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="hidden sm:inline">Tambah</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 lg:p-8">