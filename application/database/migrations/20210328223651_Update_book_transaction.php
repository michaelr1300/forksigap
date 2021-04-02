<?php

class Migration_Update_book_transaction extends CI_Migration{
    public function up(){
        $this->dbforge->add_column('book_transaction', 
        [
            'date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
                ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book_transaction', 'date');
    }
}