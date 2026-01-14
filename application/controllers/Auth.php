<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller
 * 
 * Handles Google OAuth authentication flow.
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
     * Login - redirect to Google OAuth
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

            // Set session
            $this->session->set_userdata(array(
                $this->config->item('auth_session_key') => TRUE,
                $this->config->item('auth_user_key') => array(
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'profile_completed' => $user->profile_completed
                )
            ));

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
