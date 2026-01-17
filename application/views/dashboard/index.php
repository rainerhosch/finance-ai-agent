<?php $this->load->view('layout/dashboard_header'); ?>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4 lg:gap-6 mb-8">
    <!-- Income -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-500 text-xs md:text-sm">Pemasukan</span>
            <span
                class="w-8 md:w-10 h-8 md:h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center"><i
                    class="fa-solid fa-arrow-down"></i></span>
        </div>
        <div class="text-md md:text-2xl font-bold text-slate-800">
            Rp
            <?= number_format($summary['income'], 0, ',', '.') ?>
        </div>
        <div class="text-xs md:text-sm text-slate-500 mt-1">
            <?= $summary['income_count'] ?> transaksi
        </div>
    </div>

    <!-- Expense -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-500 text-xs md:text-sm">Pengeluaran</span>
            <span class="w-8 md:w-10 h-8 md:h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center"><i
                    class="fa-solid fa-arrow-up"></i></span>
        </div>
        <div class="text-sm md:text-2xl font-bold text-slate-800">
            Rp
            <?= number_format($summary['expense'], 0, ',', '.') ?>
        </div>
        <div class="text-xs md:text-sm text-slate-500 mt-1">
            <?= $summary['expense_count'] ?> transaksi
        </div>
    </div>
    <!-- Balance -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-500 text-xs md:text-sm">Saldo Keuangan Bulan Ini</span>
            <span
                class="w-8 md:w-10 h-8 md:h-10 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center"><i
                    class="fa-solid fa-wallet"></i></span>
        </div>
        <div class="text-sm md:text-2xl font-bold <?= $summary['balance'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
            Rp
            <?= number_format($summary['balance'], 0, ',', '.') ?>
        </div>
        <div class="text-xs md:text-sm text-slate-500 mt-1">
            <?php
            $diff = $summary['income'] > 0 ? round(($summary['balance'] / $summary['income']) * 100) : 0;
            echo $diff >= 0 ? "Sisa {$diff}% dari Pemasukan" : "Over " . abs($diff) . "% dari Pemasukan";
            ?>
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-500 text-xs md:text-sm">Total Transaksi</span>
            <span
                class="w-8 md:w-10 h-8 md:h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">📊</span>
        </div>
        <div class="text-sm md:text-2xl font-bold text-slate-800">
            <?= $summary['income_count'] + $summary['expense_count'] ?>
        </div>
        <div class="text-xs md:text-sm text-slate-500 mt-1">
            Bulan ini
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-4 lg:gap-6 mb-8">
    <!-- Chart Section -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-lg font-semibold mb-6">Ringkasan 6 Bulan Terakhir</h3>
        <div class="relative" style="height: 300px;">
            <canvas id="summaryChart"></canvas>
        </div>
    </div>

    <!-- Top Expenses -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-lg font-semibold mb-6">Pengeluaran Terbesar</h3>
        <div class="space-y-4">
            <?php if (!empty($top_expenses)): ?>
                <?php foreach ($top_expenses as $expense): ?>
                    <div class="flex items-center gap-3">
                        <span class="text-md md:text-2xl">
                            <?= $expense->icon ? "<i class='{$expense->icon}'></i>" : '📌' ?>
                        </span>
                        <div class="flex-1">
                            <div class="text-sm md:text-base font-medium text-slate-800">
                                <?= $expense->name ?: 'Lainnya' ?>
                            </div>
                            <div class="text-xs md:text-sm text-slate-500">Rp
                                <?= number_format($expense->total, 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-slate-500 text-center py-8">Belum ada data pengeluaran</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="mt-8 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold">Transaksi Terakhir</h3>
        <a href="<?= site_url('dashboard/transactions') ?>"
            class="text-primary-600 hover:text-primary-700 font-medium text-sm">
            Lihat Semua →
        </a>
    </div>

    <?php if (!empty($recent_transactions)): ?>
        <div class="divide-y">
            <?php foreach ($recent_transactions as $tx): ?>
                <div class="py-4 flex items-center gap-4">
                    <span class="text-md md:text-2xl">
                        <?php if ($tx->category_icon): ?>
                            <i class="<?= $tx->category_icon ?>"></i>
                        <?php else: ?>
                            <i
                                class="fa-solid <?= $tx->type == 'income' ? 'fa-arrow-down text-green-500' : 'fa-arrow-up text-red-500' ?>"></i>
                        <?php endif; ?>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm md:text-base font-medium text-slate-800">
                            <?= $tx->description ?: $tx->category_name ?: ucfirst($tx->type) ?>
                        </div>
                        <div class="text-xs md:text-sm text-slate-500">
                            <?= date('d M Y', strtotime($tx->transaction_date)) ?> •
                            <?= $tx->source ?>
                        </div>
                    </div>
                    <div class="text-right">
                        <div
                            class="text-sm md:text-base font-semibold <?= $tx->type == 'income' ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $tx->type == 'income' ? '+' : '-' ?> Rp
                            <?= number_format($tx->amount, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="text-6xl mb-4">📝</div>
            <h4 class="text-lg font-semibold text-slate-800 mb-2">Belum ada transaksi</h4>
            <p class="text-slate-500 mb-4">Mulai catat pemasukan dan pengeluaran Anda</p>
            <button onclick="openAddModal()" class="gradient-bg text-white px-6 py-2 rounded-lg font-medium">
                Tambah Transaksi Pertama
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
    // Chart initialization
    const ctx = document.getElementById('summaryChart').getContext('2d');

    <?php
    // Prepare chart data
    $months = [];
    $income_data = [];
    $expense_data = [];

    if (!empty($monthly_data)) {
        $grouped = [];
        foreach ($monthly_data as $item) {
            if (!isset($grouped[$item->month])) {
                $grouped[$item->month] = ['income' => 0, 'expense' => 0];
            }
            $grouped[$item->month][$item->type] = (float) $item->total;
        }

        foreach ($grouped as $month => $data) {
            $months[] = date('M Y', strtotime($month . '-01'));
            $income_data[] = $data['income'];
            $expense_data[] = $data['expense'];
        }
    }

    // Fill with zeros if no data
    if (empty($months)) {
        for ($i = 5; $i >= 0; $i--) {
            $months[] = date('M Y', strtotime("-$i months"));
            $income_data[] = 0;
            $expense_data[] = 0;
        }
    }
    ?>

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: <?= json_encode($income_data) ?>,
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderRadius: 8,
                    barPercentage: 0.6,
                },
                {
                    label: 'Pengeluaran',
                    data: <?= json_encode($expense_data) ?>,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderRadius: 8,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>

<?php $this->load->view('layout/dashboard_footer'); ?>