<?php $this->load->view('layout/dashboard_header'); ?>

<!-- Filters -->
<div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-sm font-medium text-slate-700 mb-1">Tipe</label>
            <select name="type"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Semua</option>
                <option value="income" <?= ($filters['type'] ?? '') == 'income' ? 'selected' : '' ?>>Pemasukan</option>
                <option value="expense" <?= ($filters['type'] ?? '') == 'expense' ? 'selected' : '' ?>>Pengeluaran
                </option>
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
            <select name="category"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
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
            <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="<?= $filters['date_from'] ?? '' ?>"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="<?= $filters['date_to'] ?? '' ?>"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 outline-none">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-2 bg-primary-500 text-white rounded-lg font-medium hover:bg-primary-600 transition">
                Filter
            </button>
            <a href="<?= site_url('dashboard/transactions') ?>"
                class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg font-medium hover:bg-slate-200 transition">
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
                <div class="p-4 hover:bg-slate-50 transition flex items-center gap-4">
                    <span class="text-3xl">
                        <?= $tx->category_icon ?: ($tx->type == 'income' ? '📥' : '📤') ?>
                    </span>

                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-slate-800">
                            <?= $tx->description ?: $tx->category_name ?: ucfirst($tx->type) ?>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                                <?= date('d M Y', strtotime($tx->transaction_date)) ?>
                            </span>
                            <?php if ($tx->category_name): ?>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                                    <?= $tx->category_name ?>
                                </span>
                            <?php endif; ?>
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                                via
                                <?= ucfirst($tx->source) ?>
                            </span>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-lg font-semibold <?= $tx->type == 'income' ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $tx->type == 'income' ? '+' : '-' ?> Rp
                            <?= number_format($tx->amount, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination placeholder -->
        <div class="p-4 border-t bg-slate-50 text-center">
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