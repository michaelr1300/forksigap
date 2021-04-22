<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transfer extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'book_transfer';
        $this->load->model('book_transfer_model', 'book_transfer');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('library/library_model', 'library');
    }

    public function index($page = NULL)
    {
        if ($this->check_level_gudang_pemasaran() == TRUE) :
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
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        if ($book_transfer_id == null) {
            redirect($this->pages);
        }

        $book_transfer = $this->book_transfer->fetch_book_transfer($book_transfer_id);

        if (!$book_transfer) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $book_transfer_list = $this->book_transfer->fetch_book_transfer_list($book_transfer_id);
        $pages       = $this->pages;
        $main_view   = 'book_transfer/view/view_book_transfer';
        $this->load->view('template', compact('main_view', 'pages', 'book_transfer', 'book_transfer_list'));
    }
    
    public function generate_pdf_bon($book_transfer_id)
    {
        $book_transfer        = $this->book_transfer->fetch_book_transfer($book_transfer_id);
        $book_transfer_list   = $this->book_transfer->fetch_book_transfer_list($book_transfer_id);

        // PDF
        $this->load->library('pdf');
        // FORMAT DATA
        if($book_transfer->library_id){
            $data_format['destination'] = $book_transfer->library_name;
        }
        else{
            $data_format['destination'] = 'Showroom';
        }
        $data_format['number']        = $book_transfer->transfer_number ?? '';
        $data_format['transfer_date'] = $book_transfer->transfer_date ?? '';
        $data_format['book_list']     = $book_transfer_list ?? '';
        $format = $this->load->view('book_transfer/format_pdf_bon', $data_format, true);
        $this->pdf->loadHtml($format);

        // (Optional) Setup the paper size and orientation
        $this->pdf->set_paper('A4', 'portrait');
        // Render the HTML as PDF
        $this->pdf->render();
        $this->pdf->stream(strtolower($data_format['number'] . '_' . 'Pemindahan Buku'));

    }

    public function api_get_staff_gudang()
    {
        $staff_gudang = $this->book_transfer->get_staff_gudang();
        return $this->send_json_output(true, $staff_gudang);
    }

    public function api_add_staff_gudang()
    {
        $input = (object) $this->input->post(null, true);

        if (!$input->book_transfer_id || !$input->user_id || !$input->progress) {
            return $this->send_json_output(false, $this->lang->line('toast_data_not_available'));
        }

        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        if ($this->book_transfer->check_row_staff_gudang($input->book_transfer_id, $input->user_id, $input->progress) > 0) {
            return $this->send_json_output(false, $this->lang->line('toast_data_duplicate'), 422);
        }

        if ($this->db->insert('book_transfer_user', $input)) {
            return $this->send_json_output(true, $this->lang->line('toast_add_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_add_fail'));
        }
    }

    public function api_delete_staff_gudang($id = null)
    {
        $staff_gudang = $this->db->where('book_transfer_user_id', $id)->get('book_transfer_user')->result();
        if (!$staff_gudang) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        if ($this->db->delete('book_transfer_user', ['book_transfer_user_id' => $id])) {
            return $this->send_json_output(true, $this->lang->line('toast_delete_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_delete_fail'));
        }
    }

    public function api_start_preparing($book_transfer_id)
    {
        // apakah book_transfer tersedia
        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan book_transfer ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }
        
        // berisi 'progress' untuk conditional dibawah
        // $input = (object) $this->input->post(null, false);

        $is_start_preparing = $this->book_transfer->start_progress($book_transfer_id);

        if ($is_start_preparing) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update book_transfer, kirim update via post
    public function api_update($book_transfer_id = null)
    {
        // cek data
        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_transfer ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // untuk reset deadline
        if (isset($input->preparing_deadline)) {
            $input->preparing_deadline = empty_to_null($input->preparing_deadline);
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);

        if ($this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function api_finish_preparing($book_transfer_id)
    {
        // apakah book_transfer tersedia
        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_transfer ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }
        
        // berisi 'progress' untuk conditional dibawah
        // $input = (object) $this->input->post(null, false);

        //update status
        $is_finish_preparing = $this->book_transfer->finish_progress($book_transfer_id);
        
        if ($is_finish_preparing) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function final($book_transfer_id = null, $action = null){
        if (!$book_transfer_id || !$action) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$this->_is_warehouse_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        // apakah book transfer tersedia
        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
        }

        // update book stock tapi tabel list buku yg dipindahin blm ada
        
        $book_transfer_lists  = $this->book_transfer->fetch_book_transfer_list($book_transfer_id);

        // update stok perpustakaan
        if($book_transfer->library_id){
            foreach($book_transfer_lists as $book_transfer_list){
                $book_stock = $this->book_stock->where('book_id', $book_transfer_list->book_id)->get();
                $book_stock->library_present += $book_transfer_list->qty;
                $this->book_stock->where('book_id', $book_transfer_list->book_id)->update($book_stock);
                // stok detail perpustakaan
                $library_stock_detail = $this->db->select('*')
                    ->from('library_stock_detail')
                    ->where('book_stock_id', $book_stock->book_stock_id)
                    ->where('library_id', $book_transfer->library_id)
                    ->get()
                    ->row();
                if($library_stock_detail){
                    $library_stock_detail->library_stock += $book_transfer_list->qty;
                    $this->db->set('library_stock', $library_stock_detail->library_stock);
                    $this->db->where('library_id', $library_stock_detail->library_id);
                    $this->db->update('library_stock_detail');
                }
                else{
                    $library_stock_insert = [
                        'library_id'    => $book_transfer->library_id,
                        'book_stock_id' => $book_stock->book_stock_id,
                        'library_stock' => $book_transfer_list->qty,
                    ];
                    $this->db->insert('library_stock_detail', $library_stock_insert);                
                }
            }
        }
        // update stok showroom
        else{
            foreach($book_transfer_lists as $book_transfer_list){
                $book_stock = $this->book_stock->where('book_id', $book_transfer_list->book_id)->get();
                $book_stock->showroom_present += $book_transfer_list->qty;
                $this->book_stock->where('book_id', $book_transfer_list->book_id)->update($book_stock);
            }
        }

        // update book stock
        foreach($book_transfer_lists as $book_transfer_list){
            $book_stock = $this->book_stock->where('book_id', $book_transfer_list->book_id)->get();
            $book_stock->warehouse_present -= $book_transfer_list->qty;
            $this->book_stock->where('book_id', $book_transfer_list->book_id)->update($book_stock);
        }
        
        // update data book_transfer
        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update([
            'status' => $action,
            'finish_date' => $action == 'finish' ? now() : null
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages . "/view/$book_transfer_id");
    }

    public function add()
    {
        if (!$this->check_level_gudang_pemasaran()==true) {
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object) $this->book_transfer->get_default_values();
            // dipindah ke sini dulu, soalnya validate belum bisa
            if (!$this->book_transfer->validate() || $this->form_validation->error_array()) {

                $pages       = $this->pages;
                $main_view   = 'book_transfer/book_transfer_add';
                $form_action = 'book_transfer/add';
                $book_transfer_available = $this->book_transfer->get_ready_book_list();
                $this->load->view('template', compact('pages', 'main_view', 'form_action', 'book_transfer_available', 'input'));
                return;
            }
        } else {
            $input = (object) $this->input->post(null, true);
            // catat orang yang menginput order cetak
        }

        $input->library_id = empty_to_null($input->library_id);
        $transfer_number = $this->book_transfer->get_transfer_number();
        $book_transfer = (object) [
            'transfer_number' => $transfer_number,
            'status' => 'waiting',
            'transfer_date' => now(),
            'destination' => $input->destination,
            'library_id' => $input->library_id
        ];
        // insert book transfer
        $book_transfer_success = $this->book_transfer->insert($book_transfer);
        $book_transfer_id = $this->db->insert_id();
        foreach ($input->book_list as $books){
            $book_transfer_list = (object)[
                'book_transfer_id' => $book_transfer_id,
                'book_id' => $books['book_id'],
                'qty' => $books['qty'],
                'discount' => $input->discount
            ];
            $book_transfer_list_success = $this->db->insert('book_transfer_list',$book_transfer_list);
        }
        if ($book_transfer_success && $book_transfer_list_success) {
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        }
        redirect('book_transfer/add');
    }

    public function api_get_book($book_id)
    {
        return $this->send_json_output(true, $this->book_transfer->get_book($book_id));
    }

    public function edit($book_transfer_id = null)
    {
        if (!$this->check_level_gudang_pemasaran()) {
            redirect($this->pages);
        }

        $book_transfer = $this->book_transfer->fetch_book_transfer($book_transfer_id);
        if (!$book_transfer) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        else{
        // if (!$_POST) {
        //     $input = (object) $book_transfer;
        // } else {
        //     $input = (object) $this->input->post(null, true);
        // }

        // if (!$this->book_transfer->validate() || $this->form_validation->error_array()) {
            $pages       = $this->pages;
            $main_view   = 'book_transfer/book_transfer_edit';
            // $form_action = "book_transfer/edit/$book_transfer_id";
            $this->load->view('template', compact('pages', 'main_view', 'book_transfer'));
            return;
        }

        // memastikan konsistensi data
        // $this->db->trans_begin();

        
        // $input->finish_date = empty_to_null($input->finish_date);
        // $input->transfer_date = empty_to_null($input->transfer_date);
        // $input->preparing_start_date = empty_to_null($input->preparing_start_date);
        // $input->preparing_end_date = empty_to_null($input->preparing_end_date);
        // $input->preparing_deadline = empty_to_null($input->preparing_deadline);
        
        //  hapus order cetak jika check delete_file
        // if (empty($input->library_id)) {
        //     $input->library_id = empty_to_null($input->library_id);
        // }

        // $book_stock = $this->book_stock->where('book_id', $input->book_id)->get();

        // if($input->status == 'waiting' || $input->status == 'preparing'){
        //     $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);
        // }
        // else if($input->status == 'finish'){
        //     // $book_stock->warehouse_present -= $input->quantity;
        //     // if ($input->library_id) {
        //     //     $book_stock->library_present += $input->quantity;
        //     //     $library_stock_detail = $this->db->select('*')
        //     //         ->from('library_stock_detail')
        //     //         ->where('book_stock_id', $book_stock->book_stock_id)
        //     //         ->where('library_id', $input->library_id)
        //     //         ->get()
        //     //         ->row();
                                                 
        //     //     if($library_stock_detail){
        //     //         $this->db->set('library_stock', $library_stock_detail->library_stock+$input->quantity)
        //     //         ->where('library_id', $input->library_id)
        //     //         ->where('book_stock_id', $book_stock->book_stock_id)
        //     //         ->update('library_stock_detail');
        //     //     }
        //     //     elseif(!$library_stock_detail){
        //     //         $library_stock_insert = [
        //     //             'library_id'    => $book_transfer->library_id,
        //     //             'book_stock_id' => $book_stock->book_stock_id,
        //     //             'library_stock' => $input->quantity,
        //     //         ];
        //     //         $this->db->insert('library_stock_detail', $library_stock_insert);                
        //     //     }
        //     //     // $this->library->where('library_id', $library_stock_detail->library_id)->update($library_stock_detail);    
        //     // } else {
        //     //     $book_stock->showroom_present += $input->quantity;
        //     // }
        //     // $this->book_stock->where('book_id', $book_stock->book_id)->update($book_stock);            
            
        // }
        
        // $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);
        
        // if ($this->db->trans_status() === false) {
        //     $this->db->trans_rollback();
        //     $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        // } else {
        //     $this->db->trans_commit();
        //     $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        // }

        // redirect('book_transfer/view/'.$book_transfer_id);
    }


    public function update($book_transfer_id)
    {
        $input = (object) $this->input->post(null, true);

        $input->finish_date = empty_to_null($input->finish_date);
        $input->transfer_date = empty_to_null($input->transfer_date);
        $input->preparing_start_date = empty_to_null($input->preparing_start_date);
        $input->preparing_end_date = empty_to_null($input->preparing_end_date);
        $input->preparing_deadline = empty_to_null($input->preparing_deadline);

        $this->db->trans_begin();
        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        // if ($this->form_validation->run() == true) {
        //     $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        // } else {
            // $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        //     redirect($_SERVER['HTTP_REFERER'], 'refresh');
        // }
        redirect('book_transfer/view/' . $book_transfer_id);
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

    public function action_transfer($book_transfer_id)
    {
        if ($this->check_level_gudang() == TRUE) :
            $this->load->library('form_validation');
            $this->form_validation->set_rules('flag', 'Aksi', 'required|max_length[1]');
            $this->form_validation->set_rules('transfer_notes_admin', 'Catatan', 'required|max_length[1000]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Gagal melakukan aksi pada progress permintaan.');
                redirect($_SERVER['HTTP_REFERER'] . '#section_transfer', 'refresh');
            } else {
                $check  =   $this->book_transfer->action_transfer($book_transfer_id);
                if ($check   ==  TRUE) {
                    $this->session->set_flashdata('success', 'Berhasil melakukan aksi pada progress permintaan.');
                    redirect($_SERVER['HTTP_REFERER'] . '#section_transfer', 'refresh');
                } else {
                    $this->session->set_flashdata('error', 'Gagal melakukan aksi pada progress permintaan.');
                    redirect($_SERVER['HTTP_REFERER'] . '#section_transfer', 'refresh');
                }
            }
        endif;
    }

    public function action_final($book_transfer_id)
    {
        if ($this->check_level_gudang() == TRUE) :
            $this->load->library('form_validation');
            $this->form_validation->set_rules('stock_in_warehouse', 'Stok dalam gudang', 'required|max_length[10]');
            $this->form_validation->set_rules('stock_out_warehouse', 'Stok luar gudang', 'required|max_length[10]');
            $this->form_validation->set_rules('stock_marketing', 'Stok pemasaran', 'required|max_length[10]');
            $this->form_validation->set_rules('stock_input_notes', 'Catatan', 'required|max_length[256]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Permintaan buku gagal di finalisasi.');
                redirect($_SERVER['HTTP_REFERER'] . '#section_final', 'refresh');
            } else {
                $check  =   $this->book_transfer->action_final($book_transfer_id);
                if ($check   ==  TRUE) {
                    $this->session->set_flashdata('success', 'Permintaan buku berhasil di finalisasi.');
                    redirect($_SERVER['HTTP_REFERER'] . '#section_final', 'refresh');
                } else {
                    $this->session->set_flashdata('error', 'Permintaan buku gagal di finalisasi.');
                    redirect($_SERVER['HTTP_REFERER'] . '#section_final', 'refresh');
                }
            }
        endif;
    }

    public function check_level_gudang_pemasaran()
    {
        if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang' || $_SESSION['level'] == 'admin_pemasaran') {
            return TRUE;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang, admin pemasaran, dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }

    public function check_level_gudang()
    {
        if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang') {
            return TRUE;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }

    public function check_level_pemasaran()
    {
        if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_pemasaran') {
            return TRUE;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin pemasaran dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }

    public function ac_book_id()
    {
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
