<?php

class Migration_Add_discount_column_to_book_transfer extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('book_transfer', 
        [
            'discount' => [
                'type' => 'INT',
                'constraint' => 3,
            ],
        ]);
        $this->dbforge->drop_column('book_transfer_list', 'discount');
    }

    public function down(){
        $this->dbforge->drop_column('book_transfer', 'discount');
        $this->dbforge->add_column('book_transfer_list', 
        [
            'discount' => [
                'type' => 'INT',
                'constraint' => 3,
            ],
        ]);
    }
}
