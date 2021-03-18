<?php

class Migration_revision_book_request extends CI_Migration
{
    public function up(){
        $this->dbforge->add_column('book_request', 
        [
            'faktur_id' => [
                'type' => 'INT',
                'constraint' => 50 
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50 
            ],
            'deadline' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'is_preparing' => [
                'type' => 'INT',
                'constraint' => 1 
            ],
            'preparing_start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'preparing_end_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'preparing_deadline' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'preparing_notes' => [
                'type' => 'TEXT',
            ],
            'preparing_notes_admin' => [
                'type' => 'TEXT',
            ],
        ]);

        $this->dbforge->modify_column('book_request', [
            'entry_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'finish_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
        ]);

        $this->dbforge->drop_column('book_request', 'book_id');
        $this->dbforge->drop_column('book_request', 'user_entry');
        $this->dbforge->drop_column('book_request', 'total');
        $this->dbforge->drop_column('book_request', 'notes');
        $this->dbforge->drop_column('book_request', 'flag');
        $this->dbforge->drop_column('book_request', 'request_user');
        $this->dbforge->drop_column('book_request', 'request_date');
        $this->dbforge->drop_column('book_request', 'request_notes_admin');
        $this->dbforge->drop_column('book_request', 'request_status');
        $this->dbforge->drop_column('book_request', 'final_user');
        $this->dbforge->drop_column('book_request', 'final_date');
        $this->dbforge->drop_column('book_request', 'final_notes_admin');
        $this->dbforge->drop_column('book_request', 'final_status');
    }

    public function down(){
        $this->dbforge->add_column('book_request', [
            'book_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'user_entry' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'total' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => 1000,
            ],
            'flag' => [
                'type' => 'INT',
                'constraint' => 1,
            ],

            // request progress
            'request_user' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'request_date' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'request_notes_admin' => [
                'type' => 'VARCHAR',
                'constraint' => 1000,
            ],
            'request_status' => [
                'type' => 'INT',
                'constraint' => 1,
            ],

            // final progress
            'final_user' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'final_date' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'final_notes_admin' => [
                'type' => 'VARCHAR',
                'constraint' => 1000,
            ],
            'final_status' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
        ]);

        $this->dbforge->drop_column('book_request', 'faktur_id');
        $this->dbforge->drop_column('book_request', 'category');
        $this->dbforge->drop_column('book_request', 'deadline');
        $this->dbforge->drop_column('book_request', 'is_preparing');
        $this->dbforge->drop_column('book_request', 'preparing_start_date');
        $this->dbforge->drop_column('book_request', 'preparing_end_date');
        $this->dbforge->drop_column('book_request', 'preparing_deadline');
        $this->dbforge->drop_column('book_request', 'preparing_notes');
        $this->dbforge->drop_column('book_request', 'preparing_notes_admin');
    }
}