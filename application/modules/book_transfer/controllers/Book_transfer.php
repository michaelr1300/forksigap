<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transfer extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'book_transfer';
        $this->load->model('book_transfer_model', 'book_transfer');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
    }

    public function index($page = NULL){
        if($this->check_level_gudang_pemasaran() == TRUE):
        // all filter
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'status'            => $this->input->get('status', true),
            // 'book_transfer_category' => $this->input->get('book_transfer_category', true)
        ];

        // custom per page
        $this->book_transfer->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->book_transfer->filter_book_transfer($filters, $page);

        $book_transfer   = $get_data['book_transfer'];
        $total          = $get_data['total'];
        $pagination     = $this->book_transfer->make_pagination(site_url('book_transfer'), 2, $total);
        $pages          = $this->pages;
        $main_view      = 'book_transfer/index_book_transfer';
        $this->load->view('template', compact('pages', 'main_view', 'book_transfer', 'pagination', 'total'));
        endif;
    }

    public function view($book_transfer_id)
    {
        // if (!$this->_is_book_transfer_user()) {
        //     redirect($this->pages);
        // }

        if ($book_transfer_id == null) {
            redirect($this->pages);
        }

        $book_transfer = $this->book_transfer->fetch_book_transfer($book_transfer_id);

        if (!$book_transfer) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $pages       = $this->pages;
        $main_view   = 'book_transfer/book_transfer_view';
        $this->load->view('template', compact('main_view', 'pages', 'book_transfer'));
    }

    public function add()
    {
        if (!$this->check_level_gudang_pemasaran()==true) {
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object) $this->book_transfer->get_default_values();
        } else {
            $input = (object) $this->input->post(null, true);
            // catat orang yang menginput order cetak
        }

        if (!$this->book_transfer->validate() || $this->form_validation->error_array()) {

            $pages       = $this->pages;
            $main_view   = 'book_transfer/book_transfer_add';
            $form_action = 'book_transfer/add';
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }

        // set status awal
        $input->status = 'waiting';
        $input->transfer_date = now();

        if (empty($input->library_id)) {
            $input->library_id = empty_to_null($input->library_id);
        }

        // insert book transfer
        $book_transfer_id = $this->book_transfer->insert($input);
        //insert to book stock
        $book_stock = $this->book_stock->where('book_id', $input->book_id)->get();
        // $book_stock_print = $this->book_receive->get_print_order($book_receive->print_order_id);

        // if ($book_stock) {
            $book_stock->warehouse_present -= $input->quantity;
            if($input->library_id){
                $book_stock->library_present += $input->quantity;
            }
            else{
                $book_stock->showroom_present += $input->quantity;
            }
            $this->book_stock->where('book_id', $book_stock->book_id)->update($book_stock);
        // } 
        // else {
        //     $this->book_stock->insert([
        //         'book_id'            => $book_receive->book_id,
        //         'warehouse_present'  => $book_stock_print->total_postprint
        //     ]);
        // }


        if ($book_transfer_id) {
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        }
        redirect('book_transfer');

        // redirect('book_transfer/view/' . $book_transfer_id);
    }

    public function api_get_book($book_id)
    {
        return $this->send_json_output(true, $this->book_transfer->get_book($book_id));
    }
    
    public function edit($book_transfer_id)
    {
        if (!$this->check_level_gudang_pemasaran()) {
            redirect($this->pages);
        }

        $book_transfer = $this->book_transfer->fetch_book_transfer($book_transfer_id);
        if (!$book_transfer) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object)  $book_transfer;
        } else {
            $input = (object) $this->input->post(null, true);
        }

        if (!$this->book_transfer->validate() || $this->form_validation->error_array()) {
            $pages       = $this->pages;
            $main_view   = 'book_transfer/book_transfer_edit';
            $form_action = "book_transfer/edit/$book_transfer_id";
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input', 'book_transfer'));
            return;
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        //  hapus order cetak jika check delete_file

        if (empty($input->library_id)) {
            $input->library_id = empty_to_null($input->library_id);
        }
        // update print order
        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect('book_transfer');
    }


    // public function add(){
    //     if($this->check_level_gudang_pemasaran() == TRUE):
    //     $pages       = $this->pages;
    //     $main_view   = 'book_transfer/book_transfer_add';
    //     $this->load->view('template', compact('pages', 'main_view'));
    //     endif;book_transfer

    //     $data['book'] = $this->book_transfer_model->get_book()->result();
    //     $this->load->view('template', compact('pages', 'main_view'));
    // }

    // public function edit($book_transfer_id){
    //     if($this->check_level_gudang_pemasaran() == TRUE):
    //     $pages       = $this->pages;
    //     $main_view   = 'book_transfer/book_transfer_edit';
    //     $rData       = $this->book_transfer->fetch_book_transfer_id($book_transfer_id);
    //     if(empty($rData) == FALSE):
    //     $this->load->view('template', compact('pages', 'main_view', 'rData'));
    //     else:
    //     $this->session->set_flashdata('error','Halaman tidak ditemukan.');
    //     redirect(base_url(), 'refresh');
    //     endif;
    //     endif;
    // }

    // public function add_book_transfer(){
    //     if($this->check_level_gudang_pemasaran() == TRUE):
    //     $this->load->library('form_validation');
    //     $this->form_validation->set_rules('book_id', 'Judul buku', 'required|max_length[10]');
    //     $this->form_validation->set_rules('order_number', 'Nomor Order', 'required|max_length[25]');
    //     $this->form_validation->set_rules('total', 'Jumlah Permintaan', 'required|max_length[10]');
    //     $this->form_validation->set_rules('notes', 'Catatan', 'required|max_length[250]');

    //     if($this->form_validation->run() == FALSE){
    //         $this->session->set_flashdata('error',validation_errors());
    //         redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //     }else{
    //         $check  =   $this->book_transfer->add_book_transfer();
    //         if($check   ==  TRUE){
    //             $this->session->set_flashdata('success','Berhasil menambahkan draft permintaan buku.');
    //             redirect('book_transfer');
    //         }else{
    //             $this->session->set_flashdata('error','Gagal menambahkan draft permintaan buku.');
    //             redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //         }
    //     }
    //     endif;
    // }

    // public function edit_book_transfer($book_transfer_id){
    //     if($this->check_level_gudang() == TRUE):
    //     $this->load->library('form_validation');
    //     $this->form_validation->set_rules('book_id', 'Judul buku', 'max_length[10]');
    //     $this->form_validation->set_rules('order_number', 'Nomor Order', 'max_length[25]');
    //     $this->form_validation->set_rules('total', 'Jumlah Permintaan', 'max_length[10]');
    //     $this->form_validation->set_rules('notes', 'Catatan', 'max_length[250]');

    //     if($this->form_validation->run() == FALSE){
    //         $this->session->set_flashdata('error','Gagal mengubah data draft permintaan buku.');
    //         redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //     }else{
    //         $check  =   $this->book_transfer->edit_book_transfer($book_transfer_id);
    //         if($check   ==  TRUE){
    //             $this->session->set_flashdata('success','Berhasil mengubah data draft permintaan buku.');
    //             redirect('book_transfer/view/'.$book_transfer_id);
    //         }else{
    //             $this->session->set_flashdata('error','Gagal mengubah data draft permintaan buku.');
    //             redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //         }
    //     }
    //     endif;
    // }

    // public function delete_book_transfer($book_transfer_id){
    //     if($this->check_level_gudang() == TRUE):
    //     $check  = $this->book_transfer->delete_book_transfer($book_transfer_id);
    //     if($check   ==  TRUE){
    //         $this->session->set_flashdata('success','Berhasil menghapus data draft permintaan buku.');
    //         redirect('book_transfer');
    //     }else{
    //         $this->session->set_flashdata('error','Gagal menghapus data draft permintaan buku.');
    //         redirect('book_transfer');
    //     }
    //     endif;
    // }

    public function delete_book_transfer($book_transfer_id = null)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->delete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }

        redirect($this->pages);
    }

    public function action_transfer($book_transfer_id){
        if($this->check_level_gudang() == TRUE):
        $this->load->library('form_validation');
        $this->form_validation->set_rules('flag', 'Aksi', 'required|max_length[1]');
        $this->form_validation->set_rules('transfer_notes_admin', 'Catatan', 'required|max_length[1000]');

        if($this->form_validation->run() == FALSE){
            $this->session->set_flashdata('error','Gagal melakukan aksi pada progress permintaan.');
            redirect($_SERVER['HTTP_REFERER'].'#section_transfer', 'refresh');
        }else{
            $check  =   $this->book_transfer->action_transfer($book_transfer_id);
            if($check   ==  TRUE){
                $this->session->set_flashdata('success','Berhasil melakukan aksi pada progress permintaan.');
                redirect($_SERVER['HTTP_REFERER'].'#section_transfer', 'refresh');
            }else{
                $this->session->set_flashdata('error','Gagal melakukan aksi pada progress permintaan.');
                redirect($_SERVER['HTTP_REFERER'].'#section_transfer', 'refresh');
            }
        }
        endif;
    }

    public function action_final($book_transfer_id){
        if($this->check_level_gudang() == TRUE):
        $this->load->library('form_validation');
        $this->form_validation->set_rules('stock_in_warehouse', 'Stok dalam gudang', 'required|max_length[10]');
        $this->form_validation->set_rules('stock_out_warehouse', 'Stok luar gudang', 'required|max_length[10]');
        $this->form_validation->set_rules('stock_marketing', 'Stok pemasaran', 'required|max_length[10]');
        $this->form_validation->set_rules('stock_input_notes', 'Catatan', 'required|max_length[256]');

        if($this->form_validation->run() == FALSE){
            $this->session->set_flashdata('error','Permintaan buku gagal di finalisasi.');
            redirect($_SERVER['HTTP_REFERER'].'#section_final', 'refresh');
        }else{
            $check  =   $this->book_transfer->action_final($book_transfer_id);
            if($check   ==  TRUE){
                $this->session->set_flashdata('success','Permintaan buku berhasil di finalisasi.');
                redirect($_SERVER['HTTP_REFERER'].'#section_final', 'refresh');
            }else{
                $this->session->set_flashdata('error','Permintaan buku gagal di finalisasi.');
                redirect($_SERVER['HTTP_REFERER'].'#section_final', 'refresh');
            }
        }
        endif;
    }

    public function check_level_gudang_pemasaran(){
        if($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang' || $_SESSION['level'] == 'admin_pemasaran'){
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Hanya admin gudang, admin pemasaran, dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }

    public function check_level_gudang(){
        if($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang'){
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Hanya admin gudang dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }
    
    public function check_level_pemasaran(){
        if($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_pemasaran'){
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Hanya admin pemasaran dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }

    public function ac_book_id(){
        $postData   =   $this->input->post();
        $data       =   $this->book_transfer->fetch_book_id($postData);

        echo json_encode($data);
    }

    private function _is_warehouse_admin()
    {
        if ($this->level == 'superadmin' || $this->level == 'admin_gudang') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang dan superadmin yang dapat mengakses.');
            return false;
        }
    }
}