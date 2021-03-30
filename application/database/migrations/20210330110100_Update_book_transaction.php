<?php

class Migration_Update_book_transaction extends CI_Migration{
    public function up(){
        $this->dbforge->modify_column('book_transaction', 
        [
            'buku_faktur_id' => [
                'name' => 'book_faktur_id',
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->modify_column('book_transaction', 
        [
            'book_faktur_id' => [
                'name' => 'buku_faktur_id',
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
        ]);
    }
}