<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Transactions Table (Header)
 * 
 * Transaction header - one transaction can have multiple items.
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
            'business_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'type' => array(
                'type' => 'ENUM',
                'constraint' => array('income', 'expense'),
                'null' => FALSE
            ),
            'transaction_date' => array(
                'type' => 'DATE',
                'null' => FALSE
            ),
            'store_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ),
            'total_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'null' => FALSE
            ),
            'source' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'web',
                'null' => FALSE
            ),
            'attachment_url' => array(
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ),
            'notes' => array(
                'type' => 'TEXT',
                'null' => TRUE
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
        $this->dbforge->add_key('business_id');
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('type');
        $this->dbforge->add_key('transaction_date');

        $this->dbforge->create_table('transactions', TRUE);

        // Add foreign keys
        $this->db->query('ALTER TABLE transactions ADD CONSTRAINT fk_transactions_business 
            FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE transactions ADD CONSTRAINT fk_transactions_user 
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->dbforge->drop_table('transactions', TRUE);
    }
}
