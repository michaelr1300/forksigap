<?php

class Migration_update_book_transfer extends CI_Migration
{
    public function up()
    {
        //preparing_start_date
        //preparing_deadline
        //preparing_end_date

        $this->dbforge->add_column('book_transfer', 
        [
            'preparing_deadline' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],           
            'preparing_start_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'preparing_end_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],           
            'finish_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],           
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book_transfer', 'preparing_deadline');
        $this->dbforge->drop_column('book_transfer', 'preparing_start_date');
        $this->dbforge->drop_column('book_transfer', 'preparing_end_date');
        $this->dbforge->drop_column('book_transfer', 'finish_date');
    }
}