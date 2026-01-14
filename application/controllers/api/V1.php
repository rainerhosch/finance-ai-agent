<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API V1 Controller
 * 
 * REST API for Telegram Bot integration.
 */
class V1 extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaction_model');
        $this->load->model('Category_model');
    }

    /**
     * Verify if user is registered
     * GET /api/v1/verify-user?telegram_id=xxx OR ?email=xxx
     */
    public function verify_user()
    {
        $telegram_id = $this->input->get('telegram_id');
        $email = $this->input->get('email');

        if (empty($telegram_id) && empty($email)) {
            $this->json_response(array(
                'error' => 'Parameter telegram_id atau email diperlukan'
            ), 400);
            return;
        }

        $user = null;

        if (!empty($telegram_id)) {
            $user = $this->User_model->find_by_telegram_id($telegram_id);
        } elseif (!empty($email)) {
            $user = $this->User_model->find_by_email($email);
        }

        if ($user) {
            $this->json_response(array(
                'registered' => true,
                'user' => array(
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telegram_linked' => !empty($user->telegram_user_id),
                    'profile_completed' => (bool) $user->profile_completed
                )
            ));
        } else {
            $this->json_response(array(
                'registered' => false,
                'message' => 'Pengguna tidak ditemukan. Silakan daftar di website kami.'
            ));
        }
    }

    /**
     * Link Telegram account to user
     * POST /api/v1/link-telegram
     * Body: { "email": "xxx", "telegram_id": "xxx" }
     */
    public function link_telegram()
    {
        $input = $this->get_json_input();

        if (empty($input['email']) || empty($input['telegram_id'])) {
            $this->json_response(array(
                'error' => 'Parameter email dan telegram_id diperlukan'
            ), 400);
            return;
        }

        $user = $this->User_model->find_by_email($input['email']);

        if (!$user) {
            $this->json_response(array(
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ), 404);
            return;
        }

        // Check if telegram_id is already linked to another account
        $existing = $this->User_model->find_by_telegram_id($input['telegram_id']);
        if ($existing && $existing->id != $user->id) {
            $this->json_response(array(
                'success' => false,
                'message' => 'Telegram ID sudah terhubung ke akun lain'
            ), 409);
            return;
        }

        $this->User_model->link_telegram($user->id, $input['telegram_id']);

        $this->json_response(array(
            'success' => true,
            'message' => 'Akun Telegram berhasil dihubungkan!'
        ));
    }

    /**
     * Create a new transaction
     * POST /api/v1/transactions/create
     * Requires Bearer token authentication
     */
    public function create_transaction()
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

        $transaction_data = array(
            'user_id' => $this->user->id,
            'type' => $input['type'],
            'amount' => $input['amount'],
            'description' => isset($input['description']) ? $input['description'] : null,
            'category_id' => isset($input['category_id']) ? $input['category_id'] : null,
            'transaction_date' => isset($input['date']) ? $input['date'] : date('Y-m-d'),
            'source' => 'telegram',
            'attachment_url' => isset($input['attachment_url']) ? $input['attachment_url'] : null
        );

        $transaction_id = $this->Transaction_model->create($transaction_data);

        $this->json_response(array(
            'success' => true,
            'message' => 'Transaksi berhasil dicatat!',
            'transaction_id' => $transaction_id
        ), 201);
    }

    /**
     * Get transactions list
     * GET /api/v1/transactions
     * Requires Bearer token authentication
     */
    public function transactions()
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
     * Get summary
     * GET /api/v1/summary?period=month
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
     * Get categories list
     * GET /api/v1/categories
     */
    public function categories()
    {
        if (!$this->authenticate()) {
            return;
        }

        $type = $this->input->get('type');
        $categories = $this->Category_model->get_all($this->user->id, $type);

        $this->json_response(array(
            'success' => true,
            'data' => $categories
        ));
    }
}
