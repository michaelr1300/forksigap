<?php

class Migration_Update_book_transaction extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column('book_transaction', 
        [
            'book_transfer_id' => [
                'type' => 'INT',
                'constraint' => 10
            ],
            'book_non_sales_id' => [
                'type' => 'INT',
                'constraint' => 10
            ]
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book_transaction', 'book_transfer_id');
        $this->dbforge->drop_column('book_transaction', 'book_non_sales_id');

    }
}
