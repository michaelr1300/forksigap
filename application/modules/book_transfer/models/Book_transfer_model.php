<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transfer_model extends MY_Model{
    public $per_page = 10;

    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'quantity',
                'label' => 'Jumlah Buku',
                'rules' => 'trim|required',
            ],
            [
                'field' => 'destination',
                'label' => 'Tujuan Pemindahan',
                'rules' => 'trim|required',
            ],
        ];

        return $validation_rules;
    }

    public function get_default_values()
    {
        return [
            'book_id'           => '',
            'destination'       => '',
            'library_id'        => '',
            'quantity'          => '',
        ];
    }

    public function fetch_book_transfer($book_transfer_id){
        return $this->select(['book.book_id', 'book.book_title', 'library.*', 'book_transfer.*'])
        // ->from('book_transfer')
        ->join_table('book', 'book_transfer', 'book')
        ->join_table('library', 'book_transfer', 'library')
        ->where('book_transfer_id', $book_transfer_id)
        ->get();
    }

    public function get_book($book_id)
    {
        return $this->select('book.*')
            ->where('book_id', $book_id)
            ->get('book');
    }
    
    // public function action_transfer($book_transfer_id){
    //     $date = date('Y-m-d H:i:s');
    //     $user = $_SESSION['username'];
    //     $note = $this->input->post('transfer_notes_admin');

    //     $set = [
    //         'flag'                  => $this->input->post('flag'),
    //         'transfer_status'        => 2,
    //         'transfer_user'          => $user,
    //         'transfer_notes_admin'   => $note,
    //         'transfer_date'          => $date
    //     ];

    //     if($this->input->post('flag') == 2){//setuju
    //         $set['final_status']        = 1;
    //         $set['final_user']          = '';
    //         $set['final_date']          = '';
    //         $set['final_notes_admin']   = '';
    //         $set['finish_date']         = '';
    //         $set['status']              = 1;
    //     }elseif($this->input->post('flag') == 1){//tolak
    //         $set['final_status']        = 0;
    //         $set['final_user']          = $user;
    //         $set['final_date']          = $date;
    //         $set['final_notes_admin']   = $note;
    //         $set['finish_date']         = $date;
    //         $set['status']              = 2;
    //     }

    //     $this->db->set($set)->where('book_transfer_id',$book_transfer_id)->update('book_transfer');
    //     return TRUE;
    // }

    // public function action_final($book_transfer_id){
    //     $add    =   [
    //         'book_id'               => $this->input->post('book_id'),
    //         'stock_in_warehouse'    => $this->input->post('stock_in_warehouse'),
    //         'stock_out_warehouse'   => $this->input->post('stock_out_warehouse'),
    //         'stock_marketing'       => $this->input->post('stock_marketing'),
    //         'stock_input_notes'     => $this->input->post('stock_input_notes'),
    //         'stock_input_type'      => 2,
    //         'stock_input_user'      => $_SESSION['username'],
    //         'stock_input_date'      => date('Y-m-d H:i:s')
    //     ];
        
    //     $this->db->insert('book_stock', $add);

    //     $set    =   [
    //         'final_status'      => 2,
    //         'final_notes_admin' => $this->input->post('stock_input_notes'),
    //         'final_user'        => $_SESSION['username'],
    //         'final_date'        => date('Y-m-d H:i:s'),
    //         'status'            => 3
    //     ];

    //     $this->db->set($set)->where('book_transfer_id',$book_transfer_id)->update('book_transfer');
    //     return TRUE;
    // }

    // public function fetch_book_id($postData){
    //     $response = array();

    //     if(isset($postData['search']) ){
    //         $records = $this->db->select('book_id, book_title')->order_by('book_title','ASC')->like('book_title', $postData['search'],'both')->limit(5)->get('book')->result();
    //         foreach($records as $row ){
    //             $response[] = array("value"=>$row->book_id,"label"=>$row->book_title);
    //         }
    //     }

    //     return $response;
    // }

    public function filter_book_transfer($filters, $page){
        $book_transfer = $this->select(['book_transfer.*','book.book_title','library.library_name'])
        ->when('keyword',$filters['keyword'])
        ->when('status',$filters['status'])
        // ->when('book_transfer_category', $filters['book_transfer_category'])
        ->join_table('book','book_transfer','book')
        ->join_table('library','book_transfer','library')
        ->order_by('book_transfer_id','DESC')
        // ->order_by('status')
        ->paginate($page)
        ->get_all();

        $total = $this->select('book_transfer.id')
        ->when('keyword',$filters['keyword'])
        ->when('status',$filters['status'])
        // ->when('book_transfer_category', $filters['book_transfer_category'])
        ->join_table('book','book_transfer','book')
        // ->order_by('UNIX_TIMESTAMP(entry_date)','DESC')
        // ->order_by('book_title')
        ->count();

        return [
            'book_transfer'  => $book_transfer,
            'total'         => $total,
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data != '') {
            if($params == 'keyword'){
                $this->group_start();
                $this->or_like('book_title',$data);
                // $this->or_like('nomor_faktur',$data);
                // $this->or_like('total',$data);
                $this->group_end();
            }
            if($params == 'status'){
                $this->where('status', $data);
            }
        }
        return $this;
    }

    public function fetch_book_transfer_list($book_transfer_id)
    {
        return $this->db
            ->select('book_transfer_list.*, book.book_id, book.book_title')
            ->from('book_transfer_list')
            ->join('book', 'book.book_id = book_transfer_list.book_id')
            ->where('book_transfer_id', $book_transfer_id)
            ->get()
            ->result();
    }

    public function delete_book_transfer($where){
        $this->db->where('book_transfer_id', $where);
        $this->db->delete('book_transfer');
    }

    // ambil data buku yg ada di gudang
    public function fetch_warehouse_stock($book_id)
    {
        $stock = $this->db->select('warehouse_present')
            ->from('book_stock')
            ->where('book_id', $book_id)
            ->order_by("book_stock_id", "DESC")
            ->limit(1)
            ->get()
            ->row();
        return $stock;
    }

    // buat nampilin data buku gudang di form add
    public function get_ready_book_list()
    {
        $books = $this->db
            ->select('book_id, book_title')
            ->order_by('book_title', 'ASC')
            ->from('book')
            ->get()
            ->result();
        foreach ($books as $book) {
            // Tambahkan data stock ke buku
            $stock = $this->fetch_warehouse_stock($book->book_id);
            if ($stock == NULL)
                $book->stock = 0;
            else
                $book->stock = $stock->warehouse_present;
        }

        // Buku stock 0 tidak ditampilkan
        foreach ($books as $key => $book) {
            if ($book->stock == 0) {
                unset($books[$key]);
            }
        }

        // Input buku ke array untuk dropdown
        $options = ['' => '-- Pilih --'];
        foreach ($books as $book) {
            $options += [$book->book_id => $book->book_title];
        }

        return $options;
    }

    public function get_transfer_number(){
        $data = $this->select(['transfer_number'])
        ->order_by('transfer_number', 'DESC')
        ->get('book_transfer');
        $current_year = date('Y');
        if ($data){
            $year = substr($data->transfer_number,0,4);
            $number = substr($data->transfer_number,5);
            if ($current_year == $year){
                $new_number = (int)++$number;
                $new_number = str_pad((string) $number, 5, '0', STR_PAD_LEFT);
            }
            else {
                $new_number = '00001';
            }
            $new_transfer_number = $current_year."-".$new_number;
        }
        else {
            $new_transfer_number = $current_year."-00001";
        }
        // return 00001
        return $new_transfer_number;
    }

    public function get_staff_gudang()
    {
        return $this->select(['user_id', 'username', 'level', 'email'])
            ->where('level', 'staff_gudang')
            ->where('is_blocked', 'n')
            ->order_by('username', 'ASC')
            ->get_all('user');
    }

    public function get_staff_gudang_by_progress($progress, $book_transfer_id)
    {
        return $this->db->select(['book_transfer_user_id', 'book_transfer_user.user_id', 'book_transfer_id', 'progress', 'username', 'email'])
            ->from('user')
            ->join('book_transfer_user', 'user.user_id = book_transfer_user.user_id')
            ->where('book_transfer_id', $book_transfer_id)
            ->where('progress', $progress)
            ->get()->result();
    }

    public function check_row_staff_gudang($book_transfer_id, $user_id, $progress)
    {
        return $this->db
            ->where(['book_transfer_id' => $book_transfer_id, 'user_id' => $user_id, 'progress' => $progress])
            ->get('book_transfer_user')
            ->num_rows();
    }

    public function start_progress($book_transfer_id)
    {
        // transaction data agar konsisten
        $this->db->trans_begin();

        $input = [
            'status' => 'preparing',
            "preparing_start_date" => date('Y-m-d H:i:s')
        ];

        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function finish_progress($book_transfer_id)
    {
        $input = [
            'status' => "preparing_finish",
            "preparing_end_date" => date('Y-m-d H:i:s')
        ];

        $update_state = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);

        if ($update_state) {
            return true;
        } else {
            return false;
        }
    }


}
