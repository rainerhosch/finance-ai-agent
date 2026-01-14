<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Category Model
 * 
 * Handles transaction categories (income/expense types).
 */
class Category_model extends CI_Model
{
    protected $table = 'categories';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all categories (default + user custom)
     */
    public function get_all($user_id = null, $type = null)
    {
        $this->db->where('(user_id IS NULL OR user_id = ' . (int) $user_id . ')');

        if ($type) {
            $this->db->where('type', $type);
        }

        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('name', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Get categories for a specific user
     */
    public function get_by_user($user_id, $type = null)
    {
        return $this->get_all($user_id, $type);
    }

    /**
     * Find category by ID
     */
    public function find($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }

    /**
     * Create a new category
     */
    public function create($data)
    {
        $insert_data = array(
            'user_id' => isset($data['user_id']) ? $data['user_id'] : null,
            'name' => $data['name'],
            'type' => $data['type'],
            'icon' => isset($data['icon']) ? $data['icon'] : '📌',
            'is_default' => 0,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $insert_data);
        return $this->db->insert_id();
    }

    /**
     * Get default categories
     */
    public function get_defaults($type = null)
    {
        $this->db->where('is_default', 1);

        if ($type) {
            $this->db->where('type', $type);
        }

        return $this->db->get($this->table)->result();
    }

    /**
     * Get income categories
     */
    public function get_income_categories($user_id = null)
    {
        return $this->get_all($user_id, 'income');
    }

    /**
     * Get expense categories
     */
    public function get_expense_categories($user_id = null)
    {
        return $this->get_all($user_id, 'expense');
    }
}
