<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transaction_model extends MY_Model{
    // public function filter_excel($filters)
    public $per_page = 10;
    public function filter_book_transaction($filters, $page)
    {
        $book_transactions = $this->select([            
            'book.book_title', 'book_transaction.*'])
            ->where_not('date', NULL)
            ->join_table('book', 'book_transaction', 'book')
            ->when('keyword', $filters['keyword'])
            ->when('start_date', $filters['start_date'])
            ->when('end_date', $filters['end_date'])
            ->order_by('book_transaction_id', 'DESC')
            ->paginate($page)
            ->get_all();

        $total = $this->select(['book.book_title', 'book_transaction.*'])
            ->where_not('date', NULL)
            ->join_table('book', 'book_transaction', 'book')
            ->when('keyword', $filters['keyword'])
            ->when('start_date', $filters['start_date'])
            ->when('end_date', $filters['end_date'])
            ->count();
        return [
            'book_transactions' => $book_transactions,
            'total' => $total
        ];
    }
    public function get_transaction_data($book_stock_id,$year){
        return $this->select(['book_transaction.*'])
            ->when('book_stock_id',$book_stock_id)
            ->when('start_date', $year.'-01-01')
            ->when('end_date', $year.'-12-31')
            ->get_all();
    }
    public function when($params, $data)
    {
        //jika data null, maka skip
        if ($data) {
            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('book_title', $data);
                $this->group_end();
            }
            else if ($params == 'book_id') {
                $this->where('book_id', $data);
            }
            else if ($params == 'start_date') {
                $this->where('date >=', $data.' 00:00:00');
            }
            else if ($params == 'end_date') {
                $this->where('date <=', $data.' 23:59:59');
            }
        }
        return $this;
    }
    public function filter_excel($filters)
    {
        return $this->select(['book.book_title', 'book_stock.book_stock_id', 
        // 'invoice.tanggal_selesai',
        // 'invoice_book.id', 
        // 'book_receive.book_receive_id', 
        // 'book_receive.finish_date', 
        'book_transaction.*'])
        ->when('keyword', $filters['keyword'])
        ->when('start_date', $filters['start_date'])
        ->when('end_date', $filters['end_date'])
        // ->join_table('book_transaction', 'book_stock', 'book_transaction')
        ->join_table('book', 'book_transaction', 'book')
        ->join_table('book_stock', 'book_transaction', 'book_stock')
        // ->join_table('book_receive', 'book_stock', 'book_receive')
        ->get_all();
    }
}