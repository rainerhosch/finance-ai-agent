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
        $this->data['categories'] = $this->Category_model->get_all($user['id']);

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
        $this->data['categories'] = $this->Category_model->get_all($user['id']);
        $this->data['filters'] = $filters;
        $this->data['title'] = 'Transaksi - incatat.id';
        $this->data['page'] = 'transactions';

        $this->load->view('dashboard/transactions', $this->data);
    }

    /**
     * Add transaction (AJAX)
     */
    public function add_transaction()
    {
        $user = $this->get_user();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('type', 'Tipe', 'required|in_list[income,expense]');
        $this->form_validation->set_rules('amount', 'Jumlah', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('transaction_date', 'Tanggal', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => validation_errors())));
            return;
        }

        $transaction_data = array(
            'user_id' => $user['id'],
            'category_id' => $this->input->post('category_id'),
            'type' => $this->input->post('type'),
            'amount' => $this->input->post('amount'),
            'description' => $this->input->post('description'),
            'transaction_date' => $this->input->post('transaction_date'),
            'source' => 'web'
        );

        $transaction_id = $this->Transaction_model->create($transaction_data);

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
}

