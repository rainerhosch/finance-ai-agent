<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Model
 * 
 * Handles all user-related database operations including
 * OAuth authentication and Telegram integration.
 */
class User_model extends CI_Model
{
    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Find user by ID
     */
    public function find($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }

    /**
     * Find user by email
     */
    public function find_by_email($email)
    {
        return $this->db->get_where($this->table, array('email' => $email))->row();
    }

    /**
     * Find user by Google ID
     */
    public function find_by_google_id($google_id)
    {
        return $this->db->get_where($this->table, array('google_id' => $google_id))->row();
    }

    /**
     * Find user by Telegram ID
     */
    public function find_by_telegram_id($telegram_id)
    {
        return $this->db->get_where($this->table, array('telegram_user_id' => $telegram_id))->row();
    }

    /**
     * Find user by API token
     */
    public function find_by_api_token($token)
    {
        if (empty($token)) {
            return null;
        }
        return $this->db->get_where($this->table, array('api_token' => $token))->row();
    }

    /**
     * Create or update user from OAuth data
     */
    public function create_or_update_from_oauth($data)
    {
        $existing = $this->find_by_google_id($data['google_id']);

        if ($existing) {
            // Update existing user
            $update_data = array(
                'name' => $data['name'],
                'email' => $data['email'],
                'avatar' => isset($data['avatar']) ? $data['avatar'] : null,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $this->db->where('id', $existing->id);
            $this->db->update($this->table, $update_data);

            return $this->find($existing->id);
        } else {
            // Create new user
            $insert_data = array(
                'google_id' => $data['google_id'],
                'email' => $data['email'],
                'name' => $data['name'],
                'avatar' => isset($data['avatar']) ? $data['avatar'] : null,
                'profile_completed' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            $this->db->insert($this->table, $insert_data);
            $user_id = $this->db->insert_id();

            // Generate API token for new user
            $this->generate_api_token($user_id);

            return $this->find($user_id);
        }
    }

    /**
     * Update user profile
     */
    public function update_profile($user_id, $data)
    {
        $allowed_fields = array('name', 'phone', 'telegram_user_id');
        $update_data = array();

        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $update_data[$field] = $data[$field];
            }
        }

        if (!empty($update_data)) {
            $update_data['updated_at'] = date('Y-m-d H:i:s');

            // Check if profile is complete
            $user = $this->find($user_id);
            if ($user && !empty($user->name) && !empty($data['phone'])) {
                $update_data['profile_completed'] = 1;
            }

            $this->db->where('id', $user_id);
            $this->db->update($this->table, $update_data);
        }

        return $this->find($user_id);
    }

    /**
     * Generate API token for user
     */
    public function generate_api_token($user_id)
    {
        $token = bin2hex(random_bytes(32)); // 64 character token

        $this->db->where('id', $user_id);
        $this->db->update($this->table, array(
            'api_token' => $token,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        return $token;
    }

    /**
     * Link Telegram account to user
     */
    public function link_telegram($user_id, $telegram_user_id)
    {
        $this->db->where('id', $user_id);
        $this->db->update($this->table, array(
            'telegram_user_id' => $telegram_user_id,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        return $this->find($user_id);
    }
}
