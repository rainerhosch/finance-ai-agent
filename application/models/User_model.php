<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Model
 * 
 * Handles all user-related database operations including
 * OAuth authentication, Telegram integration, and business membership.
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
     * Find user by ID with business info
     */
    public function find_with_business($id)
    {
        $this->db->select('users.*, businesses.name as business_name');
        $this->db->from($this->table);
        $this->db->join('businesses', 'businesses.id = users.business_id', 'left');
        $this->db->where('users.id', $id);
        return $this->db->get()->row();
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
     * Find user by Telegram ID (checks telegram_accounts table)
     * Returns user object if found in telegram_accounts, falls back to users table for backward compatibility
     */
    public function find_by_telegram_id($telegram_id)
    {
        // First check telegram_accounts table (new method)
        $this->load->model('Telegram_account_model');
        $account = $this->Telegram_account_model->find_by_telegram_id($telegram_id);

        if ($account) {
            return $this->find($account->user_id);
        }

        // Fallback to old telegram_user_id in users table (backward compatibility)
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
            // Create new user with their own business
            $this->load->model('Business_model');

            // Create a default business for the user
            $business_id = $this->Business_model->create(array(
                'name' => $data['name'] . "'s Business"
            ));

            // Create new user
            $insert_data = array(
                'business_id' => $business_id,
                'google_id' => $data['google_id'],
                'email' => $data['email'],
                'name' => $data['name'],
                'avatar' => isset($data['avatar']) ? $data['avatar'] : null,
                'role' => 'owner',
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
     * Link Telegram account to user (saves to telegram_accounts table)
     */
    public function link_telegram($user_id, $telegram_user_id, $telegram_data = array())
    {
        $this->load->model('Telegram_account_model');

        // Create entry in telegram_accounts table
        return $this->Telegram_account_model->create(array(
            'user_id' => $user_id,
            'telegram_user_id' => $telegram_user_id,
            'telegram_username' => isset($telegram_data['username']) ? $telegram_data['username'] : null,
            'telegram_first_name' => isset($telegram_data['first_name']) ? $telegram_data['first_name'] : null,
            'label' => isset($telegram_data['label']) ? $telegram_data['label'] : null
        ));
    }

    /**
     * Get user's business
     */
    public function get_business($user_id)
    {
        $user = $this->find($user_id);
        if ($user && $user->business_id) {
            $this->load->model('Business_model');
            return $this->Business_model->find($user->business_id);
        }
        return null;
    }

    /**
     * Set user's business
     */
    public function set_business($user_id, $business_id, $role = 'staff')
    {
        $this->db->where('id', $user_id);
        $this->db->update($this->table, array(
            'business_id' => $business_id,
            'role' => $role,
            'updated_at' => date('Y-m-d H:i:s')
        ));
        return $this->find($user_id);
    }

    /**
     * Set password for user
     */
    public function set_password($user_id, $password)
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $this->db->where('id', $user_id);
        $this->db->update($this->table, array(
            'password' => $hashed,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        return true;
    }

    /**
     * Verify password login
     */
    public function verify_password($email, $password)
    {
        $user = $this->find_by_email($email);

        if (!$user || empty($user->password)) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            return $user;
        }

        return false;
    }

    /**
     * Check if user has password set
     */
    public function has_password($user_id)
    {
        $user = $this->find($user_id);
        return $user && !empty($user->password);
    }

    /**
     * Download and save avatar locally
     */
    public function download_avatar($user_id, $google_avatar_url)
    {
        if (empty($google_avatar_url)) {
            return null;
        }

        // Create uploads directory if not exists
        $upload_dir = FCPATH . 'assets/uploads/avatars/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generate filename
        $filename = 'avatar_' . $user_id . '_' . time() . '.jpg';
        $filepath = $upload_dir . $filename;

        // Download image from Google
        $ch = curl_init($google_avatar_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $image_data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200 || empty($image_data)) {
            log_message('error', 'Failed to download avatar from: ' . $google_avatar_url);
            return null;
        }

        // Save to file
        if (file_put_contents($filepath, $image_data) === false) {
            log_message('error', 'Failed to save avatar to: ' . $filepath);
            return null;
        }

        // Update user avatar path
        $local_path = 'assets/uploads/avatars/' . $filename;
        $this->db->where('id', $user_id);
        $this->db->update($this->table, array(
            'avatar' => $local_path,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        return $local_path;
    }

    /**
     * Get avatar URL (with fallback)
     */
    public function get_avatar_url($user)
    {
        if (!empty($user->avatar)) {
            // Check if it's a local path
            if (strpos($user->avatar, 'http') === 0) {
                return $user->avatar; // External URL
            }
            return base_url($user->avatar); // Local path
        }
        // Default avatar
        return base_url('assets/img/default-avatar.png');
    }
}

