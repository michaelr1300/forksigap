<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_stock_model extends MY_Model
{
    public $per_page = 10;

    // public function get_validation_rules(){
    //     $validation_rules = [
    //         [
    //         'field' => '',
    //         'label' => '',
    //         'ruler' => 'trim|required'
    //         ],
    //     ];

    //     return $validation_rules;
    // }

    public function filter_book_stock($filters, $page)
    {
        $book_stocks = $this->select([
            'book_stock_id', 'book.book_id',
            'book.book_title', 'book.isbn', 'book.published_date',
            'book_stock.*'])
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('warehouse_present', $filters['warehouse_present'])
            ->join_table('book', 'book_stock', 'book')
            ->order_by('warehouse_present')
            ->paginate($page)
            ->get_all();

        $total = $this->select('book.book_id')
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('warehouse_present', $filters['warehouse_present'])
            ->join_table('book', 'book_stock', 'book')
            ->order_by('warehouse_present')
            ->count();
        return [
            'book_stocks' => $book_stocks,
            'total' => $total
        ];
    }

    public function filter_excel($filters)
    {
        return $this->select(['book.book_title', 'book.isbn', 'book.published_date', 'book_stock.*'])
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('warehouse_present', $filters['warehouse_present'])
            ->join_table('book', 'book_stock', 'book')
            ->order_by('book.book_title')
            ->get_all();
    }

    public function when($params, $data)
    {
        //jika data null, maka skip
        if ($data) {
            if ($params == 'keyword') {
                $this->group_start();
                // $this->or_like('name', $data);
                $this->or_like('book_title', $data);
                $this->or_like('isbn', $data);
                $this->group_end();
            }
            if ($params == 'published_year') {
                $this->where('year(published_date)', $data);
            }
            if ($params == 'warehouse_present') {
                if($data == 1){
                    $this->where('warehouse_present <=', 50);
                }
                else if($data == 2){
                    $this->where('warehouse_present >', 50);
                } 
                else{
                    $this->where('warehouse_present', $data);
                }
            }
        }
        return $this;
    }

    public function get_book_stock($book_stock_id){
        return $this->select(['book.book_title', 
        'book_stock.*'])
        ->join('book')
        ->where('book_stock_id', $book_stock_id)
        ->get();
    }

    public function get_book($book_id)
    {
        return $this->select('book.*')
        ->where('book_id', $book_id)
        ->join_table('book','book_stock','book')
        ->get('book');
    }

    // public function fetch_stock_by_id($book_id)
    // {
    //     $stock_history    = $this->db->select('*')->from('book_stock')->where('book_id', $book_id)->order_by("UNIX_TIMESTAMP(date)", "DESC")->get()->result();
    //     $stock_last       = $this->db->select('*')->from('book_stock')->where('book_id', $book_id)->order_by("UNIX_TIMESTAMP(date)", "DESC")->limit(1)->get()->row();
    //     return [
    //         'stock_history' => $stock_history,
    //         'stock_last'    => $stock_last
    //     ];
    // }

    public function get_stock_by_id($book_stock_id)
    {
        return $this->db->select('*')
        ->from('book_stock')
        ->where('book_stock_id', $book_stock_id)
        ->get()
        ->row();
    }

    public function delete_book_stock($where){
        $this->db->where('book_stock_id', $where);
        $this->db->delete('book_stock');
    }

    public function get_stock_revision($book_id){
        return $this->db->select('*')
        ->from('book_stock_revision')
        ->where('book_stock_revision.book_id', $book_id)
        ->order_by('book_stock_revision.book_stock_revision_id', 'DESC')
        ->get()
        ->result();
    }

    public function get_library($library_id){
        return $this->db->select('*')
        ->from('library')
        ->where('library.library_id', $library_id)
        ->get()
        ->row();
        // ->result();
    }

    public function get_library_stock($book_stock_id){
        return $this->db->select('*','library.library_name')
        ->from('library_stock_detail')
        ->join('library', "library.library_id = library_stock_detail.library_id", 'left')
        ->where('library_stock_detail.book_stock_id', $book_stock_id)
        // ->where('library_stock_detail.library_id', $library_id)
        ->get()
        ->result();
    }

    // public function fetch_library_stock($book_stock_id){
    //     return $this->select(['library.library_name', 'library.library_id',
    //     'book_stock.book_stock_id',
    //     'library_stock_detail.*'])
        // ->from('library_stock_detail')
        // ->from('library')
        // ->from('book')
        // ->join('book_stock', 'book_stock.book_stock_id = library_stock_detail.book_stock_id', 'inner')
        // ->join('library_stock_detail', 'library_stock_detail.library_id = library.library_id', 'inner')
        // ->join_table('book_stock', 'library_stock_detail', 'book_stock')
        // ->join_table('library', 'library_stock_detail', 'library')
        // ->where('book_stock_id', $book_stock_id)
        // ->join('book_stock', 'book_stock.book_stock_id = library_stock_detail.book_stock_id', 'inner')
        // ->where('library.library_id', 'library_stock_detail.library_id')
    //     ->get();
    // }

}
