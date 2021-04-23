<?php

class Migration_Update_book_stock extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column('book_stock', 
        [
            'retur_stock' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book_stock', 'retur_stock');
    }
}
