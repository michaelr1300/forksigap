<?php

class Migration_Update_book_non_sales extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column('book_non_sales', 
        [
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => TRUE
            ],
            'address' => [
                'type' => 'TEXT',
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book_non_sales', 'name');
        $this->dbforge->drop_column('book_non_sales', 'address');
    }
}
