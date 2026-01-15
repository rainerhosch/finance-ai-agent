<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Category Model
 * 
 * Handles transaction categories (income/expense types).
 * Categories belong to a business or are global defaults.
 */
class Category_model extends CI_Model
{
    protected $table = 'categories';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all categories (default + business custom)
     */
    public function get_all($business_id = null, $type = null)
    {
        if ($business_id) {
            $this->db->where('(business_id IS NULL OR business_id = ' . (int) $business_id . ')');
        } else {
            $this->db->where('business_id IS NULL');
        }

        if ($type) {
            $this->db->where('type', $type);
        }

        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('name', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Get categories for a user (via their business)
     */
    public function get_by_user($user_id, $type = null)
    {
        $this->load->model('User_model');
        $user = $this->User_model->find($user_id);

        if ($user && $user->business_id) {
            return $this->get_all($user->business_id, $type);
        }

        return $this->get_all(null, $type);
    }

    /**
     * Find category by ID
     */
    public function find($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }

    /**
     * Find category by name (case-insensitive)
     */
    public function find_by_name($name, $business_id = null)
    {
        $this->db->where('LOWER(name)', strtolower($name));
        if ($business_id) {
            $this->db->where('(business_id IS NULL OR business_id = ' . (int) $business_id . ')');
        }
        return $this->db->get($this->table)->row();
    }

    /**
     * Create a new category
     */
    public function create($data)
    {
        $insert_data = array(
            'business_id' => isset($data['business_id']) ? $data['business_id'] : null,
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
    public function get_income_categories($business_id = null)
    {
        return $this->get_all($business_id, 'income');
    }

    /**
     * Get expense categories
     */
    public function get_expense_categories($business_id = null)
    {
        return $this->get_all($business_id, 'expense');
    }
}
