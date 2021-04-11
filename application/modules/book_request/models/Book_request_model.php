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