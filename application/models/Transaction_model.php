<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Transaction Model
 * 
 * Handles transaction header operations.
 * Each transaction can have multiple items.
 */
class Transaction_model extends CI_Model
{
    protected $table = 'transactions';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaction_item_model');
    }

    /**
     * Find transaction by ID
     */
    public function find($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }

    /**
     * Find transaction with items
     */
    public function find_with_items($id)
    {
        $transaction = $this->find($id);
        if ($transaction) {
            $transaction->items = $this->Transaction_item_model->get_by_transaction($id);
        }
        return $transaction;
    }

    /**
     * Get transactions by business with optional filters
     * Includes computed fields for view compatibility
     */
    public function get_by_business($business_id, $filters = array())
    {
        $this->db->select('transactions.*, users.name as created_by_name');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = transactions.user_id', 'left');
        $this->db->where('transactions.business_id', $business_id);

        // Apply filters
        if (!empty($filters['type'])) {
            $this->db->where('transactions.type', $filters['type']);
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
        $this->db->order_by('transactions.id', 'DESC');

        // Pagination
        if (!empty($filters['limit'])) {
            $offset = isset($filters['offset']) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $transactions = $this->db->get()->result();

        // Add computed fields for backward compatibility
        foreach ($transactions as &$tx) {
            // Map new fields to old field names for view compatibility
            $tx->amount = $tx->total_amount;
            $tx->description = $tx->notes ?: $tx->store_name;

            // Get first item's category info
            $first_item = $this->db->select('transaction_items.*, categories.name as cat_name, categories.icon as cat_icon')
                ->from('transaction_items')
                ->join('categories', 'categories.id = transaction_items.category_id', 'left')
                ->where('transaction_items.transaction_id', $tx->id)
                ->order_by('transaction_items.id', 'ASC')
                ->limit(1)
                ->get()
                ->row();

            if ($first_item) {
                $tx->category_name = $first_item->cat_name;
                $tx->category_icon = $first_item->cat_icon;
                if (!$tx->description) {
                    $tx->description = $first_item->name;
                }
            } else {
                $tx->category_name = null;
                $tx->category_icon = null;
            }

            // Get all items for this transaction
            $tx->items = $this->db->select('transaction_items.*, categories.name as category_name, categories.icon as category_icon')
                ->from('transaction_items')
                ->join('categories', 'categories.id = transaction_items.category_id', 'left')
                ->where('transaction_items.transaction_id', $tx->id)
                ->order_by('transaction_items.id', 'ASC')
                ->get()
                ->result();
        }

        return $transactions;
    }

    /**
     * Get transactions by user (for backward compatibility)
     */
    public function get_by_user($user_id, $filters = array())
    {
        // Get user's business
        $this->load->model('User_model');
        $user = $this->User_model->find($user_id);

        if ($user && $user->business_id) {
            return $this->get_by_business($user->business_id, $filters);
        }

        return array();
    }

    /**
     * Get summary (totals) for a business
     */
    public function get_summary($business_id_or_user_id, $period = null, $is_user_id = true)
    {
        // Determine business_id
        if ($is_user_id) {
            $this->load->model('User_model');
            $user = $this->User_model->find($business_id_or_user_id);
            $business_id = $user ? $user->business_id : null;
        } else {
            $business_id = $business_id_or_user_id;
        }

        if (!$business_id) {
            return array(
                'income' => 0,
                'expense' => 0,
                'income_count' => 0,
                'expense_count' => 0,
                'balance' => 0
            );
        }

        $this->db->select('type, SUM(total_amount) as total, COUNT(*) as count');
        $this->db->from($this->table);
        $this->db->where('business_id', $business_id);

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
        $this->load->model('User_model');
        $user = $this->User_model->find($user_id);

        if (!$user || !$user->business_id) {
            return array();
        }

        $this->db->select('
            DATE_FORMAT(transaction_date, "%Y-%m") as month,
            type,
            SUM(total_amount) as total
        ');
        $this->db->from($this->table);
        $this->db->where('business_id', $user->business_id);
        $this->db->where('transaction_date >=', date('Y-m-01', strtotime("-$months months")));
        $this->db->group_by(array('month', 'type'));
        $this->db->order_by('month', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Create a new transaction with items
     */
    public function create($data)
    {
        // Get user's business
        $this->load->model('User_model');
        $user = $this->User_model->find($data['user_id']);

        if (!$user || !$user->business_id) {
            return false;
        }

        // Calculate total from items if provided
        $total_amount = 0;
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $qty = isset($item['qty']) ? $item['qty'] : 1;
                $price = isset($item['price']) ? $item['price'] : 0;
                $total_amount += $qty * $price;
            }
        } else {
            $total_amount = isset($data['total_amount']) ? $data['total_amount'] : (isset($data['amount']) ? $data['amount'] : 0);
        }

        $insert_data = array(
            'business_id' => $user->business_id,
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'transaction_date' => isset($data['transaction_date']) ? $data['transaction_date'] : date('Y-m-d'),
            'store_name' => isset($data['store_name']) ? $data['store_name'] : null,
            'total_amount' => $total_amount,
            'source' => isset($data['source']) ? $data['source'] : 'web',
            'attachment_url' => isset($data['attachment_url']) ? $data['attachment_url'] : null,
            'notes' => isset($data['notes']) ? $data['notes'] : (isset($data['description']) ? $data['description'] : null),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $insert_data);
        $transaction_id = $this->db->insert_id();

        // Create items if provided
        if (!empty($data['items'])) {
            $this->Transaction_item_model->create_batch($transaction_id, $data['items']);
        } else if ($total_amount > 0) {
            // Create a single item if no items array but has amount
            $this->Transaction_item_model->create(array(
                'transaction_id' => $transaction_id,
                'category_id' => isset($data['category_id']) ? $data['category_id'] : null,
                'name' => isset($data['description']) ? $data['description'] : ($data['type'] == 'income' ? 'Pemasukan' : 'Pengeluaran'),
                'qty' => 1,
                'price' => $total_amount
            ));
        }

        return $transaction_id;
    }

    /**
     * Update a transaction
     */
    public function update($id, $data)
    {
        $allowed_fields = array('type', 'transaction_date', 'store_name', 'total_amount', 'notes', 'attachment_url');
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

        // Update items if provided
        if (isset($data['items'])) {
            $this->Transaction_item_model->delete_by_transaction($id);
            $this->Transaction_item_model->create_batch($id, $data['items']);

            // Recalculate total
            $total = $this->Transaction_item_model->get_total($id);
            $this->db->where('id', $id);
            $this->db->update($this->table, array('total_amount' => $total));
        }

        return $this->find($id);
    }

    /**
     * Delete a transaction
     */
    public function delete($id)
    {
        // Items will be deleted by CASCADE
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
     * Get top expense categories for a business
     */
    public function get_top_categories($user_id, $type = 'expense', $limit = 5)
    {
        $this->load->model('User_model');
        $user = $this->User_model->find($user_id);

        if (!$user || !$user->business_id) {
            return array();
        }

        $this->db->select('categories.name, categories.icon, SUM(transaction_items.subtotal) as total');
        $this->db->from('transaction_items');
        $this->db->join('transactions', 'transactions.id = transaction_items.transaction_id');
        $this->db->join('categories', 'categories.id = transaction_items.category_id', 'left');
        $this->db->where('transactions.business_id', $user->business_id);
        $this->db->where('transactions.type', $type);
        $this->db->where('MONTH(transactions.transaction_date)', date('m'));
        $this->db->where('YEAR(transactions.transaction_date)', date('Y'));
        $this->db->group_by('transaction_items.category_id');
        $this->db->order_by('total', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get balance (income - expense) by custom date range
     * 
     * @param int $user_id User ID
     * @param string $date_from Start date (YYYY-MM-DD)
     * @param string $date_to End date (YYYY-MM-DD)
     * @param string $type Optional type filter (income/expense)
     * @return array Balance data
     */
    public function get_balance_by_date_range($user_id, $date_from = null, $date_to = null, $type = null)
    {
        // Get user's business
        $this->load->model('User_model');
        $user = $this->User_model->find($user_id);

        if (!$user || !$user->business_id) {
            return array(
                'income' => 0,
                'expense' => 0,
                'income_count' => 0,
                'expense_count' => 0,
                'balance' => 0
            );
        }

        $this->db->select('type, SUM(total_amount) as total, COUNT(*) as count');
        $this->db->from($this->table);
        $this->db->where('business_id', $user->business_id);

        // Apply date range filter
        if ($date_from) {
            $this->db->where('transaction_date >=', $date_from);
        }
        if ($date_to) {
            $this->db->where('transaction_date <=', $date_to);
        }

        // Apply type filter if specified
        if ($type) {
            $this->db->where('type', $type);
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
     * Get transaction report with filters
     * 
     * @param int $user_id User ID
     * @param array $filters Filters (date_from, date_to, type, category_id, limit, offset)
     * @return array List of transactions
     */
    public function get_report($user_id, $filters = array())
    {
        // Get user's business
        $this->load->model('User_model');
        $user = $this->User_model->find($user_id);

        if (!$user || !$user->business_id) {
            return array();
        }

        $this->db->select('transactions.*, users.name as created_by_name');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = transactions.user_id', 'left');
        $this->db->where('transactions.business_id', $user->business_id);

        // Apply filters
        if (!empty($filters['type'])) {
            $this->db->where('transactions.type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('transactions.transaction_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('transactions.transaction_date <=', $filters['date_to']);
        }

        // Sorting
        $this->db->order_by('transactions.transaction_date', 'DESC');
        $this->db->order_by('transactions.id', 'DESC');

        // Pagination
        if (!empty($filters['limit'])) {
            $offset = isset($filters['offset']) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $transactions = $this->db->get()->result();

        // Add items and computed fields
        foreach ($transactions as &$tx) {
            $tx->amount = $tx->total_amount;
            $tx->description = $tx->notes ?: $tx->store_name;

            // Get items with category info
            $tx->items = $this->db->select('transaction_items.*, categories.name as category_name, categories.icon as category_icon')
                ->from('transaction_items')
                ->join('categories', 'categories.id = transaction_items.category_id', 'left')
                ->where('transaction_items.transaction_id', $tx->id)
                ->order_by('transaction_items.id', 'ASC')
                ->get()
                ->result();

            // Filter by category if specified
            if (!empty($filters['category_id'])) {
                $has_category = false;
                foreach ($tx->items as $item) {
                    if ($item->category_id == $filters['category_id']) {
                        $has_category = true;
                        break;
                    }
                }
                if (!$has_category) {
                    $tx = null; // Mark for removal
                }
            }

            // Get first item's category for display
            if ($tx && count($tx->items) > 0) {
                $tx->category_name = $tx->items[0]->category_name;
                $tx->category_icon = $tx->items[0]->category_icon;
                if (!$tx->description) {
                    $tx->description = $tx->items[0]->name;
                }
            } else if ($tx) {
                $tx->category_name = null;
                $tx->category_icon = null;
            }
        }

        // Remove null entries (filtered out by category)
        $transactions = array_values(array_filter($transactions, function ($tx) {
            return $tx !== null;
        }));

        return $transactions;
    }
}
