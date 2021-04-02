<?php

class Migration_Book_faktur extends CI_Migration{
    public function up(){
        $this->dbforge->add_field([
            'book_faktur_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'faktur_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'book_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'diskon' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => TRUE
            ],
            'harga' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
        ]);
        $this->dbforge->add_key('book_faktur_id', TRUE);
        $this->dbforge->create_table('book_faktur');
    }

    public function down(){
        $this->dbforge->drop_table('book_faktur');
    }
}