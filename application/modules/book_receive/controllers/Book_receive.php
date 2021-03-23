<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_receive extends MY_Controller
{
    public $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_receive";
        $this->load->model('book_receive/book_receive_model', 'book_receive');
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

        if (!$book_receive) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $is_handover = $book_receive->is_handover == 1;
        $is_wrapping = $book_receive->is_wrapping == 1;
        $is_final    = $book_receive->book_receive_status == 'finish';

        $pages       = $this->pages;
        $main_view   = 'book_receive/view/overview';
        $form_action = "book_receive/edit/$book_receive_id";
        $this->load->view('template', compact('form_action', 'main_view', 'pages', 'book_receive', 'is_final', 'is_handover', 'is_wrapping'));
    }

    // api buat start progress, finish progress, update, action progress, 
    // select staff di view dll kayak di print order

    public function api_get_staff_gudang()
    {
        $staff_gudang = $this->book_receive->get_staff_gudang();
        return $this->send_json_output(true, $staff_gudang);
    }

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
        // $data_format['category'] = get_print_order_category()[$print_order->category] ?? '';
        $data_format['ordernumber'] = $book_receive->order_number ?? '';
        $data_format['total_print'] = $book_receive->total_print ?? '';
        $data_format['total_postprint'] = $book_receive->total_postprint ?? '';
        // $data_format['entrydate'] = date('d/m/Y', strtotime($book_receive->entry_date)) ?? '';
        // $data_format['deadline'] = date('d/m/Y', strtotime($book_receive->{"{$progress}_deadline"})) ?? '';
        $data_format['handover_end_date'] = date('d/m/Y', strtotime($book_receive->wrapping_end_date)) ?? '';
        $data_format['staff'] = $staff;
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
        $data_format['total_print'] = $book_receive->total_print ?? '';
        $data_format['total_postprint'] = $book_receive->total_postprint ?? '';
        $data_format['wrapping_start_date'] = date('d/m/Y', strtotime($book_receive->wrapping_start_date)) ?? '';
        $data_format['wrapping_end_date'] = date('d/m/Y', strtotime($book_receive->wrapping_end_date)) ?? '';
        $data_format['wrapping_deadline'] = date('d/m/Y', strtotime($book_receive->wrapping_deadline)) ?? '';
        $data_format['staff'] = $staff;
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
        // $book_receive = $this->book_receive->get_book_receive($book_receive_id);
        if (!$book_receive) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        } else {
            // if (!$this->book_receive->validate()) {
                $pages       = $this->pages;
                $main_view   = 'book_receive/edit_bookreceive';
                // $form_action = "book_receive/edit/$book_receive_id";
                $this->load->view('template', compact(
                    'pages',
                    'main_view',
                    // 'form_action', 
                    // 'input', 
                    'book_receive'
                ));
            //     return;
            // }
        }

        // if (!$_POST) {
        //     $input = (object)$book_receive;
        // } else {
        //     $input = (object)$this->input->post(null, true);
        //     // catat orang yang menginput order cetak
        //     // $input->user_id = $_SESSION['user_id'];
        // }


        // if ($this->book_receive->where('book_receive_id', $book_receive_id)->update($input)) {
        //     $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        // } else {
        //     $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        // }

        // memastikan konsistensi data
        // $this->db->trans_begin();

        // update book_receive
        // $this->book_receive->where('book_receive_id', $book_receive_id)->update($input);

        // if ($this->db->trans_status() === false) {
        //     $this->db->trans_rollback();
        //     $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        // } else {
        //     $this->db->trans_commit();
        //     $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        // }

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
        $handover_deadline = $this->input->post('handover_deadline');
        $is_wrapping = $this->input->post('is_wrapping');
        $wrapping_start_date = $this->input->post('wrapping_start_date');
        $wrapping_end_date = $this->input->post('wrapping_end_date');
        $wrapping_deadline = $this->input->post('wrapping_deadline');

        // $book_receive->entry_date = $entry_date;
        // $book_receive->deadline = $deadline;
        // $book_receive->finish_date = $finish_date;
        // $book_receive->is_handover = $is_handover;
        // $book_receive->handover_start_date = $handover_start_date;
        // $book_receive->handover_end_date = $handover_end_date;
        // $book_receive->handover_deadline = $handover_deadline;
        // $book_receive->is_wrapping = $is_wrapping;
        // $book_receive->wrapping_start_date = $wrapping_start_date;
        // $book_receive->wrapping_end_date = $wrapping_end_date;
        // $book_receive->wrapping_deadline = $wrapping_deadline;
        $data = [
            'entry_date' => $entry_date,
            'deadline' => $deadline,
            'finish_date' => $finish_date,
            'is_handover' => $is_handover,
            'handover_start_date' => $handover_start_date,
            'handover_end_date' => $handover_end_date,
            'handover_deadline' => $handover_deadline,
            'is_wrapping' => $is_wrapping,
            'wrapping_start_date' => $wrapping_start_date,
            'wrapping_end_date' => $wrapping_end_date,
            'wrapping_deadline' => $wrapping_deadline,
        ];

        // $this->book_receive->update_book_receive($book_receive_id, $data, 'book_receive');
        // if ($this->book_receive->where('book_receive_id', $book_receive_id)->update($book_receive)) {
            $this->db->set($data)->where('book_receive_id', $book_receive_id)->update('book_receive');
            // $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        // } else {
            // $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        // }

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
        // $this->book_stock->delete_book_stock($book_stock_id);
        // $this->print_order->delete_print_order_file($print_order->print_order_file);
        // $this->print_order->delete_letter_file($print_order->letter_file);
        // $this->print_order->delete_preprint_file($print_order->delete_preprint_file);

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
}
