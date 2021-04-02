<?php

class Migration_Update_book_receive extends CI_Migration{
    public function up(){
        $this->dbforge->add_column('book_receive', 
        [
            'handover_staff' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
        ]);
        $this->dbforge->modify_column('book_receive',[
            'wrapping_user' => [
                'name' => 'wrapping_staff',
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
        ]);
    }
    public function down(){
        $this->dbforge->drop_column('book_receive', 'handover_staff');
        $this->dbforge->modify_column('book_receive',[
            'wrapping_user' => [
                'name' => 'wrapping_user',
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
        ]);
    }
}