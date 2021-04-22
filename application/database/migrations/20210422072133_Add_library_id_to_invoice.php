<?php

class Migration_Add_library_id_to_invoice extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_column('invoice', 
        [
            'source_library_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('invoice', 'source_library_id');
    }

}