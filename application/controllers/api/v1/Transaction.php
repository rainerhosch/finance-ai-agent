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
        $this->load->model('Category_model');
    }

    /**
     * Get transactions list
     * GET /api/v1/transaction
     * Requires Bearer token authentication
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

        $this->json_response(array(
            'success' => true,
            'data' => $transactions
        ));
    }

    /**
     * Create a new transaction
     * POST /api/v1/transaction/create
     * Requires Bearer token authentication
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

        if (empty($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
            $this->json_response(array('error' => 'Jumlah harus berupa angka positif'), 400);
            return;
        }

        // Handle category - try to match by name if category_id not provided
        $category_id = null;
        if (!empty($input['category_id'])) {
            $category_id = $input['category_id'];
        } elseif (!empty($input['category'])) {
            // Try to find category by name
            $category = $this->Category_model->find_by_name($input['category'], $this->user->id);
            if ($category) {
                $category_id = $category->id;
            }
        }

        $transaction_data = array(
            'user_id' => $this->user->id,
            'type' => $input['type'],
            'amount' => $input['amount'],
            'description' => isset($input['description']) ? $input['description'] : null,
            'category_id' => $category_id,
            'transaction_date' => isset($input['date']) ? $input['date'] : date('Y-m-d'),
            'source' => isset($input['source']) ? $input['source'] : 'telegram',
            'attachment_url' => isset($input['attachment_url']) ? $input['attachment_url'] : null
        );

        $transaction_id = $this->Transaction_model->create($transaction_data);

        $this->json_response(array(
            'success' => true,
            'message' => 'Transaksi berhasil dicatat!',
            'transaction_id' => $transaction_id,
            'data' => $transaction_data
        ), 201);
    }

    /**
     * Get summary
     * GET /api/v1/transaction/summary?period=month
     * Requires Bearer token authentication
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
     * Delete transaction
     * DELETE /api/v1/transaction/delete/{id}
     * Requires Bearer token authentication
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

        if (!$transaction || $transaction->user_id != $this->user->id) {
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
