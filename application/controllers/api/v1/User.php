<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API User Controller
 * 
 * Handles user-related API endpoints for Telegram Bot.
 * Updated to use telegram_accounts table for multi-account support.
 */
class User extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Telegram_account_model');
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
        $telegram_account = null;

        if (!empty($telegram_id)) {
            // Check telegram_accounts table first
            $telegram_account = $this->Telegram_account_model->find_by_telegram_id($telegram_id);
            if ($telegram_account) {
                $user = $this->User_model->find($telegram_account->user_id);
            } else {
                // Fallback to old telegram_user_id in users table for backward compatibility
                $user = $this->User_model->find_by_telegram_id($telegram_id);
            }
        } elseif (!empty($email)) {
            $user = $this->User_model->find_by_email($email);
        }

        if ($user) {
            // Get all telegram accounts for this user
            $telegram_accounts = $this->Telegram_account_model->get_by_user($user->id);

            $this->json_response(array(
                'registered' => true,
                'user' => array(
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'api_token' => $user->api_token,
                    'business_id' => $user->business_id,
                    'telegram_linked' => (!empty($user->telegram_user_id) || count($telegram_accounts) > 0),
                    'telegram_accounts' => array_map(function ($acc) {
                        return array(
                            'telegram_id' => $acc->telegram_user_id,
                            'username' => $acc->telegram_username,
                            'first_name' => $acc->telegram_first_name,
                            'label' => $acc->label,
                            'is_primary' => (bool) $acc->is_primary
                        );
                    }, $telegram_accounts),
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
     * Body: { "email": "xxx", "telegram_id": "xxx", "username": "xxx", "first_name": "xxx", "label": "xxx" }
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

        // Check if telegram_id is already linked
        if ($this->Telegram_account_model->is_telegram_id_registered($input['telegram_id'])) {
            $this->json_response(array(
                'success' => false,
                'message' => 'Telegram ID sudah terhubung ke akun lain'
            ), 409);
            return;
        }

        // Create telegram account link
        $account_id = $this->Telegram_account_model->create(array(
            'user_id' => $user->id,
            'telegram_user_id' => $input['telegram_id'],
            'telegram_username' => isset($input['username']) ? $input['username'] : null,
            'telegram_first_name' => isset($input['first_name']) ? $input['first_name'] : null,
            'label' => isset($input['label']) ? $input['label'] : null
        ));

        // Return updated user with API token
        $this->json_response(array(
            'success' => true,
            'message' => 'Akun Telegram berhasil dihubungkan!',
            'api_token' => $user->api_token,
            'account_id' => $account_id
        ));
    }

    /**
     * Unlink Telegram account
     * POST /api/v1/user/unlink-telegram
     * Body: { "telegram_id": "xxx" }
     */
    public function unlink_telegram()
    {
        $input = $this->get_json_input();

        if (empty($input['telegram_id'])) {
            $this->json_response(array(
                'error' => 'Parameter telegram_id diperlukan'
            ), 400);
            return;
        }

        $account = $this->Telegram_account_model->find_by_telegram_id($input['telegram_id']);

        if (!$account) {
            $this->json_response(array(
                'success' => false,
                'message' => 'Akun Telegram tidak ditemukan'
            ), 404);
            return;
        }

        $this->Telegram_account_model->delete_by_telegram_id($input['telegram_id']);

        $this->json_response(array(
            'success' => true,
            'message' => 'Akun Telegram berhasil dilepas'
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

        // Get telegram accounts
        $telegram_accounts = $this->Telegram_account_model->get_by_user($this->user->id);

        $this->json_response(array(
            'success' => true,
            'user' => array(
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'business_id' => $this->user->business_id,
                'role' => $this->user->role,
                'telegram_accounts' => array_map(function ($acc) {
                    return array(
                        'id' => $acc->id,
                        'telegram_id' => $acc->telegram_user_id,
                        'username' => $acc->telegram_username,
                        'first_name' => $acc->telegram_first_name,
                        'label' => $acc->label,
                        'is_primary' => (bool) $acc->is_primary
                    );
                }, $telegram_accounts),
                'profile_completed' => (bool) $this->user->profile_completed
            )
        ));
    }
}
