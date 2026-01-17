</div>
</main>
</div>

<!-- Add Transaction Modal -->
<div id="add-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="min-h-screen px-4 py-8 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" onclick="closeAddModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
            <form id="add-transaction-form" class="flex flex-col h-full max-h-[85vh]">
                <div class="flex items-center justify-between p-6 border-b flex-shrink-0">
                    <h3 class="text-xl font-semibold">Tambah Transaksi</h3>
                    <button type="button" onclick="closeAddModal()" class="p-2 hover:bg-slate-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 overflow-y-auto flex-1 min-h-0">
                    <!-- Transaction Type -->
                    <div class="flex gap-4">
                        <label class="flex-1">
                            <input type="radio" name="type" value="expense" class="peer hidden" checked>
                            <div
                                class="p-4 border-2 rounded-xl cursor-pointer text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition">
                                <i class="fa-solid fa-arrow-up text-2xl text-red-500"></i>
                                <div class="font-medium mt-1">Pengeluaran</div>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="type" value="income" class="peer hidden">
                            <div
                                class="p-4 border-2 rounded-xl cursor-pointer text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                                <i class="fa-solid fa-arrow-down text-2xl text-green-500"></i>
                                <div class="font-medium mt-1">Pemasukan</div>
                            </div>
                        </label>
                    </div>

                    <!-- Date & Store -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal</label>
                            <input type="date" name="transaction_date" value="<?= date('Y-m-d') ?>" required
                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Toko (opsional)</label>
                            <input type="text" name="store_name"
                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                                placeholder="Contoh: Indomaret">
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-slate-700">
                                <i class="fa-solid fa-list mr-1"></i> Item Transaksi
                            </label>
                            <button type="button" onclick="addItem()"
                                class="text-primary-600 text-sm font-medium hover:text-primary-700">
                                <i class="fa-solid fa-plus mr-1"></i>Tambah Item
                            </button>
                        </div>

                        <div id="items-container" class="space-y-3">
                            <!-- Item rows will be added here -->
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="bg-slate-50 rounded-xl p-4 flex justify-between items-center">
                        <span class="font-medium text-slate-700">Total</span>
                        <span id="total-amount" class="text-xl font-bold text-slate-800">Rp 0</span>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Catatan (opsional)</label>
                        <input type="text" name="notes"
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                            placeholder="Catatan tambahan...">
                    </div>
                </div>

                <div class="flex-shrink-0 p-6 border-t bg-slate-50">
                    <button type="submit"
                        class="w-full gradient-bg text-white py-4 rounded-xl font-semibold hover:opacity-90 transition text-lg">
                        <i class="fa-solid fa-save mr-2"></i>Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Item Template (hidden) -->
<template id="item-template">
    <div class="item-row bg-slate-50 rounded-xl p-3 space-y-2">
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="items[INDEX][name]" placeholder="Nama item"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none"
                    required>
            </div>
            <button type="button" onclick="removeItem(this)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <div>
                <select name="items[INDEX][category_id]"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">Kategori</option>
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= $cat->name ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="relative">
                <input type="number" name="items[INDEX][qty]" value="1" min="1"
                    class="item-qty w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none"
                    placeholder="Qty" onchange="calculateTotal()">
            </div>
            <div class="relative">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 text-xs">Rp</span>
                <input type="number" name="items[INDEX][price]" step="100"
                    class="item-price w-full pl-8 pr-2 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none"
                    placeholder="Harga" required onchange="calculateTotal()">
            </div>
        </div>
    </div>
</template>

<!-- Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <div id="flash-success"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3 z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <?= $this->session->flashdata('success') ?>
        <button onclick="this.parentElement.remove()" class="ml-2 hover:opacity-75">×</button>
    </div>
<?php endif; ?>

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
    let itemIndex = 0;

    // Sidebar toggle
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }

    // Modal functions
    function openAddModal() {
        document.getElementById('add-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Add first item if empty
        if (document.querySelectorAll('.item-row').length === 0) {
            addItem();
        }
    }

    function closeAddModal() {
        document.getElementById('add-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Add item row
    function addItem() {
        const template = document.getElementById('item-template');
        const container = document.getElementById('items-container');
        const clone = template.content.cloneNode(true);

        // Replace INDEX with actual index
        clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
            el.name = el.name.replace('INDEX', itemIndex);
        });

        container.appendChild(clone);
        itemIndex++;
        calculateTotal();
    }

    // Remove item row
    function removeItem(btn) {
        const row = btn.closest('.item-row');
        row.remove();
        calculateTotal();

        // Ensure at least one item
        if (document.querySelectorAll('.item-row').length === 0) {
            addItem();
        }
    }

    // Calculate total
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 1;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            total += qty * price;
        });

        document.getElementById('total-amount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Handle form submission
    document.getElementById('add-transaction-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            const response = await fetch('<?= site_url('dashboard/add_transaction') ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                closeAddModal();
                location.reload();
            } else {
                alert(result.error || 'Terjadi kesalahan');
            }
        } catch (error) {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });

    // Auto hide flash messages
    setTimeout(() => {
        document.querySelectorAll('[id^="flash-"]').forEach(el => el.remove());
    }, 5000);

    // Close modal on escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAddModal();
    });

    // ========================================
    // AJAX Navigation System with Skeleton Loading
    // ========================================

    const PageNavigator = {
        pageContent: document.getElementById('page-content'),
        pageLoader: document.getElementById('page-loader'),
        currentUrl: window.location.href,

        // Skeleton templates
        skeletons: {
            dashboard: `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4 lg:gap-6 mb-8">
                    ${Array(4).fill(`
                        <div class="skeleton-card">
                            <div class="flex items-center justify-between mb-4">
                                <div class="skeleton skeleton-text sm" style="width: 60%"></div>
                                <div class="skeleton skeleton-icon"></div>
                            </div>
                            <div class="skeleton skeleton-text xl" style="width: 80%"></div>
                            <div class="skeleton skeleton-text sm" style="width: 40%; margin-top: 0.5rem"></div>
                        </div>
                    `).join('')}
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-4 lg:gap-6 mb-8">
                    <div class="lg:col-span-2 skeleton-card">
                        <div class="skeleton skeleton-text lg" style="width: 40%; margin-bottom: 1.5rem"></div>
                        <div class="skeleton skeleton-chart"></div>
                    </div>
                    <div class="skeleton-card">
                        <div class="skeleton skeleton-text lg" style="width: 50%; margin-bottom: 1.5rem"></div>
                        ${Array(5).fill(`
                            <div class="skeleton-row">
                                <div class="skeleton skeleton-icon"></div>
                                <div style="flex: 1">
                                    <div class="skeleton skeleton-text" style="width: 70%"></div>
                                    <div class="skeleton skeleton-text sm" style="width: 40%"></div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="skeleton-card">
                    <div class="flex items-center justify-between mb-6">
                        <div class="skeleton skeleton-text lg" style="width: 30%"></div>
                        <div class="skeleton skeleton-text sm" style="width: 15%"></div>
                    </div>
                    ${Array(5).fill(`
                        <div class="skeleton-row border-b border-slate-100 py-4">
                            <div class="skeleton skeleton-icon"></div>
                            <div style="flex: 1">
                                <div class="skeleton skeleton-text" style="width: 50%"></div>
                                <div class="skeleton skeleton-text sm" style="width: 30%"></div>
                            </div>
                            <div style="text-align: right">
                                <div class="skeleton skeleton-text" style="width: 100px; margin-left: auto"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `,
            transactions: `
                <div class="skeleton-card mb-6">
                    <div class="flex flex-wrap gap-4">
                        ${Array(4).fill(`
                            <div style="flex: 1; min-width: 150px">
                                <div class="skeleton skeleton-text sm" style="width: 40%; margin-bottom: 0.5rem"></div>
                                <div class="skeleton" style="height: 42px; width: 100%"></div>
                            </div>
                        `).join('')}
                        <div style="display: flex; gap: 0.5rem; align-items: flex-end">
                            <div class="skeleton" style="height: 42px; width: 80px"></div>
                            <div class="skeleton" style="height: 42px; width: 60px"></div>
                        </div>
                    </div>
                </div>
                <div class="skeleton-card">
                    ${Array(8).fill(`
                        <div class="skeleton-row border-b border-slate-100 py-4">
                            <div class="skeleton skeleton-icon" style="width: 2.5rem; height: 2.5rem"></div>
                            <div style="flex: 1">
                                <div class="skeleton skeleton-text" style="width: 40%"></div>
                                <div class="skeleton skeleton-text sm" style="width: 25%"></div>
                            </div>
                            <div style="text-align: right">
                                <div class="skeleton skeleton-text" style="width: 100px; margin-left: auto"></div>
                                <div class="skeleton skeleton-text sm" style="width: 60px; margin-left: auto; margin-top: 0.25rem"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `,
            default: `
                <div class="skeleton-card">
                    <div class="skeleton skeleton-text lg" style="width: 40%; margin-bottom: 2rem"></div>
                    ${Array(6).fill(`
                        <div class="skeleton skeleton-text" style="width: 100%; margin-bottom: 1rem"></div>
                    `).join('')}
                </div>
            `
        },

        // Get skeleton type based on URL
        getSkeletonType(url) {
            if (url.includes('/transactions')) return 'transactions';
            if (url.includes('/dashboard') && !url.includes('/profile') && !url.includes('/settings')) return 'dashboard';
            return 'default';
        },

        // Show loading state
        showLoading(targetUrl) {
            this.pageLoader.classList.add('loading');
            this.pageContent.classList.add('loading');

            // Show skeleton
            const skeletonType = this.getSkeletonType(targetUrl);
            this.pageContent.innerHTML = this.skeletons[skeletonType];
            this.pageContent.classList.remove('loading');
        },

        // Hide loading state
        hideLoading() {
            this.pageLoader.classList.remove('loading');
            this.pageContent.classList.remove('loading');
        },

        // Update active navigation
        updateActiveNav(url) {
            document.querySelectorAll('.sidebar-link').forEach(link => {
                const href = link.getAttribute('href');
                if (!href) return; // Skip links without href
                
                const isActive = url.includes(href.split('/').pop());

                link.classList.toggle('active', isActive);
                link.classList.toggle('font-semibold', isActive);
                link.classList.toggle('text-primary-600', isActive);
            });
        },

        // Navigate to URL
        async navigateTo(url, pushState = true) {
            if (url === this.currentUrl) return;

            try {
                this.showLoading(url);

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const html = await response.text();

                // Parse the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Get new page content
                const newContent = doc.getElementById('page-content');
                const newTitle = doc.querySelector('title');
                const newPageTitle = doc.querySelector('header h1');

                if (newContent) {
                    // Update content with fade effect
                    this.pageContent.style.opacity = '0';

                    setTimeout(() => {
                        this.pageContent.innerHTML = newContent.innerHTML;
                        this.pageContent.style.opacity = '1';

                        // Update title
                        if (newTitle) document.title = newTitle.textContent;
                        if (newPageTitle) {
                            const headerTitle = document.querySelector('header h1');
                            if (headerTitle) headerTitle.textContent = newPageTitle.textContent;
                        }

                        // Execute scripts in new content
                        this.executeScripts(this.pageContent);

                        this.hideLoading();
                    }, 100);
                }

                // Update URL
                if (pushState) {
                    history.pushState({ url: url }, '', url);
                }

                this.currentUrl = url;
                this.updateActiveNav(url);

            } catch (error) {
                console.error('Navigation error:', error);
                // Fallback to regular navigation
                window.location.href = url;
            }
        },

        // Execute scripts in dynamically loaded content
        executeScripts(container) {
            const scripts = container.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
        },

        // Initialize
        init() {
            // Intercept navigation link clicks
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href]');
                if (!link) return;

                const href = link.getAttribute('href');

                // Only intercept internal dashboard links
                if (href &&
                    href.includes('/dashboard') &&
                    !href.includes('/logout') &&
                    !link.hasAttribute('target') &&
                    !e.ctrlKey && !e.metaKey && !e.shiftKey) {

                    e.preventDefault();
                    this.navigateTo(href);

                    // Close mobile sidebar if open
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                        if (window.innerWidth < 1024) {
                            toggleSidebar();
                        }
                    }
                }
            });

            // Handle browser back/forward
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.url) {
                    this.navigateTo(e.state.url, false);
                } else {
                    this.navigateTo(window.location.href, false);
                }
            });

            // Set initial state
            history.replaceState({ url: window.location.href }, '', window.location.href);
        }
    };

    // Initialize page navigator
    PageNavigator.init();
</script>
</body>

</html>