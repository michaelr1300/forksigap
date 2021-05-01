<?php

class Migration_update_invoice extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('invoice', 
        [
            'delivery_fee' => [
                'type' => 'INT',
                'constraint' => 20,
                'null' => TRUE
            ],
            'total_weight' => [
                'type' => 'INT',
                'constraint' => 20,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('invoice', 'delivery_fee');
        $this->dbforge->drop_column('invoice', 'total_weight');
    }
}