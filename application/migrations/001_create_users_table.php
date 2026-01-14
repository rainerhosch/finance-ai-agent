<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Users Table
 */
class Migration_Create_users_table extends CI_Migration
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
            'google_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ),
            'avatar' => array(
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ),
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ),
            'telegram_user_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ),
            'api_token' => array(
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => TRUE
            ),
            'profile_completed' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
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
        $this->dbforge->add_key('google_id');
        $this->dbforge->add_key('email');
        $this->dbforge->add_key('telegram_user_id');
        $this->dbforge->add_key('api_token');

        $this->dbforge->create_table('users', TRUE);

        // Add unique constraints
        $this->db->query('ALTER TABLE users ADD UNIQUE INDEX idx_google_id (google_id)');
        $this->db->query('ALTER TABLE users ADD UNIQUE INDEX idx_email (email)');
        $this->db->query('ALTER TABLE users ADD UNIQUE INDEX idx_api_token (api_token)');
    }

    public function down()
    {
        $this->dbforge->drop_table('users', TRUE);
    }
}
