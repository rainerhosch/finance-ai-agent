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
            animation: skeleton-loading 1.2s ease-in-out infinite;
            border-radius: 0.5rem;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
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

        .skeleton-text.xl {
            height: 2rem;
        }

        .skeleton-circle {
            border-radius: 50%;
        }

        .skeleton-card {
            padding: 1.5rem;
            background: white;
            border-radius: 1rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .skeleton-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
        }

        .skeleton-chart {
            height: 300px;
            border-radius: 0.5rem;
        }

        .skeleton-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
        }

        /* Page transition */
        .page-content {
            opacity: 1;
            transition: opacity 0.15s ease-out;
        }

        .page-content.loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Page loading overlay */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 50%, #d946ef 100%);
            z-index: 9999;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease-out;
        }

        #page-loader.loading {
            animation: page-loading 1.5s ease-in-out infinite;
        }

        @keyframes page-loading {
            0% {
                transform: scaleX(0);
                transform-origin: left;
            }

            50% {
                transform: scaleX(1);
                transform-origin: left;
            }

            51% {
                transform-origin: right;
            }

            100% {
                transform: scaleX(0);
                transform-origin: right;
            }
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
    <!-- Page Loading Bar -->
    <div id="page-loader"></div>

    <div class="flex h-screen overflow-hidden">
        <?php $this->load->view('layout/dashboard_sidebar'); ?>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()">
        </div>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 overflow-y-auto h-screen">

            <?php $this->load->view('layout/dashboard_header'); ?>

            <!-- Page Content -->
            <div id="page-content" class="page-content p-2 md:p-4 lg:p-8">

                <?php
                // Load the page content based on $page variable
// $page should be set in controller, e.g., 'dashboard/index', 'dashboard/transactions'
                if (isset($content_view) && !empty($content_view)) {
                    $this->load->view($content_view);
                }
                ?>

                <?php $this->load->view('layout/dashboard_footer'); ?>