<?php

class Migration_Book_transfer extends CI_Migration{
    public function up(){
        $this->dbforge->add_field([
            'book_transfer_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'library_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'book_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 10
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30
            ],
            'destination' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'transfer_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
        ]);
        $this->dbforge->add_key('book_transfer_id', TRUE);
        $this->dbforge->create_table('book_transfer');
    }

    public function down(){
        $this->dbforge->drop_table('book_transfer');
    }
}