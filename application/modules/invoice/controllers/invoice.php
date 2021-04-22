<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'invoice';
        $this->load->model('invoice_model', 'invoice');
        $this->load->model('book/Book_model', 'book');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        $filters = [
            'keyword'   => $this->input->get('keyword', true),
            'type'      => $this->input->get('type', true),
            'status'    => $this->input->get('status', true)
        ];

        $this->invoice->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->invoice->filter_invoice($filters, $page);

        //data invoice
        $invoice    = $get_data['invoice'];
        $total      = $get_data['total'];
        $pagination = $this->invoice->make_pagination(site_url('invoice'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'invoice/index_invoice';
        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'pagination', 'total'));
    }

    public function view($invoice_id)
    {
        $pages          = $this->pages;
        $main_view      = 'invoice/view_invoice';
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);

        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'invoice_books'));
    }

    // View add
    public function add()
    {
        //post add invoice
        if ($_POST) {
            //validasi input
            $this->invoice->validate_invoice();
            $date_created       = date('Y-m-d H:i:s');

            //Nentuin customer id jika customer diambil dari database
            if (!empty($this->input->post('customer-id'))) {
                $customer_id = $this->input->post('customer-id');
            }
            //Nentuin customer id jika customer dibuat baru
            else {
                $add = [
                    'name'          => $this->input->post('new-customer-name'),
                    'address'       => $this->input->post('new-customer-address'),
                    'phone_number'  => $this->input->post('new-customer-phone-number'),
                    'type'          => $this->input->post('new-customer-type')
                ];
                $this->db->insert('customer', $add);
                $customer_id = $this->db->insert_id();
            }

            $add = [
                'number'            => $this->input->post('number'),
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'type'              => $this->input->post('type'),
                'source'            => $this->input->post('source'),
                'status'            => 'waiting',
                'issued_date'       => $date_created
                // 'user_created'      => $user_created
            ];
            $this->db->insert('invoice', $add);

            // ID faktur terbaru untuk diisi buku
            $invoice_id = $this->db->insert_id();

            // Jumlah Buku di Faktur
            $countsize = count($this->input->post('invoice_book_id'));

            // Masukkan buku di form faktur ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book = [
                    'invoice_id'    => $invoice_id,
                    'book_id'       => $this->input->post('invoice_book_id')[$i],
                    'qty'           => $this->input->post('invoice_book_qty')[$i],
                    'price'         => $this->input->post('invoice_book_price')[$i],
                    'discount'      => $this->input->post('invoice_book_discount')[$i]
                ];
                $this->db->insert('invoice_book', $book);
            }
            echo json_encode(['status' => TRUE]);
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        }

        //View add invoice
        else {
            $invoice_type = array(
                'credit'      => 'Kredit',
                'online'      => 'Online',
                'cash'        => 'Tunai',
                'showroom'    => 'Showroom',
            );

            $source = array(
                'library'   => 'Perpustakaan',
                'showroom'  => 'Showroom',
                'warehouse' => 'Gudang'
            );

            $customer_type = get_customer_type();

            $dropdown_book_options = $this->invoice->get_ready_book_list();

            $pages       = $this->pages;
            $main_view   = 'invoice/add_invoice';
            $this->load->view('template', compact('pages', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
        }
    }

    // View Edit
    public function edit($invoice_id)
    {
        //post edit invoice
        if ($_POST) {
            //validasi input edit
            $this->invoice->validate_invoice();
            //Nentuin customer id jika customer diambil dari database
            if (!empty($this->input->post('customer-id'))) {
                $customer_id = $this->input->post('customer-id');
            }
            //Nentuin customer id jika customer dibuat baru
            else {
                $add = [
                    'name'          => $this->input->post('new-customer-name'),
                    'address'       => $this->input->post('new-customer-address'),
                    'phone_number'  => $this->input->post('new-customer-phone-number'),
                    'type'          => $this->input->post('new-customer-type')
                ];
                $this->db->insert('customer', $add);
                $customer_id = $this->db->insert_id();
            }

            $edit = [
                'number'            => $this->input->post('number'),
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'type'              => $this->input->post('type'),
                'source'            => $this->input->post('source'),
                'status'            => 'waiting'
                // 'date_edited'   => date('Y-m-d H:i:s'),
                // 'user_edited'   => $_SESSION['username']
            ];

            $this->db->set($edit)->where('invoice_id', $invoice_id)->update('invoice');

            // Jumlah Buku di Faktur
            $countsize = count($this->input->post('invoice_book_id'));

            //hapus invoice_book yang sudah ada 
            $this->db->where('invoice_id', $invoice_id)->delete('invoice_book');

            // Masukkan buku di form faktur ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book = [
                    'invoice_id'    => $invoice_id,
                    'book_id'       => $this->input->post('invoice_book_id')[$i],
                    'qty'           => $this->input->post('invoice_book_qty')[$i],
                    'price'         => $this->input->post('invoice_book_price')[$i],
                    'discount'      => $this->input->post('invoice_book_discount')[$i]
                ];
                $this->db->insert('invoice_book', $book);
            }
            echo json_encode(['status' => TRUE]);
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }
        //view edit invoice
        else {
            $invoice        = $this->invoice->fetch_invoice_id($invoice_id);

            //info customer dan diskon
            $customer = $this->db->select('*')->from('customer')->where('customer_id', $invoice->customer_id)->get()->row();
            $discount_data = $this->db->select('discount')->from('discount')->where('membership', $customer->type)->get()->row();
            $discount = $discount_data->discount;

            $invoice_type = array(
                'credit'      => 'Kredit',
                'online'      => 'Online',
                'cash'        => 'Tunai',
                'showroom'    => 'Showroom',
            );

            $source = array(
                'library'   => 'Perpustakaan',
                'showroom'  => 'Showroom',
                'warehouse' => 'Gudang'
            );

            $customer_type = get_customer_type();

            $invoice_book = $this->invoice->fetch_invoice_book($invoice->invoice_id);

            $dropdown_book_options = $this->invoice->get_ready_book_list();

            $pages       = $this->pages;
            $main_view   = 'invoice/edit_invoice';
            $this->load->view('template', compact('pages', 'invoice', 'invoice_book', 'customer', 'discount', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
        }
    }

    public function action($id, $invoice_status)
    {
        $invoice = $this->invoice->where('invoice_id', $id)->get();
        if (!$invoice) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $this->db->trans_begin();

        // update lembar kerja
        $this->invoice->where('invoice_id', $id)->update([
            'status' => $invoice_status,
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages);
    }

    public function generate_pdf($invoice_id)
    {
        // $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        // $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
        // PDF
        $this->load->library('pdf');

        // $data_format['book_title'] = $invoice_book->book_title ?? '';
        // $data_format['quantity'] = $invoice_book->qty ?? '';
        // $data_format['discount'] = $invoice_book->discount ?? '';
        // $data_format['price'] = $invoice_book->price ?? '';
        // $data_format['total_price_temporary'] = $invoice_book->price * $invoice_book->qty ?? '';
        // $data_format['invoice_discount'] = $invoice_book->price * $invoice_book->qty * ($invoice_book->discount/100) ?? '';
        // $data_format['total_price'] = $invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount/100) ?? '';

        $format = $this->load->view('invoice/view_invoice_pdf');
        $this->pdf->loadHtml($format);

        // (Optional) Setup the paper size and orientation
        $this->pdf->set_paper('A4', 'potrait');

        // Render the HTML as PDF
        $this->pdf->render();
        // $this->pdf->stream("".$invoice_id.".pdf", array('Attachment' => 1));
    }

    public function api_get_book($book_id)
    {
        $book = $this->invoice->get_book($book_id);
        return $this->send_json_output(true, $book);
    }

    public function api_get_customer($customer_id)
    {
        $customer =  $this->invoice->get_customer($customer_id);
        return $this->send_json_output(true, $customer);
    }

    // Auto fill diskon berdasar jenis customer
    public function api_get_discount($customerType)
    {
        $discount = $this->invoice->get_discount($customerType);
        return $this->send_json_output(true, $discount);
    }

    // Auto generate nomor faktur berdasar jenis faktur
    public function api_get_last_invoice_number($type)
    {
        $number = $this->invoice->get_last_invoice_number($type);
        return $this->send_json_output(true, $number);
    }
}
