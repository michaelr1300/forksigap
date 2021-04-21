<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_request extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'book_request';
        $this->load->model('book_request_model', 'book_request');
        $this->load->model('invoice/invoice_model', 'invoice');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
    }

    public function index($page = NULL){
        // all filter
        $filters = [
            'keyword'              => $this->input->get('keyword', true),
            'type'                 => $this->input->get('type', true),
            'status'               => $this->input->get('status', true),
        ];

        // custom per page
        $this->book_request->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->invoice->filter_book_request($filters, $page);

        $book_request   = $get_data['book_request'];
        $total          = $get_data['total'];
        $pagination     = $this->book_request->make_pagination(site_url('book_request'), 2, $total);
        $pages          = $this->pages;
        $main_view      = 'book_request/index_book_request';
        $this->load->view('template', compact('pages', 'main_view', 'book_request', 'pagination','total'));
    }

    public function edit($invoice_id){
        if($this->check_level_gudang() == TRUE):
        $pages        = $this->pages;
        $main_view    = 'book_request/book_request_edit';
        $book_request = $this->invoice->fetch_invoice_id($invoice_id);
        if(empty($book_request) == FALSE):
        $this->load->view('template', compact('pages', 'main_view', 'book_request'));
        else:
        $this->session->set_flashdata('error','Halaman tidak ditemukan.');
        redirect(base_url(), 'refresh');
        endif;
        endif;
    }

    public function view($invoice_id){
        $book_request = $this->invoice->fetch_invoice_id($invoice_id);
        if (!$book_request) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }
        $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);

        $pages        = $this->pages;
        $main_view    = 'book_request/view/view_book_request';
        $this->load->view('template', compact('pages', 'main_view', 'book_request','invoice_books'));
        return;
    }

    public function api_start_preparing($invoice_id)
    {
        // apakah book_request tersedia
        $book_request = $this->invoice->where('invoice_id', $invoice_id)->get();
        if (!$book_request) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan book_request ini
        if (!$this->check_level_gudang()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }
        
        // berisi 'progress' untuk conditional dibawah
        // $input = (object) $this->input->post(null, false);

        $is_start_preparing = $this->invoice->start_progress($invoice_id);

        if ($is_start_preparing) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update book_request, kirim update via post
    public function api_update($invoice_id = null)
    {
        // cek data
        $book_request = $this->invoice->where('invoice_id', $invoice_id)->get();
        if (!$book_request) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_request ini
        if (!$this->check_level_gudang()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // untuk reset deadline
        if (isset($input->preparing_deadline)) {
            $input->preparing_deadline = empty_to_null($input->preparing_deadline);
        }


        if ($this->invoice->where('invoice_id', $invoice_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function api_finish_preparing($invoice_id)
    {
        // apakah book_request tersedia
        $book_request = $this->invoice->where('invoice_id', $invoice_id)->get();
        if (!$book_request) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_request ini
        if (!$this->check_level_gudang()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }
        
        // berisi 'progress' untuk conditional dibawah
        // $input = (object) $this->input->post(null, false);

        //update status
        $is_finish_preparing = $this->invoice->finish_progress($invoice_id);
        
        //update book stock
        $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
        foreach($invoice_books as $invoice_book){
            $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
            $book_stock->warehouse_present -= $invoice_book->qty;
            $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
        }

        //insert to book transaction
        foreach($invoice_books as $invoice_book){
            $this->book_transaction->insert([
                'book_id' => $invoice_book->book_id,
                'book_invoice_id' => $invoice_book->invoice_id,
                'stock_out' => $invoice_book->qty,
                'date' => now()
            ]);
        }

        if ($is_finish_preparing) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // public function add_book_request(){
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
    //         $check  =   $this->book_request->add_book_request();
    //         if($check   ==  TRUE){
    //             $this->session->set_flashdata('success','Berhasil menambahkan draft permintaan buku.');
    //             redirect('book_request');
    //         }else{
    //             $this->session->set_flashdata('error','Gagal menambahkan draft permintaan buku.');
    //             redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //         }
    //     }
    //     endif;
    // }

    public function edit_book_request(){
        if($this->check_level_gudang_pemasaran() == TRUE && $this->input->method()=='post'){
            $order_number = $this->input->post('number');
            $invoice_id = $this->input->post('invoice_id');
            $new_status = $this->input->post('status');
            $invoice = $this->invoice->where('invoice_id', $invoice_id)->get();
            if (!$invoice) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            }
            else {
                $invoice->status = $new_status;
                if ($this->invoice->where('invoice_id', $invoice_id)->update($invoice)) {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                } else {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
                }
            }
        }
        else {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
        }
        redirect($this->pages);
    }

    public function delete_book_request($book_request_id){
        if($this->check_level_gudang_pemasaran() == TRUE){
            $book_request = $this->book_request->where('book_request_id', $book_request_id)->get();
            if (!$book_request) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
                redirect($this->pages);
            }

            if ($this->book_request->where('book_request_id', $book_request_id)->delete()) {
                $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
            } else {
                $this->session->set_flashdata('success', $this->lang->line('toast_delete_fail'));
            }
        }
        redirect($this->pages);
    }

    public function action_request($book_request_id){
        if($this->check_level_gudang() == TRUE):
        $this->load->library('form_validation');
        $this->form_validation->set_rules('flag', 'Aksi', 'required|max_length[1]');
        $this->form_validation->set_rules('request_notes_admin', 'Catatan', 'required|max_length[1000]');

        if($this->form_validation->run() == FALSE){
            $this->session->set_flashdata('error','Gagal melakukan aksi pada progress permintaan.');
            redirect($_SERVER['HTTP_REFERER'].'#section_request', 'refresh');
        }else{
            $check  =   $this->book_request->action_request($book_request_id);
            if($check   ==  TRUE){
                $this->session->set_flashdata('success','Berhasil melakukan aksi pada progress permintaan.');
                redirect($_SERVER['HTTP_REFERER'].'#section_request', 'refresh');
            }else{
                $this->session->set_flashdata('error','Gagal melakukan aksi pada progress permintaan.');
                redirect($_SERVER['HTTP_REFERER'].'#section_request', 'refresh');
            }
        }
        endif;
    }

    public function action_final($book_request_id){
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
            $check  =   $this->book_request->action_final($book_request_id);
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
        $data       =   $this->book_request->fetch_book_id($postData);

        echo json_encode($data);
    }
}