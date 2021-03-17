<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_receive_model extends MY_Model
{

    public $per_page = 10;

    //insert data


    //get & filter data and total of data
    public function filter_book_receive($filters, $page)
    {
        $book_receives = $this->select(['print_order.print_order_id', 'print_order.order_number', 
        'print_order.total_print', 'print_order.total_postprint', 'book.book_id', 'book.book_title', 
        'book_receive.*'])
            ->when('keyword', $filters['keyword'])
            ->when('book_receive_status', $filters['book_receive_status'])
            ->join_table('print_order', 'book_receive', 'print_order')
            ->join_table('book', 'book_receive', 'book')
            ->paginate($page)
            ->get_all();
        $total = $this->select('book_receive_id')
            ->when('keyword', $filters['keyword'])
            ->join_table('print_order', 'book_receive', 'print_order')
            ->join_table('book', 'book_receive', 'book')
            ->count();
        return [
            'book_receives' => $book_receives,
            'total' => $total
        ];
    }

    //get filtered
    public function when($params, $data)
    {
        if ($data) {
            if ($params == 'keyword') {
                $this->group_start();
                // $this->or_like('name', $data);
                $this->like('book_title', $data);
                $this->or_like('order_number', $data);
                $this->group_end();
            }
            if ($params == 'book_receive_status'){
                    $this->where('book_receive_status', $data);
            }
        }
        return $this;
    }

    //get book_id
    public function get_book($book_id)
    {
        return $this->select('book.*')
            ->where('book_id', $book_id)
            ->join_table('book', 'book_stock', 'book')
            ->get('book');
    }

    //get book receive id
    public function get_book_receive($book_receive_id)
    {
        return $this->select(['print_order.print_order_id', 'print_order.order_number', 
        'print_order.total_print', 'print_order.total_postprint', 'book.book_id', 'book.book_title', 
        'book_receive.*'])
            ->join_table('print_order', 'book_receive', 'print_order')
            ->join_table('book', 'book_receive', 'book')
            ->where('book_receive_id', $book_receive_id)
            ->get();
    }

    public function get_staff_gudang()
    {
        return $this->select(['user_id', 'username', 'level', 'email'])
            ->where('level', 'staff_gudang')
            ->where('is_blocked', 'n')
            ->order_by('username', 'ASC')
            ->get_all('user');
    }

    public function get_staff_gudang_by_progress($progress, $book_receive_id)
    {
        return $this->db->select(['book_receive_user_id', 'book_receive_user.user_id', 'book_receive_id', 'progress', 'username', 'email'])
            ->from('user')
            ->join('book_receive_user', 'user.user_id = book_receive_user.user_id')
            ->where('book_receive_id', $book_receive_id)
            ->where('progress', $progress)
            ->get()->result();
    }

    public function check_row_staff_gudang($book_receive_id, $user_id, $progress)
    {
        return $this->db
            ->where(['book_receive_id' => $book_receive_id, 'user_id' => $user_id, 'progress' => $progress])
            ->get('book_receive_user')
            ->num_rows();
    }
}