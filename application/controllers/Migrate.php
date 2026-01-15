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
    }

    public function index()
    {
        $this->load->dbforge();

        echo "<h1>incatat.id Database Migration</h1>";
        echo "<pre>";

        try {
            // Create businesses table
            echo "Creating businesses table...\n";
            $this->create_businesses_table();
            echo "✓ Businesses table created\n\n";

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

            // Create transaction_items table
            echo "Creating transaction_items table...\n";
            $this->create_transaction_items_table();
            echo "✓ Transaction items table created\n\n";

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

    /**
     * Drop all tables (for fresh start)
     */
    public function fresh()
    {
        $this->load->dbforge();

        echo "<h1>incatat.id - Fresh Database</h1>";
        echo "<pre>";

        try {
            echo "Dropping all tables...\n";

            // Disable foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            $tables = array('transaction_items', 'transactions', 'categories', 'users', 'businesses');
            foreach ($tables as $table) {
                $this->dbforge->drop_table($table, TRUE);
                echo "  - Dropped $table\n";
            }

            // Enable foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

            echo "\n✓ All tables dropped\n\n";

            echo "Now run /migrate to create new tables\n";

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }

        echo "</pre>";
    }

    private function create_businesses_table()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE),
            'phone' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE),
            'address' => array('type' => 'TEXT', 'null' => TRUE),
            'logo_url' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE),
            'created_at' => array('type' => 'TIMESTAMP', 'null' => TRUE),
            'updated_at' => array('type' => 'TIMESTAMP', 'null' => TRUE)
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('businesses', TRUE);
    }

    private function create_users_table()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'business_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'google_id' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
            'email' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE),
            'name' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
            'avatar' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE),
            'phone' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE),
            'role' => array('type' => 'ENUM', 'constraint' => array('owner', 'admin', 'staff'), 'default' => 'owner'),
            'telegram_user_id' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE),
            'api_token' => array('type' => 'VARCHAR', 'constraint' => 64, 'null' => TRUE),
            'profile_completed' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE)
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users', TRUE);

        // Add indexes
        $this->db->query('CREATE UNIQUE INDEX idx_google_id ON users(google_id)');
        $this->db->query('CREATE UNIQUE INDEX idx_email ON users(email)');
        $this->db->query('CREATE UNIQUE INDEX idx_api_token ON users(api_token)');
        $this->db->query('CREATE INDEX idx_telegram_user_id ON users(telegram_user_id)');
        $this->db->query('CREATE INDEX idx_business_id ON users(business_id)');

        // Foreign key
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_business FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE SET NULL');
    }

    private function create_categories_table()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'business_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE),
            'type' => array('type' => 'ENUM', 'constraint' => array('income', 'expense'), 'null' => FALSE),
            'icon' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE),
            'is_default' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE)
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('categories', TRUE);

        $this->db->query('CREATE INDEX idx_category_business_id ON categories(business_id)');
        $this->db->query('CREATE INDEX idx_category_type ON categories(type)');
        $this->db->query('ALTER TABLE categories ADD CONSTRAINT fk_categories_business FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE');
    }

    private function create_transactions_table()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'business_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE),
            'user_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE),
            'type' => array('type' => 'ENUM', 'constraint' => array('income', 'expense'), 'null' => FALSE),
            'transaction_date' => array('type' => 'DATE', 'null' => FALSE),
            'store_name' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
            'total_amount' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0),
            'source' => array('type' => 'VARCHAR', 'constraint' => 50, 'default' => 'web'),
            'attachment_url' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE),
            'notes' => array('type' => 'TEXT', 'null' => TRUE),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE)
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('transactions', TRUE);

        $this->db->query('CREATE INDEX idx_transaction_business_id ON transactions(business_id)');
        $this->db->query('CREATE INDEX idx_transaction_user_id ON transactions(user_id)');
        $this->db->query('CREATE INDEX idx_transaction_type ON transactions(type)');
        $this->db->query('CREATE INDEX idx_transaction_date ON transactions(transaction_date)');
        $this->db->query('ALTER TABLE transactions ADD CONSTRAINT fk_transactions_business FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE transactions ADD CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    private function create_transaction_items_table()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'transaction_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE),
            'category_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE),
            'qty' => array('type' => 'INT', 'constraint' => 11, 'default' => 1),
            'price' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0),
            'subtotal' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0),
            'notes' => array('type' => 'TEXT', 'null' => TRUE),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE)
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('transaction_items', TRUE);

        $this->db->query('CREATE INDEX idx_items_transaction_id ON transaction_items(transaction_id)');
        $this->db->query('CREATE INDEX idx_items_category_id ON transaction_items(category_id)');
        $this->db->query('ALTER TABLE transaction_items ADD CONSTRAINT fk_items_transaction FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE transaction_items ADD CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL');
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
