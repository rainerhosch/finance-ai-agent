<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Transactions Table
 */
class Migration_Create_transactions_table extends CI_Migration
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
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'category_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ),
            'type' => array(
                'type' => 'ENUM',
                'constraint' => array('income', 'expense'),
                'null' => FALSE
            ),
            'amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'source' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'web'
            ),
            'attachment_url' => array(
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ),
            'transaction_date' => array(
                'type' => 'DATE',
                'null' => FALSE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('category_id');
        $this->dbforge->add_key('type');
        $this->dbforge->add_key('transaction_date');

        $this->dbforge->create_table('transactions', TRUE);

        // Add foreign keys
        $this->db->query('ALTER TABLE transactions ADD CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE transactions ADD CONSTRAINT fk_transactions_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL');
    }

    public function down()
    {
        $this->dbforge->drop_table('transactions', TRUE);
    }
}
