<?php

class Migration_Invoice extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'invoice_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'due_date' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'customer_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'default' => 'waiting'
            ],
            'issued_date' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);
        $this->dbforge->add_key('invoice_id', TRUE);
        $this->dbforge->create_table('invoice');
    }

    public function down()
    {
        $this->dbforge->drop_table('invoice');
    }
}
