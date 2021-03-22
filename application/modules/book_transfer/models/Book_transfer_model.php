<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transfer_model extends MY_Model{
    public $per_page = 10;

    // public function add_book_transfer(){
    //     $add = [
    //         'book_id'           => $this->input->post('book_id'),
    //         'order_number'      => $this->input->post('order_number'),
    //         'total'             => $this->input->post('total'),
    //         'notes'             => $this->input->post('notes'),
    //         'user_entry'        => $_SESSION['username'],
    //         'entry_date'        => date('Y-m-d H:i:s'),
    //         'transfer_status'    => 1,
    //     ];
        
    //     $this->db->insert('book_transfer', $add);
    //     return TRUE;
    // }

    // public function edit_book_transfer($book_transfer_id){
    //     $set = [
    //         'book_id'           => $this->input->post('book_id'),
    //         'order_number'      => $this->input->post('order_number'),
    //         'total'             => $this->input->post('total'),
    //         'notes'             => $this->input->post('notes')
    //     ];

    //     $this->db->set($set)->where('book_transfer_id',$book_transfer_id)->update('book_transfer');
    //     return TRUE;
    // }

    // public function delete_book_transfer($book_transfer_id){
    //     $this->db->where('book_transfer_id',$book_transfer_id)->delete('book_transfer');
    //     return TRUE;
    // }
    public function fetch_book_transfer_id($book_transfer_id){
        return $this->db
        ->select(['book.*', 'book_transfer.*'])
        ->from('book_transfer')
        ->join_table('book', 'book_transfer', 'book')
        // ->join_table('faktur', 'book_transfer', 'faktur')
        ->where('book_transfer_id', $book_transfer_id)
        ->get()->row();
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
        // ->order_by('UNIX_TIMESTAMP(entry_date)','DESC')
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

    public function delete_book_transfer($where){
        $this->db->where('book_transfer_id', $where);
        $this->db->delete('book_transfer');
    }

}