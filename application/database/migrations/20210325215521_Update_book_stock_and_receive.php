<?php

class Migration_Update_book_stock_and_receive extends CI_Migration{
    public function up(){
        $this->dbforge->add_column('book_stock', 
        [
            'book_receive_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'book_request_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'book_revision_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
        ]);
        $this->dbforge->drop_column('book_receive', 'total'); 
    }

    public function down(){
        $this->dbforge->drop_column('book_stock', 'book_receive_id');
        $this->dbforge->drop_column('book_stock', 'book_request_id');
        $this->dbforge->drop_column('book_stock', 'book_revision_id');
        $this->dbforge->add_column('book_receive', 
        [
            'total' => [
                'type' => 'INT',
                'constraint' => 10,
            ],    
        ]);
    }
}