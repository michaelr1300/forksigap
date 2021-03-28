<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transaction_model extends MY_Model{
    // public function filter_excel($filters)
    public function filter_excel()
    {
        return $this->select(['book.book_title', 'book_stock.book_stock_id', 
        // 'faktur.tanggal_selesai',
        'book_faktur.book_faktur_id', 
        // 'book_receive.book_receive_id', 
        'book_receive.finish_date', 
        'book_transaction.*'])
            // ->when('keyword', $filters['keyword'])
            // ->when('published_year', $filters['published_year'])
            // ->when('warehouse_present', $filters['warehouse_present'])
            ->join_table('book', 'book_transaction', 'book')
            ->join_table('book_stock', 'book_transaction', 'book_stock')
            ->join_table('book_faktur', 'book_transaction', 'book_faktur')
            // ->join_table('faktur', 'book_transaction', 'faktur')
            ->join_table('book_receive', 'book_transaction', 'book_receive')
            ->get_all();
    }
}