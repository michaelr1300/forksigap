<?php

class Migration_Update_book_transaction extends CI_Migration
{

    public function up()
    {
        $this->dbforge->drop_column('book_transaction', 'stock_in');
        $this->dbforge->drop_column('book_transaction', 'stock_out');
        $this->dbforge->add_column('book_transaction', 
        [
            'stock_mutation' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'book_stock_revision_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
        ]);
    }

    public function down()
    {
        $this->dbforge->add_column('book_transaction', 
        [
            'stock_in' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'stock_out' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
        ]);
        $this->dbforge->drop_column('book_transaction', 'stock_mutation');
        $this->dbforge->drop_column('book_transaction', 'book_stock_revision_id');
    }
}
