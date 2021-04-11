<?php

class Migration_update_invoice extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('invoice', 
        [
            'preparing_staff' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('invoice', 'preparing_staff');
    }
}