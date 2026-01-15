<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Businesses Table
 * 
 * Stores business/store information. 
 * Multiple users can belong to one business.
 */
class Migration_Create_businesses_table extends CI_Migration
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
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ),
            'address' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'logo_url' => array(
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ),
            'created_at' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ),
            'updated_at' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('businesses', TRUE);
    }

    public function down()
    {
        $this->dbforge->drop_table('businesses', TRUE);
    }
}
