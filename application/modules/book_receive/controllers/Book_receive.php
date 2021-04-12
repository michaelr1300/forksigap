<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_receive extends MY_Controller
{
    public $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_receive";
        $this->load->model('book_receive/book_receive_model', 'book_receive');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
    }

    //index book receive
    public function index($page = NULL)
    {
        //all filters
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'book_receive_status' => $this->input->get('book_receive_status', true)
        ];

        // custom per page
        $this->book_receive->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->book_receive->filter_book_receive($filters, $page);

        // if ($this->check_level() == TRUE):
        $book_receives = $get_data['book_receives'];
        $total = $get_data['total'];
        $pagination = $this->book_receive->make_pagination(site_url('book_receives'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'book_receive/index_bookreceive';
        $this->load->view('template', compact('pages', 'main_view', 'book_receives', 'pagination', 'total'));
        // endif;
    }

    //view details of book receive
    public function view($book_receive_id = null)
    {
        if (!$this->_is_book_receive_user()) {
            redirect($this->pages);
        }

        if ($book_receive_id == null) {
            redirect($this->pages);
        }

        $book_receive = $this->book_receive->get_book_receive($book_receive_id);
        $filename = $filename = 'serah_terima_' .$book_receive_id.'_'. str_replace(['-', ':', ' '], ['', '', '_'], $book_receive->entry_date);

        if (!$book_receive) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $is_handover = $book_receive->is_handover;
        $is_wrapping = $book_receive->is_wrapping;
        $is_final    = $book_receive->book_receive_status == 'finish';
        $uploaded_file = $this->book_receive->find_file_ext($filename);

        $pages       = $this->pages;
        $main_view   = 'book_receive/view/overview';
        $form_action = "book_receive/edit/$book_receive_id";
        $this->load->view('template', compact('form_action', 'main_view', 'pages', 'book_receive', 'is_final', 'is_handover', 'is_wrapping', 'uploaded_file'));
    }

    // public function api_get_staff_gudang()
    // {
    //     $staff_gudang = $this->book_receive->get_staff_gudang();
    //     return $this->send_json_output(true, $staff_gudang);
    // }

    private function _is_book_receive_user()
    {
        if ($this->level == 'superadmin' || $this->level == 'admin_gudang' || $this->level == 'staff_gudang') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang dan superadmin yang dapat mengakses.');
            return false;
        }
    }

    public function generate_pdf_handover($book_receive_id, $progress)
    {
        $book_receive        = $this->book_receive->get_book_receive($book_receive_id);
        $staff_gudang        = $this->book_receive->get_staff_gudang_by_progress($progress, $book_receive_id);
        $staff = '';
        foreach ($staff_gudang as $val) {
            $staff .= $val->username . ", ";
        }
        // PDF
        $this->load->library('pdf');

        // FORMAT DATA
        $data_format['jobtype'] = 'Serah Terima';
        $data_format['title'] = $book_receive->book_title ?? '';
        $data_format['ordernumber'] = $book_receive->order_number ?? '';
        $data_format['total'] = $book_receive->total ?? '';
        $data_format['total_postprint'] = $book_receive->total_postprint ?? '';
        $data_format['handover_end_date'] = date('d/m/Y', strtotime($book_receive->handover_end_date)) ?? '';
        $data_format['staff'] = $book_receive->handover_staff;
        $data_format['notes'] = $book_receive->{"{$progress}_notes"} ?? '';
        $format = $this->load->view('book_receive/format_pdf_handover', $data_format, true);
        $this->pdf->loadHtml($format);

        // (Optional) Setup the paper size and orientation
        $this->pdf->set_paper('A4', 'landscape');

        // Render the HTML as PDF
        $this->pdf->render();
        $this->pdf->stream(strtolower($data_format['ordernumber'] . '_' . $data_format['jobtype']));
    }

    public function generate_pdf_wrapping($book_receive_id, $progress)
    {
        $book_receive        = $this->book_receive->get_book_receive($book_receive_id);
        $staff_gudang        = $this->book_receive->get_staff_gudang_by_progress($progress, $book_receive_id);
        $staff = '';
        foreach ($staff_gudang as $val) {
            $staff .= $val->username . ", ";
        }
        // PDF
        $this->load->library('pdf');

        // FORMAT DATA
        $data_format['jobtype'] = 'Wrapping';
        $data_format['title'] = $book_receive->book_title ?? '';
        $data_format['ordernumber'] = $book_receive->order_number ?? '';
        $data_format['total'] = $book_receive->total ?? '';
        $data_format['total_postprint'] = $book_receive->total_postprint ?? '';
        $data_format['wrapping_start_date'] = date('d/m/Y', strtotime($book_receive->wrapping_start_date)) ?? '';
        $data_format['wrapping_end_date'] = date('d/m/Y', strtotime($book_receive->wrapping_end_date)) ?? '';
        $data_format['wrapping_deadline'] = date('d/m/Y', strtotime($book_receive->wrapping_deadline)) ?? '';
        $data_format['staff'] = $book_receive->wrapping_staff;
        $data_format['notes'] = $book_receive->{"{$progress}_notes"} ?? '';
        $format = $this->load->view('book_receive/format_pdf_wrapping', $data_format, true);
        $this->pdf->loadHtml($format);

        // (Optional) Setup the paper size and orientation
        $this->pdf->set_paper('A4', 'portrait');

        // Render the HTML as PDF
        $this->pdf->render();
        $this->pdf->stream(strtolower($data_format['ordernumber'] . '_' . $data_format['jobtype']));
    }

    //edit book receive
    public function edit($book_receive_id)
    {
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        } else {
            $pages       = $this->pages;
            $main_view   = 'book_receive/edit_bookreceive';
            $this->load->view('template', compact('pages', 'main_view', 'book_receive'));
        }
    }

    public function update($book_receive_id)
    {
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        $book_receive_id = $this->input->post('book_receive_id');
        $entry_date = $this->input->post('entry_date');
        $deadline = $this->input->post('deadline');
        $finish_date = $this->input->post('finish_date');
        $is_handover = $this->input->post('is_handover');
        $handover_start_date = $this->input->post('handover_start_date');
        $handover_end_date = $this->input->post('handover_end_date');
        $handover_staff = $this->input->post('handover_staff');
        $handover_deadline = $this->input->post('handover_deadline');
        $is_wrapping = $this->input->post('is_wrapping');
        $wrapping_start_date = $this->input->post('wrapping_start_date');
        $wrapping_end_date = $this->input->post('wrapping_end_date');
        $wrapping_staff = $this->input->post('wrapping_staff');
        $wrapping_deadline = $this->input->post('wrapping_deadline');

        $this->form_validation->set_rules('deadline', 'Deadline Penerimaan Buku', 'required');

        $status_waiting           = ['book_receive_status' => 'waiting'];
        $status_handover          = ['book_receive_status' => 'handover'];
        $status_handover_approval = ['book_receive_status' => 'handover_approval'];
        $status_handover_finish   = ['book_receive_status' => 'handover_finish'];
        $status_wrapping          = ['book_receive_status' => 'wrapping'];
        $status_wrapping_approval = ['book_receive_status' => 'wrapping_approval'];
        $status_wrapping_finish   = ['book_receive_status' => 'wrapping_finish'];
        $status_finish            = ['book_receive_status' => 'finish'];

        if (empty($handover_start_date)) {
            $handover_start_date = empty_to_null($handover_start_date);
        }
        if (empty($handover_end_date)) {
            $handover_end_date = empty_to_null($handover_end_date);
        }
        if (empty($handover_deadline)) {
            $handover_deadline = empty_to_null($handover_deadline);
        }
        if (empty($wrapping_start_date)) {
            $wrapping_start_date = empty_to_null($wrapping_start_date);
        }
        if (empty($wrapping_end_date)) {
            $wrapping_end_date = empty_to_null($wrapping_end_date);
        }
        if (empty($wrapping_deadline)) {
            $wrapping_deadline = empty_to_null($wrapping_deadline);
        }

        $data = [
            'entry_date' => $entry_date,
            'deadline' => $deadline,
            'finish_date' => $finish_date,
            'is_handover' => $is_handover,
            'handover_start_date' => $handover_start_date,
            'handover_end_date' => $handover_end_date,
            'handover_staff' => $handover_staff,
            'handover_deadline' => $handover_deadline,
            'is_wrapping' => $is_wrapping,
            'wrapping_start_date' => $wrapping_start_date,
            'wrapping_end_date' => $wrapping_end_date,
            'wrapping_staff' => $wrapping_staff,
            'wrapping_deadline' => $wrapping_deadline
        ];

        if ($this->form_validation->run() == true) {
            $this->db->set($data)->where('book_receive_id', $book_receive_id)->update('book_receive');
            if ($finish_date == null) {
                if ($is_handover == 0 && $is_wrapping == 0) {
                    if (
                        $handover_start_date == null && $handover_deadline == null && $handover_end_date == null
                        && $wrapping_start_date == null && $wrapping_deadline == null && $wrapping_end_date == null
                    ) {
                        $this->db->set($status_waiting)->where('book_receive_id', $book_receive_id)->update('book_receive');
                    }
                    if (!$handover_start_date == null && !$handover_deadline == null) {
                        if ($handover_end_date == null) {
                            $this->db->set($status_handover)->where('book_receive_id', $book_receive_id)->update('book_receive');
                        } else if (!$handover_end_date == null) {
                            $this->db->set($status_handover_approval)->where('book_receive_id', $book_receive_id)->update('book_receive');
                        }
                    }
                }
                if ($is_handover == 1 && $is_wrapping == 0) {
                    if ($wrapping_start_date == null && $wrapping_deadline == null && $wrapping_end_date == null) {
                        $this->db->set($status_handover_finish)->where('book_receive_id', $book_receive_id)->update('book_receive');
                    }
                    if (!$wrapping_start_date == null && !$wrapping_deadline == null) {
                        if ($wrapping_end_date == null) {
                            $this->db->set($status_wrapping)->where('book_receive_id', $book_receive_id)->update('book_receive');
                        }
                        if (!$wrapping_end_date == null) {
                            $this->db->set($status_wrapping_approval)->where('book_receive_id', $book_receive_id)->update('book_receive');
                        }
                    }
                }
                if ($is_handover == 1 && $is_wrapping == 1) {
                    $this->db->set($status_wrapping_finish)->where('book_receive_id', $book_receive_id)->update('book_receive');
                }
            }
            if (!$finish_date == null && $is_handover == 1 && $is_wrapping == 1) {
                $this->db->set($status_finish)->where('book_receive_id', $book_receive_id)->update('book_receive');
            }
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }
        redirect('book_receive/view/' . $book_receive->book_receive_id);
    }

    public function delete($book_receive_id = null)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        $this->book_receive->where('book_receive_id', $book_receive_id)->delete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }

        redirect($this->pages);
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

    //add deadline sebelum proses dimulai
    public function add_deadline($book_receive_id)
    {
        if ($this->_is_warehouse_admin() == TRUE && $this->input->method() == 'post') {
            $deadline = $this->input->post('deadline');
            $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
            // $this->load->library('form_validation');
            $this->form_validation->set_rules('deadline', 'Deadline Penerimaan Buku', 'required');
            if (!$book_receive) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            } else {
                if ($this->form_validation->run() == true) {
                    $book_receive->deadline = $deadline;
                    $this->book_receive->where('book_receive_id', $book_receive_id)->update($book_receive);
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan data deadline.');
                    redirect($_SERVER['HTTP_REFERER'], 'refresh');
                }
            }
        } else {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
        }
        redirect('book_receive/view/' . $book_receive_id);
    }

    public function api_start_progress($book_receive_id)
    {
        // apakah book_receive tersedia
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_receive ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $is_start_progress = $this->book_receive->start_progress($book_receive_id, $input->progress);

        if ($is_start_progress) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update book_receive, kirim update via post
    public function api_update($book_receive_id = null)
    {
        // cek data
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_receive ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // untuk reset deadline
        if (isset($input->handover_deadline)) {
            $input->handover_deadline = empty_to_null($input->handover_deadline);
        }
        if (isset($input->wrapping_deadline)) {
            $input->wrapping_deadline = empty_to_null($input->wrapping_deadline);
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);

        if ($this->book_receive->where('book_receive_id', $book_receive_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function api_set_stock($book_receive_id)
    {
        // cek data
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_receive ini
        if (!$this->_is_warehouse_admin()) {
            return $this->send_json_output(false, $this->lang->line('toast_error_not_authorized'));
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);

        if ($this->book_receive->where('book_receive_id', $book_receive_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function api_finish_progress($book_receive_id)
    {
        // apakah order cetak tersedia
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_receive ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $is_finish_progress = $this->book_receive->finish_progress($book_receive_id, $input->progress);

        if ($is_finish_progress) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }


    // update book_receive, kirim update via post
    public function api_action_progress($book_receive_id)
    {
        // cek data
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk superadmin
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $input = (object) $this->input->post(null, false);

        // cek status apakah akan direvert
        if ($input->revert) {
            $input->{"is_$input->progress"} = 0;

            // kembali ke status 'sedang diproses'
            if ($input->progress == 'handover') {
                $input->book_receive_status = 'handover';
            } elseif ($input->progress == 'wrapping') {
                $input->book_receive_status = 'wrapping';
            }
        } else {
            $input->{"is_$input->progress"} = $input->accept;

            // update book_receive status ketika selesai progress
            if ($input->progress == 'handover') {
                $input->book_receive_status = $input->accept ? 'handover_finish' : 'reject';
            } elseif ($input->progress == 'wrapping') {
                $input->book_receive_status = $input->accept ? 'wrapping_finish' : 'reject';
            }
        }

        // jika end date kosong, maka isikan nilai now
        if (!$book_receive->{"{$input->progress}_end_date"}) {
            $input->{"{$input->progress}_end_date"} = now();
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);
        unset($input->accept);
        unset($input->revert);

        if ($this->book_receive->where('book_receive_id', $book_receive_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function final($book_receive_id = null, $action = null)
    {
        if (!$book_receive_id || !$action) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$this->_is_warehouse_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        // ambil data book_receive
        // $data = $this->book_receive->get_book_receive($book_receive_id);

        // insert data book_receive ke table book_transaction
        // $book_id_to_trans         = $data->book_id;
        // $book_receive_id_to_trans = $data->book_receive_id;

        // update book stock

        // memastikan konsistensi data
        $this->db->trans_begin();

        // apakah book receive tersedia
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
        }

        // update data book_receive
        $this->book_receive->where('book_receive_id', $book_receive_id)->update([
            'book_receive_status' => $action,
            'finish_date' => $action == 'finish' ? now() : null
        ]);
        //insert to book stock
        $book_stock = $this->book_stock->where('book_id', $book_receive->book_id)->get();
        $book_stock_print = $this->book_receive->get_print_order($book_receive->print_order_id);
        if ($book_stock) {
            $book_stock->warehouse_present += $book_stock_print->total_postprint;
            $this->book_stock->where('book_id', $book_stock->book_id)->update($book_stock);
        } else {
            $this->book_stock->insert([
                'book_id'            => $book_receive->book_id,
                'warehouse_present'  => $book_stock_print->total_postprint
            ]);
        }
        //insert to book transaction
        //ini datenya dari book transaction
        $book_stock = $this->book_stock->where('book_id', $book_receive->book_id)->get();
        $this->book_transaction->insert([
            'book_id'            => $book_receive->book_id,
            'book_receive_id'    => $book_receive->book_receive_id,
            'book_stock_id'      => $book_stock->book_stock_id,
            'stock_in'           => $book_stock_print->total_postprint,
            'date'               => date("Y-m-d")
        ]);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages . "/view/$book_receive_id");
    }

    public function upload_handover()
    {
        if ($this->_is_warehouse_admin() && $this->input->method() == 'post') {
            $book_receive_id = $this->input->post('receive_id');
            $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
            if (!empty($_FILES) && $book_receive) {
                $filename = 'serah_terima_' .$book_receive_id.'_'. str_replace(['-', ':', ' '], ['', '', '_'], $book_receive->entry_date);
                $upload   = $this->book_receive->upload_handover('handover_file', $filename);
                if ($upload) {
                    $this->session->set_flashdata('success', 'Upload file sukses');
                }
            } else {
                $this->session->set_flashdata('error', 'Upload file gagal');
            }
        } else {
            $this->session->set_flashdata('error', 'Upload file gagal');
        }
        redirect($this->pages . "/view/$book_receive_id");
    }
}
