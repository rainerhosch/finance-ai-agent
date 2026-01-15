<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Transaction Items Table (Detail)
 * 
 * Individual items within a transaction.
 */
class Migration_Create_transaction_items_table extends CI_Migration
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
            'transaction_id' => array(
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
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'qty' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'null' => FALSE
            ),
            'price' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'null' => FALSE
            ),
            'subtotal' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'null' => FALSE
            ),
            'notes' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('transaction_id');
        $this->dbforge->add_key('category_id');

        $this->dbforge->create_table('transaction_items', TRUE);

        // Add foreign keys
        $this->db->query('ALTER TABLE transaction_items ADD CONSTRAINT fk_items_transaction 
            FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE transaction_items ADD CONSTRAINT fk_items_category 
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL');
    }

    public function down()
    {
        $this->dbforge->drop_table('transaction_items', TRUE);
    }
}
