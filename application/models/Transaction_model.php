<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Transaction Model
 * 
 * Handles all transaction (income/expense) operations.
 */
class Transaction_model extends CI_Model
{
    protected $table = 'transactions';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Find transaction by ID
     */
    public function find($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }

    /**
     * Get transactions by user with optional filters
     */
    public function get_by_user($user_id, $filters = array())
    {
        $this->db->select('transactions.*, categories.name as category_name, categories.icon as category_icon');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = transactions.category_id', 'left');
        $this->db->where('transactions.user_id', $user_id);

        // Apply filters
        if (!empty($filters['type'])) {
            $this->db->where('transactions.type', $filters['type']);
        }

        if (!empty($filters['category_id'])) {
            $this->db->where('transactions.category_id', $filters['category_id']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('transactions.transaction_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('transactions.transaction_date <=', $filters['date_to']);
        }

        if (!empty($filters['source'])) {
            $this->db->where('transactions.source', $filters['source']);
        }

        // Sorting
        $sort_by = isset($filters['sort_by']) ? $filters['sort_by'] : 'transaction_date';
        $sort_order = isset($filters['sort_order']) ? $filters['sort_order'] : 'DESC';
        $this->db->order_by('transactions.' . $sort_by, $sort_order);

        // Pagination
        if (!empty($filters['limit'])) {
            $offset = isset($filters['offset']) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Get summary (totals) for a user
     */
    public function get_summary($user_id, $period = null)
    {
        $this->db->select('type, SUM(amount) as total, COUNT(*) as count');
        $this->db->from($this->table);
        $this->db->where('user_id', $user_id);

        // Apply period filter
        if ($period) {
            switch ($period) {
                case 'today':
                    $this->db->where('transaction_date', date('Y-m-d'));
                    break;
                case 'week':
                    $this->db->where('transaction_date >=', date('Y-m-d', strtotime('-7 days')));
                    break;
                case 'month':
                    $this->db->where('MONTH(transaction_date)', date('m'));
                    $this->db->where('YEAR(transaction_date)', date('Y'));
                    break;
                case 'year':
                    $this->db->where('YEAR(transaction_date)', date('Y'));
                    break;
            }
        }

        $this->db->group_by('type');
        $results = $this->db->get()->result();

        $summary = array(
            'income' => 0,
            'expense' => 0,
            'income_count' => 0,
            'expense_count' => 0,
            'balance' => 0
        );

        foreach ($results as $row) {
            $summary[$row->type] = (float) $row->total;
            $summary[$row->type . '_count'] = (int) $row->count;
        }

        $summary['balance'] = $summary['income'] - $summary['expense'];

        return $summary;
    }

    /**
     * Get monthly summary for chart
     */
    public function get_monthly_summary($user_id, $months = 6)
    {
        $this->db->select('
            DATE_FORMAT(transaction_date, "%Y-%m") as month,
            type,
            SUM(amount) as total
        ');
        $this->db->from($this->table);
        $this->db->where('user_id', $user_id);
        $this->db->where('transaction_date >=', date('Y-m-01', strtotime("-$months months")));
        $this->db->group_by(array('month', 'type'));
        $this->db->order_by('month', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Create a new transaction
     */
    public function create($data)
    {
        $insert_data = array(
            'user_id' => $data['user_id'],
            'category_id' => isset($data['category_id']) ? $data['category_id'] : null,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'source' => isset($data['source']) ? $data['source'] : 'web',
            'attachment_url' => isset($data['attachment_url']) ? $data['attachment_url'] : null,
            'transaction_date' => isset($data['transaction_date']) ? $data['transaction_date'] : date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $insert_data);
        return $this->db->insert_id();
    }

    /**
     * Update a transaction
     */
    public function update($id, $data)
    {
        $allowed_fields = array('category_id', 'type', 'amount', 'description', 'attachment_url', 'transaction_date');
        $update_data = array();

        foreach ($allowed_fields as $field) {
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
     * Delete a transaction
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Get recent transactions
     */
    public function get_recent($user_id, $limit = 5)
    {
        return $this->get_by_user($user_id, array('limit' => $limit));
    }

    /**
     * Get top expense categories
     */
    public function get_top_categories($user_id, $type = 'expense', $limit = 5)
    {
        $this->db->select('categories.name, categories.icon, SUM(transactions.amount) as total');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = transactions.category_id', 'left');
        $this->db->where('transactions.user_id', $user_id);
        $this->db->where('transactions.type', $type);
        $this->db->where('MONTH(transactions.transaction_date)', date('m'));
        $this->db->where('YEAR(transactions.transaction_date)', date('Y'));
        $this->db->group_by('transactions.category_id');
        $this->db->order_by('total', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }
}
