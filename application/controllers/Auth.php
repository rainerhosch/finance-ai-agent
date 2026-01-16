<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller
 * 
 * Handles Google OAuth and password authentication.
 */
class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('Google_auth');
    }

    /**
     * Login page - show login form
     */
    public function login_form()
    {
        if ($this->is_logged_in()) {
            redirect('dashboard');
        }

        $data = array(
            'title' => 'Login - incatat.id'
        );

        $this->load->view('auth/login', $data);
    }

    /**
     * Login with Google - redirect to OAuth
     */
    public function login()
    {
        if ($this->is_logged_in()) {
            redirect('dashboard');
        }

        $auth_url = $this->google_auth->get_auth_url();
        redirect($auth_url);
    }

    /**
     * Login with email/password
     */
    public function login_password()
    {
        if ($this->is_logged_in()) {
            redirect('dashboard');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('login');
            return;
        }

        $email = $this->input->post('email');
        $password = $this->input->post('password');

        // Verify password
        $user = $this->User_model->verify_password($email, $password);

        if (!$user) {
            $this->session->set_flashdata('error', 'Email atau password salah.');
            redirect('login');
            return;
        }

        // Set session
        $this->_set_user_session($user);

        // Redirect
        if (!$user->profile_completed) {
            redirect('dashboard/profile');
        }

        redirect('dashboard');
    }

    /**
     * OAuth callback handler
     */
    public function callback()
    {
        $code = $this->input->get('code');

        if (empty($code)) {
            $this->session->set_flashdata('error', 'Autentikasi dibatalkan atau gagal.');
            redirect('/');
        }

        try {
            // Exchange code for token and get user info
            $google_user = $this->google_auth->authenticate($code);

            if (!$google_user) {
                throw new Exception('Gagal mendapatkan data pengguna dari Google.');
            }

            // Create or update user in database
            $user = $this->User_model->create_or_update_from_oauth(array(
                'google_id' => $google_user['id'],
                'email' => $google_user['email'],
                'name' => $google_user['name'],
                'avatar' => isset($google_user['picture']) ? $google_user['picture'] : null
            ));

            // Download avatar locally to avoid 429 rate limiting
            if (!empty($google_user['picture'])) {
                $local_avatar = $this->User_model->download_avatar($user->id, $google_user['picture']);
                if ($local_avatar) {
                    $user->avatar = $local_avatar;
                }
            }

            // Set session
            $this->_set_user_session($user);

            // Redirect based on profile completion
            if (!$user->profile_completed) {
                redirect('dashboard/profile');
            }

            redirect('dashboard');

        } catch (Exception $e) {
            log_message('error', 'Google OAuth Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
            redirect('/');
        }
    }

    /**
     * Set user session data
     */
    private function _set_user_session($user)
    {
        // Get avatar URL
        $avatar_url = $this->User_model->get_avatar_url($user);

        $this->session->set_userdata(array(
            $this->config->item('auth_session_key') => TRUE,
            $this->config->item('auth_user_key') => array(
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'avatar' => $avatar_url,
                'profile_completed' => $user->profile_completed
            )
        ));
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->unset_userdata($this->config->item('auth_session_key'));
        $this->session->unset_userdata($this->config->item('auth_user_key'));
        $this->session->sess_destroy();

        redirect('/');
    }
}
