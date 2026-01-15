<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Transaction Item Model
 * 
 * Handles individual items within a transaction.
 */
class Transaction_item_model extends CI_Model
{
    protected $table = 'transaction_items';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Find item by ID
     */
    public function find($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }

    /**
     * Get items by transaction
     */
    public function get_by_transaction($transaction_id)
    {
        $this->db->select('transaction_items.*, categories.name as category_name, categories.icon as category_icon');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = transaction_items.category_id', 'left');
        $this->db->where('transaction_items.transaction_id', $transaction_id);
        $this->db->order_by('transaction_items.id', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Create item
     */
    public function create($data)
    {
        $qty = isset($data['qty']) ? $data['qty'] : 1;
        $price = isset($data['price']) ? $data['price'] : 0;

        $insert_data = array(
            'transaction_id' => $data['transaction_id'],
            'category_id' => isset($data['category_id']) && !empty($data['category_id']) ? $data['category_id'] : null,
            'name' => $data['name'],
            'qty' => $qty,
            'price' => $price,
            'subtotal' => $qty * $price,
            'notes' => isset($data['notes']) ? $data['notes'] : null,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $insert_data);
        return $this->db->insert_id();
    }

    /**
     * Create multiple items
     */
    public function create_batch($transaction_id, $items)
    {
        $insert_data = array();

        foreach ($items as $item) {
            $qty = isset($item['qty']) ? $item['qty'] : 1;
            $price = isset($item['price']) ? $item['price'] : 0;

            $insert_data[] = array(
                'transaction_id' => $transaction_id,
                'category_id' => isset($item['category_id']) && !empty($item['category_id']) ? $item['category_id'] : null,
                'name' => $item['name'],
                'qty' => $qty,
                'price' => $price,
                'subtotal' => $qty * $price,
                'notes' => isset($item['notes']) ? $item['notes'] : null,
                'created_at' => date('Y-m-d H:i:s')
            );
        }

        if (!empty($insert_data)) {
            $this->db->insert_batch($this->table, $insert_data);
        }

        return count($insert_data);
    }

    /**
     * Update item
     */
    public function update($id, $data)
    {
        $update_data = array();
        $allowed = array('category_id', 'name', 'qty', 'price', 'notes');

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $update_data[$field] = $data[$field];
            }
        }

        if (!empty($update_data)) {
            // Recalculate subtotal if qty or price changed
            if (isset($update_data['qty']) || isset($update_data['price'])) {
                $item = $this->find($id);
                $qty = isset($update_data['qty']) ? $update_data['qty'] : $item->qty;
                $price = isset($update_data['price']) ? $update_data['price'] : $item->price;
                $update_data['subtotal'] = $qty * $price;
            }

            $this->db->where('id', $id);
            $this->db->update($this->table, $update_data);
        }

        return $this->find($id);
    }

    /**
     * Delete item
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Delete all items by transaction
     */
    public function delete_by_transaction($transaction_id)
    {
        $this->db->where('transaction_id', $transaction_id);
        return $this->db->delete($this->table);
    }

    /**
     * Sum total for a transaction
     */
    public function get_total($transaction_id)
    {
        $this->db->select_sum('subtotal');
        $this->db->where('transaction_id', $transaction_id);
        $result = $this->db->get($this->table)->row();
        return $result ? (float) $result->subtotal : 0;
    }
}
