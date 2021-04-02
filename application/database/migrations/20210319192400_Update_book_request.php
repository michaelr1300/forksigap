<?php

class Migration_Update_book_request extends CI_Migration{
    public function up(){
        $this->dbforge->modify_column('book_request',[
            'status' => [
                'name' => 'request_status',
                'type' => 'VARCHAR',
                'constraint' => 25
            ],
            'category' => [
                'name' => 'book_request_category',
                'type' => 'VARCHAR',
                'constraint' => 50 
            ]
        ]);
    }
    public function down(){
        $this->dbforge->modify_column('book_request',[
            'request_status' => [
                'name' => 'status',
                'type' => 'INT',
                'constraint' => 1
            ],
            'book_request_category' => [
                'name' => 'category',
                'type' => 'VARCHAR',
                'constraint' => 50 
            ]
        ]);
    }
}