<?php

        class Migration_Update_invoice_book_id extends CI_Migration {

            public function up() {
                $this->dbforge->modify_column('invoice_book', 
                [
                    'id' => [
                        'name' => 'invoice_book_id',
                        'type' => 'INT',
                        'constraint' => 10,
                        'auto_increment' => TRUE
                    ]
                ]);
            }

            public function down() {
                $this->dbforge->modify_column('invoice_book', 
                [
                    'invoice_book_id' => [
                        'name' => 'id',
                        'type' => 'INT',
                        'constraint' => 10,
                        'auto_increment' => TRUE
                    ]
                ]);
            }

        }