<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_receive_model extends MY_Model{

    public $per_page = 10;

    public function filter_book_receive($filters, $page){
        $book_receives = $this->select([])
            ->when('keyword', $filters['keyword'])
            ->get();
    }

    public function when($params, $data){
        if ($data) {
            if ($params == 'keyword') {
                $this->group_start();
                // $this->or_like('name', $data);
                $this->or_like('book_title', $data);
                $this->or_like('isbn', $data);
                $this->group_end();
            }
        }
        return $this;
    }
}