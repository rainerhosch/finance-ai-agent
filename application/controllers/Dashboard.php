<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller
 * 
 * User dashboard with overview, profile, and transaction management.
 */
class Dashboard extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Dashboard overview
     */
    public function index()
    {
        $user = $this->get_user();

        // Get summary data
        $this->data['summary'] = $this->Transaction_model->get_summary($user['id'], 'month');
        $this->data['recent_transactions'] = $this->Transaction_model->get_recent($user['id'], 5);
        $this->data['top_expenses'] = $this->Transaction_model->get_top_categories($user['id'], 'expense', 5);
        $this->data['monthly_data'] = $this->Transaction_model->get_monthly_summary($user['id'], 6);
        $this->data['categories'] = $this->Category_model->get_by_user($user['id']);

        $this->data['title'] = 'Dashboard - incatat.id';
        $this->data['page'] = 'dashboard';

        $this->load->view('dashboard/index', $this->data);
    }

    /**
     * Profile page
     */
    public function profile()
    {
        $user = $this->get_user();
        $this->data['user_detail'] = $this->User_model->find($user['id']);
        // Check if user has password
        $this->data['has_password'] = $this->User_model->has_password($user['id']);
        $this->data['title'] = 'Profil - incatat.id';
        $this->data['page'] = 'profile';

        $this->load->view('dashboard/profile', $this->data);
    }

    /**
     * Update profile
     */
    public function update_profile()
    {
        $user = $this->get_user();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', 'Nama', 'required|min_length[2]|max_length[255]');
        $this->form_validation->set_rules('phone', 'Nomor Telepon', 'required|min_length[10]|max_length[20]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('dashboard/profile');
        }

        $update_data = array(
            'name' => $this->input->post('name'),
            'phone' => $this->input->post('phone'),
            'telegram_user_id' => $this->input->post('telegram_user_id')
        );

        $updated_user = $this->User_model->update_profile($user['id'], $update_data);

        // Update session data
        $this->session->set_userdata($this->config->item('auth_user_key'), array(
            'id' => $updated_user->id,
            'email' => $updated_user->email,
            'name' => $updated_user->name,
            'avatar' => $updated_user->avatar,
            'profile_completed' => $updated_user->profile_completed
        ));

        $this->session->set_flashdata('success', 'Profil berhasil diperbarui!');
        redirect('dashboard/profile');
    }

    /**
     * Transactions page
     */
    public function transactions()
    {
        $user = $this->get_user();

        // Get filters from query string
        $filters = array(
            'type' => $this->input->get('type'),
            'category_id' => $this->input->get('category'),
            'date_from' => $this->input->get('from'),
            'date_to' => $this->input->get('to'),
            'limit' => 20,
            'offset' => (int) $this->input->get('offset')
        );

        $this->data['transactions'] = $this->Transaction_model->get_by_user($user['id'], $filters);
        $this->data['categories'] = $this->Category_model->get_by_user($user['id']);
        $this->data['filters'] = $filters;
        $this->data['title'] = 'Transaksi - incatat.id';
        $this->data['page'] = 'transactions';

        $this->load->view('dashboard/transactions', $this->data);
    }

    /**
     * Add transaction (AJAX) - supports multiple items
     */
    public function add_transaction()
    {
        $user = $this->get_user();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('type', 'Tipe', 'required|in_list[income,expense]');
        $this->form_validation->set_rules('transaction_date', 'Tanggal', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => validation_errors())));
            return;
        }

        // Get items from form
        $items = $this->input->post('items');

        if (empty($items) || !is_array($items)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => 'Minimal harus ada 1 item')));
            return;
        }

        // Format items array
        $formatted_items = array();
        foreach ($items as $item) {
            if (!empty($item['name']) && !empty($item['price'])) {
                $formatted_items[] = array(
                    'name' => $item['name'],
                    'category_id' => !empty($item['category_id']) ? $item['category_id'] : null,
                    'qty' => !empty($item['qty']) ? (int) $item['qty'] : 1,
                    'price' => (float) $item['price']
                );
            }
        }

        if (empty($formatted_items)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => 'Item tidak valid')));
            return;
        }

        $transaction_data = array(
            'user_id' => $user['id'],
            'type' => $this->input->post('type'),
            'transaction_date' => $this->input->post('transaction_date'),
            'store_name' => $this->input->post('store_name'),
            'notes' => $this->input->post('notes'),
            'source' => 'web',
            'items' => $formatted_items
        );

        $transaction_id = $this->Transaction_model->create($transaction_data);

        if (!$transaction_id) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => 'Gagal menyimpan transaksi')));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                        'success' => true,
                        'message' => 'Transaksi berhasil ditambahkan!',
                        'id' => $transaction_id
                    )));
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $user = $this->get_user();
        $this->data['user_detail'] = $this->User_model->find($user['id']);

        // Get business info if user has one
        $this->load->model('Business_model');
        $this->data['business'] = null;
        if ($this->data['user_detail']->business_id) {
            $this->data['business'] = $this->Business_model->find($this->data['user_detail']->business_id);
        }

        // Get telegram accounts
        $this->load->model('Telegram_account_model');
        $this->data['telegram_accounts'] = $this->Telegram_account_model->get_by_user($user['id']);

        $this->data['title'] = 'Pengaturan - incatat.id';
        $this->data['page'] = 'settings';

        $this->load->view('dashboard/settings', $this->data);
    }

    /**
     * Regenerate API token
     */
    public function regenerate_token()
    {
        $user = $this->get_user();
        $new_token = $this->User_model->generate_api_token($user['id']);

        $this->session->set_flashdata('success', 'API Token berhasil diperbarui!');
        redirect('dashboard/settings');
    }

    /**
     * Update business info (owner only)
     */
    public function update_business()
    {
        $user = $this->get_user();
        $user_detail = $this->User_model->find($user['id']);

        // Check if user is owner
        if ($user_detail->role !== 'owner') {
            $this->session->set_flashdata('error', 'Hanya owner yang dapat mengubah info bisnis');
            redirect('dashboard/settings');
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('business_name', 'Nama Bisnis', 'required|min_length[2]|max_length[255]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('dashboard/settings');
            return;
        }

        $this->load->model('Business_model');
        $this->Business_model->update($user_detail->business_id, array(
            'name' => $this->input->post('business_name'),
            'phone' => $this->input->post('business_phone'),
            'address' => $this->input->post('business_address')
        ));

        $this->session->set_flashdata('success', 'Info bisnis berhasil diperbarui!');
        redirect('dashboard/settings');
    }

    /**
     * Set or change password
     */
    public function set_password()
    {
        $user = $this->get_user();
        $has_password = $this->User_model->has_password($user['id']);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[new_password]');

        // If user has password, require current password
        if ($has_password) {
            $this->form_validation->set_rules('current_password', 'Password Lama', 'required');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('dashboard/profile');
            return;
        }

        // Verify current password if changing
        if ($has_password) {
            $user_detail = $this->User_model->find($user['id']);
            if (!password_verify($this->input->post('current_password'), $user_detail->password)) {
                $this->session->set_flashdata('error', 'Password lama salah.');
                redirect('dashboard/profile');
                return;
            }
        }

        // Set new password
        $this->User_model->set_password($user['id'], $this->input->post('new_password'));

        $this->session->set_flashdata('success', 'Password berhasil disimpan! Sekarang Anda bisa login menggunakan email dan password.');
        redirect('dashboard/profile');
    }

    /**
     * Remove telegram account
     */
    public function remove_telegram($account_id)
    {
        $user = $this->get_user();

        $this->load->model('Telegram_account_model');

        // Find the account
        $account = $this->Telegram_account_model->find($account_id);

        if (!$account || $account->user_id != $user['id']) {
            $this->session->set_flashdata('error', 'Akun Telegram tidak ditemukan.');
            redirect('dashboard/settings');
            return;
        }

        // Delete the account
        $this->Telegram_account_model->delete($account_id);

        $this->session->set_flashdata('success', 'Akun Telegram berhasil dilepas.');
        redirect('dashboard/settings');
    }
}

