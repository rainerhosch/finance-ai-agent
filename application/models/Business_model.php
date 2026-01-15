<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Business Model
 * 
 * Handles business/store operations.
 */
class Business_model extends CI_Model
{
    protected $table = 'businesses';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Find business by ID
     */
    public function find($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }

    /**
     * Create a new business
     */
    public function create($data)
    {
        $insert_data = array(
            'name' => $data['name'],
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'address' => isset($data['address']) ? $data['address'] : null,
            'logo_url' => isset($data['logo_url']) ? $data['logo_url'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $insert_data);
        return $this->db->insert_id();
    }

    /**
     * Update business
     */
    public function update($id, $data)
    {
        $update_data = array();
        $allowed = array('name', 'phone', 'address', 'logo_url');

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $update_data[$field] = $data[$field];
            }
        }

        if (!empty($update_data)) {
            $update_data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $id);
            $this->db->update($this->table, $update_data);
        }

        return $this->find($id);
    }

    /**
     * Get all users of a business
     */
    public function get_users($business_id)
    {
        return $this->db->get_where('users', array('business_id' => $business_id))->result();
    }

    /**
     * Add user to business
     */
    public function add_user($business_id, $user_id, $role = 'staff')
    {
        $this->db->where('id', $user_id);
        $this->db->update('users', array(
            'business_id' => $business_id,
            'role' => $role,
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Remove user from business
     */
    public function remove_user($user_id)
    {
        $this->db->where('id', $user_id);
        $this->db->update('users', array(
            'business_id' => null,
            'role' => 'owner',
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Delete business
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}
