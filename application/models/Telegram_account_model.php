<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Telegram Account Model
 * 
 * Handles telegram account associations with users.
 * Supports multiple telegram accounts per user.
 */
class Telegram_account_model extends CI_Model
{
    protected $table = 'telegram_accounts';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all telegram accounts for a user
     */
    public function get_by_user($user_id)
    {
        return $this->db->where('user_id', $user_id)
            ->order_by('is_primary', 'DESC')
            ->order_by('created_at', 'ASC')
            ->get($this->table)
            ->result();
    }

    /**
     * Get telegram accounts by business (all users in business)
     */
    public function get_by_business($business_id)
    {
        return $this->db->select('telegram_accounts.*, users.name as user_name')
            ->from($this->table)
            ->join('users', 'users.id = telegram_accounts.user_id')
            ->where('users.business_id', $business_id)
            ->order_by('telegram_accounts.created_at', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Find by telegram user ID
     * Returns the account with user info
     */
    public function find_by_telegram_id($telegram_user_id)
    {
        return $this->db->select('telegram_accounts.*, users.name as user_name, users.email, users.api_token, users.business_id')
            ->from($this->table)
            ->join('users', 'users.id = telegram_accounts.user_id')
            ->where('telegram_accounts.telegram_user_id', $telegram_user_id)
            ->get()
            ->row();
    }

    /**
     * Find by ID
     */
    public function find($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    /**
     * Create new telegram account link
     */
    public function create($data)
    {
        $insert_data = array(
            'user_id' => $data['user_id'],
            'telegram_user_id' => $data['telegram_user_id'],
            'telegram_username' => isset($data['telegram_username']) ? $data['telegram_username'] : null,
            'telegram_first_name' => isset($data['telegram_first_name']) ? $data['telegram_first_name'] : null,
            'label' => isset($data['label']) ? $data['label'] : null,
            'is_primary' => isset($data['is_primary']) ? $data['is_primary'] : 0,
            'verified_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        );

        // If this is the first account for user, make it primary
        $existing = $this->get_by_user($data['user_id']);
        if (empty($existing)) {
            $insert_data['is_primary'] = 1;
        }

        $this->db->insert($this->table, $insert_data);
        return $this->db->insert_id();
    }

    /**
     * Update telegram account
     */
    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Delete telegram account
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    /**
     * Delete by telegram user ID
     */
    public function delete_by_telegram_id($telegram_user_id)
    {
        return $this->db->where('telegram_user_id', $telegram_user_id)->delete($this->table);
    }

    /**
     * Set primary account
     */
    public function set_primary($id, $user_id)
    {
        // Remove primary from all
        $this->db->where('user_id', $user_id)->update($this->table, array('is_primary' => 0));

        // Set this one as primary
        return $this->db->where('id', $id)->update($this->table, array('is_primary' => 1));
    }

    /**
     * Get primary account for user
     */
    public function get_primary($user_id)
    {
        return $this->db->where('user_id', $user_id)
            ->where('is_primary', 1)
            ->get($this->table)
            ->row();
    }

    /**
     * Count accounts for user
     */
    public function count_by_user($user_id)
    {
        return $this->db->where('user_id', $user_id)->count_all_results($this->table);
    }

    /**
     * Check if telegram ID is already registered
     */
    public function is_telegram_id_registered($telegram_user_id, $exclude_user_id = null)
    {
        $this->db->where('telegram_user_id', $telegram_user_id);

        if ($exclude_user_id) {
            $this->db->where('user_id !=', $exclude_user_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }
}
