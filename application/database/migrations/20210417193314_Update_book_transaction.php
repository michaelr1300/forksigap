<?php

class Migration_Update_book_transaction extends CI_Migration
{

    public function up()
    {
        $this->dbforge->modify_column('book_transaction', 
        [
            'book_invoice_id' => [
                'name' => 'invoice_id',
                'type' => 'INT',
                'constraint' => 10
            ]
        ]);
    }

    public function down(){
        $this->dbforge->modify_column('book_transaction', 
        [
            'invoice_id' => [
                'name' => 'book_invoice_id',
                'type' => 'INT',
                'constraint' => 10
            ],
        ]);

    }
}
