<?php

class Migration_Add_royalty_column_to_invoice_book_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_column('invoice_book', 
        [
            'royalty' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('invoice_book', 'royalty');
    }

}