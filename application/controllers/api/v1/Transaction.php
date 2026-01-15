<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API Transaction Controller
 * 
 * Handles transaction-related API endpoints.
 */
class Transaction extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaction_model');
        $this->load->model('Transaction_item_model');
        $this->load->model('Category_model');
    }

    /**
     * Get transactions list
     * GET /api/v1/transaction
     */
    public function index()
    {
        if (!$this->authenticate()) {
            return;
        }

        $filters = array(
            'type' => $this->input->get('type'),
            'date_from' => $this->input->get('from'),
            'date_to' => $this->input->get('to'),
            'limit' => $this->input->get('limit') ?: 20,
            'offset' => $this->input->get('offset') ?: 0
        );

        $transactions = $this->Transaction_model->get_by_user($this->user->id, $filters);

        // Add items to each transaction
        foreach ($transactions as &$tx) {
            $tx->items = $this->Transaction_item_model->get_by_transaction($tx->id);
        }

        $this->json_response(array(
            'success' => true,
            'data' => $transactions
        ));
    }

    /**
     * Create a new transaction
     * POST /api/v1/transaction/create
     * 
     * Supports two formats:
     * 1. Simple: { type, amount, description }
     * 2. With items: { type, store_name, items: [{name, qty, price, category}] }
     */
    public function create()
    {
        if (!$this->authenticate()) {
            return;
        }

        $input = $this->get_json_input();

        // Validate required fields
        if (empty($input['type']) || !in_array($input['type'], array('income', 'expense'))) {
            $this->json_response(array('error' => 'Tipe transaksi tidak valid (income/expense)'), 400);
            return;
        }

        // Validate amount or items
        $has_amount = !empty($input['amount']) && is_numeric($input['amount']) && $input['amount'] > 0;
        $has_items = !empty($input['items']) && is_array($input['items']);

        if (!$has_amount && !$has_items) {
            $this->json_response(array('error' => 'Jumlah atau items diperlukan'), 400);
            return;
        }

        // Process items - resolve category names to IDs
        if ($has_items) {
            foreach ($input['items'] as &$item) {
                if (empty($item['name'])) {
                    $this->json_response(array('error' => 'Nama item diperlukan'), 400);
                    return;
                }

                // Resolve category by name if provided
                if (!empty($item['category']) && empty($item['category_id'])) {
                    $category = $this->Category_model->find_by_name($item['category'], $this->user->business_id);
                    if ($category) {
                        $item['category_id'] = $category->id;
                    }
                }
            }
        }

        $transaction_data = array(
            'user_id' => $this->user->id,
            'type' => $input['type'],
            'transaction_date' => isset($input['date']) ? $input['date'] : date('Y-m-d'),
            'store_name' => isset($input['store_name']) ? $input['store_name'] : null,
            'source' => isset($input['source']) ? $input['source'] : 'telegram',
            'notes' => isset($input['notes']) ? $input['notes'] : (isset($input['description']) ? $input['description'] : null),
            'attachment_url' => isset($input['attachment_url']) ? $input['attachment_url'] : null
        );

        // Add items or simple amount
        if ($has_items) {
            $transaction_data['items'] = $input['items'];
        } else {
            $transaction_data['amount'] = $input['amount'];
            $transaction_data['description'] = isset($input['description']) ? $input['description'] : null;

            // Resolve category
            if (!empty($input['category'])) {
                $category = $this->Category_model->find_by_name($input['category'], $this->user->business_id);
                if ($category) {
                    $transaction_data['category_id'] = $category->id;
                }
            } elseif (!empty($input['category_id'])) {
                $transaction_data['category_id'] = $input['category_id'];
            }
        }

        $transaction_id = $this->Transaction_model->create($transaction_data);

        if (!$transaction_id) {
            $this->json_response(array('error' => 'Gagal menyimpan transaksi'), 500);
            return;
        }

        $transaction = $this->Transaction_model->find_with_items($transaction_id);

        $this->json_response(array(
            'success' => true,
            'message' => 'Transaksi berhasil dicatat!',
            'transaction_id' => $transaction_id,
            'data' => $transaction
        ), 201);
    }

    /**
     * Get summary
     * GET /api/v1/transaction/summary?period=month
     */
    public function summary()
    {
        if (!$this->authenticate()) {
            return;
        }

        $period = $this->input->get('period') ?: 'month';
        $summary = $this->Transaction_model->get_summary($this->user->id, $period);

        $this->json_response(array(
            'success' => true,
            'period' => $period,
            'data' => $summary
        ));
    }

    /**
     * Get single transaction with items
     * GET /api/v1/transaction/{id}
     */
    public function show($id = null)
    {
        if (!$this->authenticate()) {
            return;
        }

        if (empty($id)) {
            $this->json_response(array('error' => 'ID transaksi diperlukan'), 400);
            return;
        }

        $transaction = $this->Transaction_model->find_with_items($id);

        if (!$transaction || $transaction->business_id != $this->user->business_id) {
            $this->json_response(array('error' => 'Transaksi tidak ditemukan'), 404);
            return;
        }

        $this->json_response(array(
            'success' => true,
            'data' => $transaction
        ));
    }

    /**
     * Delete transaction
     * DELETE /api/v1/transaction/delete/{id}
     */
    public function delete($id = null)
    {
        if (!$this->authenticate()) {
            return;
        }

        if (empty($id)) {
            $this->json_response(array('error' => 'ID transaksi diperlukan'), 400);
            return;
        }

        $transaction = $this->Transaction_model->find($id);

        if (!$transaction || $transaction->business_id != $this->user->business_id) {
            $this->json_response(array('error' => 'Transaksi tidak ditemukan'), 404);
            return;
        }

        $this->Transaction_model->delete($id);

        $this->json_response(array(
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
        ));
    }
}
