<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Categories Table
 * 
 * Categories now belong to business, not individual users.
 */
class Migration_Create_categories_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'business_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ),
            'type' => array(
                'type' => 'ENUM',
                'constraint' => array('income', 'expense'),
                'null' => FALSE
            ),
            'icon' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ),
            'is_default' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('business_id');
        $this->dbforge->add_key('type');

        $this->dbforge->create_table('categories', TRUE);

        // Add foreign key
        $this->db->query('ALTER TABLE categories ADD CONSTRAINT fk_categories_business 
            FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE');

        // Insert default categories (global, no business_id)
        $default_categories = array(
            // Income categories
            array('name' => 'Gaji', 'type' => 'income', 'icon' => '💰', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Bonus', 'type' => 'income', 'icon' => '🎁', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Investasi', 'type' => 'income', 'icon' => '📈', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Penjualan', 'type' => 'income', 'icon' => '🛒', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Lainnya', 'type' => 'income', 'icon' => '📥', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),

            // Expense categories
            array('name' => 'Makanan & Minuman', 'type' => 'expense', 'icon' => '🍔', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Transportasi', 'type' => 'expense', 'icon' => '🚗', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Belanja', 'type' => 'expense', 'icon' => '🛍️', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Tagihan', 'type' => 'expense', 'icon' => '📄', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Hiburan', 'type' => 'expense', 'icon' => '🎮', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Kesehatan', 'type' => 'expense', 'icon' => '💊', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Pendidikan', 'type' => 'expense', 'icon' => '📚', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
            array('name' => 'Lainnya', 'type' => 'expense', 'icon' => '📤', 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s')),
        );

        $this->db->insert_batch('categories', $default_categories);
    }

    public function down()
    {
        $this->dbforge->drop_table('categories', TRUE);
    }
}
