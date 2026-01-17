<?php $this->load->view('layout/dashboard_header'); ?>

<!-- Filters -->
<div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs md:text-sm font-medium text-slate-700 mb-1">Tipe</label>
            <select name="type"
                class="w-full px-3 py-1 md:py-2 text-xs md:text-base border rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Semua</option>
                <option value="income" <?= ($filters['type'] ?? '') == 'income' ? 'selected' : '' ?>>Pemasukan</option>
                <option value="expense" <?= ($filters['type'] ?? '') == 'expense' ? 'selected' : '' ?>>Pengeluaran
                </option>
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs md:text-sm font-medium text-slate-700 mb-1">Kategori</label>
            <select name="category"
                class="w-full px-3 py-1 md:py-2 text-xs md:text-base border rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Semua</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' ?>>
                        <?= $cat->icon ?>
                        <?= $cat->name ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs md:text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="<?= $filters['date_from'] ?? '' ?>"
                class="w-full px-3 py-1 md:py-2 text-xs md:text-base border rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs md:text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="<?= $filters['date_to'] ?? '' ?>"
                class="w-full px-3 py-1 md:py-2 text-xs md:text-base border rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-1 md:py-2 text-xs md:text-base bg-primary-500 text-white rounded-lg font-medium hover:bg-primary-600 transition">
                Filter
            </button>
            <a href="<?= site_url('dashboard/transactions') ?>"
                class="px-4 py-1 md:py-2 text-xs md:text-base bg-slate-100 text-slate-600 rounded-lg font-medium hover:bg-slate-200 transition">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Transactions List -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <?php if (!empty($transactions)): ?>
        <div class="divide-y">
            <?php foreach ($transactions as $tx): ?>
                <div class="p-4 hover:bg-slate-50 transition">
                    <div class="flex items-start gap-4">
                        <span class="text-md md:text-3xl mt-1">
                            <?php if ($tx->category_icon): ?>
                                <i class="<?= $tx->category_icon ?>"></i>
                            <?php else: ?>
                                <i
                                    class="fa-solid <?= $tx->type == 'income' ? 'fa-arrow-down text-green-500' : 'fa-arrow-up text-red-500' ?>"></i>
                            <?php endif; ?>
                        </span>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="text-sm md:text-lg font-medium text-slate-800">
                                        <?php if (!empty($tx->store_name)): ?>
                                            <?= htmlspecialchars($tx->store_name) ?>
                                        <?php elseif (!empty($tx->description)): ?>
                                            <?= htmlspecialchars($tx->description) ?>
                                        <?php elseif (!empty($tx->category_name)): ?>
                                            <?= $tx->category_name ?>
                                        <?php else: ?>
                                            <?= ucfirst($tx->type) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs md:text-sm text-slate-400 mt-0.5">
                                        <?= date('d M Y', strtotime($tx->transaction_date)) ?>
                                        <?php if (!empty($tx->notes)): ?>
                                            · <?= htmlspecialchars($tx->notes) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div
                                        class="text-sm md:text-lg font-semibold <?= $tx->type == 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                        <?= $tx->type == 'income' ? '+' : '-' ?> Rp
                                        <?= number_format($tx->amount, 0, ',', '.') ?>
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        via <?= ucfirst($tx->source) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Item Details -->
                            <?php if (!empty($tx->items) && is_array($tx->items) && count($tx->items) > 0): ?>
                                <div class="mt-2 bg-slate-50 rounded-lg p-2">
                                    <div class="text-xs text-slate-500 mb-1">
                                        <i class="fa-solid fa-list mr-1"></i><?= count($tx->items) ?> item
                                    </div>
                                    <div class="text-xs md:text-sm">
                                        <?php foreach (array_slice($tx->items, 0, 3) as $item): ?>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-slate-600">
                                                    <?= htmlspecialchars($item->name) ?>
                                                    <?php if ($item->qty > 1): ?>
                                                        <span class="text-slate-400">×<?= $item->qty ?></span>
                                                    <?php endif; ?>
                                                </span>
                                                <span class="text-slate-700">Rp
                                                    <?= number_format($item->subtotal, 0, ',', '.') ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (count($tx->items) > 3): ?>
                                            <div class="text-xs text-slate-400">
                                                +<?= count($tx->items) - 3 ?> item lainnya
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination placeholder -->
        <div class="text-xs md:text-base p-4 border-t bg-slate-50 text-center">
            <button class="text-primary-600 font-medium hover:text-primary-700">
                Muat lebih banyak...
            </button>
        </div>

    <?php else: ?>
        <div class="text-center py-16">
            <div class="text-6xl mb-4">📝</div>
            <h4 class="text-lg font-semibold text-slate-800 mb-2">Belum ada transaksi</h4>
            <p class="text-slate-500 mb-4">Mulai catat pemasukan dan pengeluaran Anda</p>
            <button onclick="openAddModal()" class="gradient-bg text-white px-6 py-2 rounded-lg font-medium">
                Tambah Transaksi
            </button>
        </div>
    <?php endif; ?>
</div>

<?php $this->load->view('layout/dashboard_footer'); ?>