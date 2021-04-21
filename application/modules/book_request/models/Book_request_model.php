<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_request_model extends MY_Model{
    public $per_page = 10;

    public function filter_book_request($filters,$page){
        $book_request = $this->select(['invoice.*'])
        ->from('invoice')
        ->when('keyword', $filters['keyword'])
        ->paginate($page)
        ->get_all();

        $total = $this->select(['invoice_id'])
        ->from('invoice')
        ->when('keyword', $filters['keyword'])
        ->count();

        return[
            'book_request' => $book_request,
            'total'        => $total
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data!='') {
            if($params == 'keyword'){
                $this->group_start();
                $this->or_like('number',$data);
                $this->group_end();
            }
            // if($params == 'type'){
            //     $this->where('type', $data);
            // }
            // if($params == 'book_request_category'){
            //     $this->where('book_request_category', $data);
            // }
        }
        return $this;
    }

    public function fetch_invoice_id($invoice_id){
        return $this->db
        ->select('*')
        ->from('invoice')
        ->where('invoice_id',$invoice_id)
        ->get()->row();
    }

    public function get_staff_gudang()
    {
        return $this->select(['user_id', 'username', 'level', 'email'])
            ->where('level', 'staff_gudang')
            ->where('is_blocked', 'n')
            ->order_by('username', 'ASC')
            ->get_all('user');
    }

    public function get_staff_gudang_by_progress($progress, $invoice_id)
    {
        return $this->db->select(['book_request_user_id', 'book_request_user.user_id', 'invoice_id', 'progress', 'username', 'email'])
            ->from('user')
            ->join('book_request_user', 'user.user_id = book_request_user.user_id')
            ->where('invoice_id', $invoice_id)
            ->where('progress', $progress)
            ->get()->result();
    }

    public function check_row_staff_gudang($invoice_id, $user_id, $progress)
    {
        return $this->db
            ->where(['invoice_id' => $invoice_id, 'user_id' => $user_id, 'progress' => $progress])
            ->get('book_request_user')
            ->num_rows();
    }
    
    // public function filter_book_request($filters, $page){
    //     $book_request = $this->select(['invoice.invoice_id','invoice.number','invoice.issued_date','invoice.type','invoice.status'])
    //     ->from('invoice')
    //     ->when('keyword',$filters['keyword'])
    //     ->when('type',$filters['type'])
    //     ->order_by('issued_date','DESC')
    //     ->paginate($page)
    //     ->get_all();

    //     $total = $this->select(['invoice.*'])
    //     ->from('invoice')
    //     ->when('keyword',$filters['keyword'])
    //     ->count();

    //     return [
    //         'book_request'  => $book_request,
    //         'total'         => $total,
    //     ];
    // }

}