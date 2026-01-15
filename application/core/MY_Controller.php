<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller - Base Controller
 * 
 * Provides common functionality for all controllers including
 * authentication checking and view loading helpers.
 */
class MY_Controller extends CI_Controller
{
    protected $data = array();
    protected $layout = 'layout/main';

    public function __construct()
    {
        parent::__construct();
        $this->load->config('auth');
    }

    /**
     * Check if user is logged in
     */
    protected function is_logged_in()
    {
        return $this->session->userdata($this->config->item('auth_session_key')) === TRUE;
    }

    /**
     * Get current user data
     */
    protected function get_user()
    {
        return $this->session->userdata($this->config->item('auth_user_key'));
    }

    /**
     * Require authentication - redirect to login if not authenticated
     */
    protected function require_auth()
    {
        if (!$this->is_logged_in()) {
            redirect('login');
        }
    }

    /**
     * Load view with layout
     */
    protected function render($view, $data = array())
    {
        $this->data = array_merge($this->data, $data);
        $this->data['user'] = $this->get_user();
        $this->data['is_logged_in'] = $this->is_logged_in();

        $this->load->view($view, $this->data);
    }
}

/**
 * Auth_Controller - For authenticated pages (Dashboard)
 */
class Auth_Controller extends MY_Controller
{
    protected $layout = 'layout/dashboard';

    public function __construct()
    {
        parent::__construct();
        $this->require_auth();

        $this->load->model('User_model');
        $this->load->model('Transaction_model');
        $this->load->model('Category_model');
    }
}

/**
 * API_Controller - For API endpoints
 */
class API_Controller extends CI_Controller
{
    protected $user = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    /**
     * Authenticate API request via Bearer token
     */
    protected function authenticate()
    {
        // First try Bearer token
        $auth_header = $this->input->get_request_header('Authorization');

        if (!empty($auth_header) && preg_match('/^Bearer\s+(.+)$/', $auth_header, $matches)) {
            $token = $matches[1];
            $this->user = $this->User_model->find_by_api_token($token);
            if ($this->user) {
                return true;
            }
        }

        // Then try telegram_id from request body or query string
        $input = $this->get_json_input();
        $telegram_id = isset($input['telegram_id']) ? $input['telegram_id'] : $this->input->get('telegram_id');

        if (!empty($telegram_id)) {
            $this->user = $this->User_model->find_by_telegram_id($telegram_id);
            if ($this->user) {
                return true;
            }
        }

        $this->json_response(['error' => 'Authentication required. Provide Bearer token or telegram_id'], 401);
        return false;
    }

    /**
     * Authenticate via telegram_id only (for bot requests)
     */
    protected function authenticate_telegram()
    {
        $input = $this->get_json_input();
        $telegram_id = isset($input['telegram_id']) ? $input['telegram_id'] : $this->input->get('telegram_id');

        if (empty($telegram_id)) {
            $this->json_response(['error' => 'telegram_id diperlukan'], 400);
            return false;
        }

        $this->user = $this->User_model->find_by_telegram_id($telegram_id);

        if (!$this->user) {
            $this->json_response(['error' => 'User tidak ditemukan. Hubungkan Telegram di website terlebih dahulu.'], 404);
            return false;
        }

        return true;
    }

    /**
     * Send JSON response
     */
    protected function json_response($data, $status_code = 200)
    {
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /**
     * Get JSON input from request body
     */
    protected function get_json_input()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?: array();
    }
}
