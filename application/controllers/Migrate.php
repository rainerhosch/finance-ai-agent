<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migrate Controller
 * 
 * Simple database migration controller.
 * Access via: http://yourdomain.com/migrate
 * 
 * IMPORTANT: Remove or protect this in production!
 */
class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Only allow in development
        if (ENVIRONMENT === 'production') {
            show_error('Migration is disabled in production.', 403);
        }
    }

    public function index()
    {
        $this->load->dbforge();

        echo "<h1>FinanceAI Database Migration</h1>";
        echo "<pre>";

        try {
            // Create users table
            echo "Creating users table...\n";
            $this->create_users_table();
            echo "✓ Users table created\n\n";

            // Create categories table
            echo "Creating categories table...\n";
            $this->create_categories_table();
            echo "✓ Categories table created\n\n";

            // Create transactions table
            echo "Creating transactions table...\n";
            $this->create_transactions_table();
            echo "✓ Transactions table created\n\n";

            // Seed default categories
            echo "Seeding default categories...\n";
            $this->seed_categories();
            echo "✓ Default categories inserted\n\n";

            echo "=============================\n";
            echo "Migration completed successfully!\n";
            echo "=============================\n";

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }

        echo "</pre>";
    }

    private function create_users_table()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'google_id' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
            'email' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE),
            'name' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
            'avatar' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE),
            'phone' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE),
            'telegram_user_id' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE),
            'api_token' => array('type' => 'VARCHAR', 'constraint' => 64, 'null' => TRUE),
            'profile_completed' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE)
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users', TRUE);

        // Add unique indexes
        $this->db->query('CREATE UNIQUE INDEX idx_google_id ON users(google_id)');
        $this->db->query('CREATE UNIQUE INDEX idx_email ON users(email)');
        $this->db->query('CREATE UNIQUE INDEX idx_api_token ON users(api_token)');
        $this->db->query('CREATE INDEX idx_telegram_user_id ON users(telegram_user_id)');
    }

    private function create_categories_table()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'user_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE),
            'type' => array('type' => 'ENUM', 'constraint' => array('income', 'expense'), 'null' => FALSE),
            'icon' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE),
            'is_default' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE)
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('categories', TRUE);

        $this->db->query('CREATE INDEX idx_category_user_id ON categories(user_id)');
        $this->db->query('CREATE INDEX idx_category_type ON categories(type)');
        $this->db->query('ALTER TABLE categories ADD CONSTRAINT fk_categories_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    private function create_transactions_table()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'user_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE),
            'category_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'type' => array('type' => 'ENUM', 'constraint' => array('income', 'expense'), 'null' => FALSE),
            'amount' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'null' => FALSE),
            'description' => array('type' => 'TEXT', 'null' => TRUE),
            'source' => array('type' => 'VARCHAR', 'constraint' => 50, 'default' => 'web'),
            'attachment_url' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE),
            'transaction_date' => array('type' => 'DATE', 'null' => FALSE),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE)
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('transactions', TRUE);

        $this->db->query('CREATE INDEX idx_transaction_user_id ON transactions(user_id)');
        $this->db->query('CREATE INDEX idx_transaction_category_id ON transactions(category_id)');
        $this->db->query('CREATE INDEX idx_transaction_type ON transactions(type)');
        $this->db->query('CREATE INDEX idx_transaction_date ON transactions(transaction_date)');
        $this->db->query('ALTER TABLE transactions ADD CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE transactions ADD CONSTRAINT fk_transactions_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL');
    }

    private function seed_categories()
    {
        $categories = array(
            // Income
            array('name' => 'Gaji', 'type' => 'income', 'icon' => '💰', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Bonus', 'type' => 'income', 'icon' => '🎁', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Investasi', 'type' => 'income', 'icon' => '📈', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Penjualan', 'type' => 'income', 'icon' => '🛒', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Lainnya', 'type' => 'income', 'icon' => '📥', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            // Expense
            array('name' => 'Makanan & Minuman', 'type' => 'expense', 'icon' => '🍔', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Transportasi', 'type' => 'expense', 'icon' => '🚗', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Belanja', 'type' => 'expense', 'icon' => '🛍️', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Tagihan', 'type' => 'expense', 'icon' => '📄', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Hiburan', 'type' => 'expense', 'icon' => '🎮', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Kesehatan', 'type' => 'expense', 'icon' => '💊', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Pendidikan', 'type' => 'expense', 'icon' => '📚', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Lainnya', 'type' => 'expense', 'icon' => '📤', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
        );

        // Only insert if categories table is empty
        if ($this->db->count_all('categories') == 0) {
            $this->db->insert_batch('categories', $categories);
        }
    }
}
