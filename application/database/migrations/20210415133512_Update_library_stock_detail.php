<?php

class Migration_update_library_stock_detail extends CI_Migration
{
    public function up()
    {
        $this->dbforge->modify_column('library_stock_detail', 
        [
            'library_stock_id' => [
                'name' => 'book_stock_id',
                'type' => 'INT',
                'constraint' => 10
            ]
        ]);
    }

    public function down(){
        $this->dbforge->modify_column('library_stock_detail', 
        [
            'book_stock_id' => [
                'name' => 'library_stock_id',
                'type' => 'INT',
                'constraint' => 10
            ],
        ]);

    }
}