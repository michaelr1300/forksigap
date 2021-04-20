<?php

class Migration_Update_book_transfer extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column('book_transfer', 
        [
            'transfer_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ]
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book_transfer','transfer_number');
    }
}
