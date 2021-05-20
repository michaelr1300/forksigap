<?php
class Migration_Author_Last_Paid extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column(
            'author',
            [
                'last_paid_date' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
            ]
        );
    }

    public function down()
    {
        $this->dbforge->drop_column('author', 'last_paid_date');
    }
}
