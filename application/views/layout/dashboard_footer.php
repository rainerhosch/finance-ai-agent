</div>
</main>
</div>

<!-- Add Transaction Modal -->
<div id="add-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeAddModal()"></div>
    <div
        class="absolute inset-4 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:w-full md:max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
        <form id="add-transaction-form" class="flex flex-col h-full">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-xl font-semibold">Tambah Transaksi</h3>
                <button type="button" onclick="closeAddModal()" class="p-2 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4 overflow-y-auto flex-1">
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

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                        <input type="number" name="amount" required
                            class="w-full pl-12 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                            placeholder="0">
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                    <select name="category_id"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        <option value="">Pilih kategori (opsional)...</option>
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php
                            $expense_cats = array_filter($categories, function ($c) {
                                return $c->type == 'expense';
                            });
                            $income_cats = array_filter($categories, function ($c) {
                                return $c->type == 'income';
                            });
                            ?>
                            <?php if (!empty($expense_cats)): ?>
                                <optgroup label="Pengeluaran">
                                    <?php foreach ($expense_cats as $cat): ?>
                                        <option value="<?= $cat->id ?>"><?= $cat->icon ?>             <?= $cat->name ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                            <?php if (!empty($income_cats)): ?>
                                <optgroup label="Pemasukan">
                                    <?php foreach ($income_cats as $cat): ?>
                                        <option value="<?= $cat->id ?>"><?= $cat->icon ?>             <?= $cat->name ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                    <input type="text" name="description"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                        placeholder="Contoh: Makan siang bersama rekan">
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal</label>
                    <input type="date" name="transaction_date" value="<?= date('Y-m-d') ?>" required
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
            </div>

            <div class="p-6 border-t bg-slate-50">
                <button type="submit"
                    class="w-full gradient-bg text-white py-3 rounded-xl font-semibold hover:opacity-90 transition">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

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
    // Sidebar toggle
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }

    // Modal functions
    function openAddModal() {
        document.getElementById('add-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddModal() {
        document.getElementById('add-modal').classList.add('hidden');
        document.body.style.overflow = '';
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
</script>
</body>

</html>