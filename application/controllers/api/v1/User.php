<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API User Controller
 * 
 * Handles user-related API endpoints for Telegram Bot.
 */
class User extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Verify if user is registered
     * GET /api/v1/user/verify?telegram_id=xxx OR ?email=xxx
     */
    public function verify()
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
                    'api_token' => $user->api_token,
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
     * POST /api/v1/user/link-telegram
     * Body: { "email": "xxx", "telegram_id": "xxx" }
     */
    public function link_telegram()
    {
        $input = $this->get_json_input();

        if (empty($input['email']) && empty($input['telegram_id'])) {
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

        // Return updated user with API token for future authenticated requests
        $updated_user = $this->User_model->find($user->id);

        $this->json_response(array(
            'success' => true,
            'message' => 'Akun Telegram berhasil dihubungkan!',
            'api_token' => $updated_user->api_token
        ));
    }

    /**
     * Get user profile
     * GET /api/v1/user/profile
     * Requires Bearer token authentication
     */
    public function profile()
    {
        if (!$this->authenticate()) {
            return;
        }

        $this->json_response(array(
            'success' => true,
            'user' => array(
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'telegram_linked' => !empty($this->user->telegram_user_id),
                'profile_completed' => (bool) $this->user->profile_completed
            )
        ));
    }
}
