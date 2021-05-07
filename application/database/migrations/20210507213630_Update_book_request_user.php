<?php

class Migration_Update_book_request_user extends CI_Migration
{

    public function up()
    {
        $this->dbforge->drop_column('book_request_user', 'progress');
    }

    public function down()
    {
        $this->dbforge->add_column('book_request_user', 
        [
            'progress' => [
                'type' => 'VARCHAR',
                'constraint' => 10
            ],
        ]);
    }
}
