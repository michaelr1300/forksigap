<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'invoice';
        $this->load->model('invoice_model', 'invoice');
        $this->load->model('book/Book_model', 'book');
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

        $invoice = $get_data['invoice'];
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

        $customer_type = array(
            'distributor'      => 'Distributor',
            'reseller'      => 'Reseller',
            'penulis'        => 'Penulis',
            'member'        => 'Member',
            'biasa'        => ' - '
        );

        $dropdown_book_options = $this->invoice->get_ready_book_list();

        $pages       = $this->pages;
        $main_view   = 'invoice/add_invoice';
        $this->load->view('template', compact('pages', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
    }

    // Store data
    public function add_invoice()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('number', 'Nomor Faktur', 'required');
        $this->form_validation->set_rules('due-date', 'Jatuh Tempo', 'required');
        $this->form_validation->set_rules('type', 'Tipe Faktur', 'required');
        $this->form_validation->set_rules('invoice_book_id[]', 'Buku Invoice', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Faktur gagal ditambah.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->invoice->add_invoice();
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Faktur berhasil ditambah.');
                redirect('invoice');
            } else {
                $this->session->set_flashdata('error', 'Faktur gagal ditambah 2.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
    }

    // View Edit
    public function edit($invoice_id)
    {
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);

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

        $customer_type = array(
            'distributor'      => 'Distributor',
            'reseller'      => 'Reseller',
            'penulis'        => 'Penulis',
            'member'        => 'Member',
            'biasa'        => ' - '
        );

        $invoice_book = $this->invoice->fetch_invoice_book($invoice->invoice_id);

        $dropdown_book_options = $this->invoice->get_ready_book_list();

        $pages       = $this->pages;
        $main_view   = 'invoice/edit_invoice';
        $this->load->view('template', compact('pages', 'invoice', 'invoice_book', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
    }

    // Update data
    public function edit_invoice($invoice_id)
    {
        $this->form_validation->set_rules('number', 'Nomor Faktur', 'required');
        $this->form_validation->set_rules('due-date', 'Jatuh Tempo', 'required');
        $this->form_validation->set_rules('type', 'Tipe Faktur', 'required');
        $this->form_validation->set_rules('invoice_book_id[]', 'Buku Invoice', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Invoice gagal diubah.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->invoice->edit_invoice($invoice_id);
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Invoice berhasil diubah.');
                redirect('invoice/view/' . $invoice_id);
            } else {
                $this->session->set_flashdata('error', 'Invoice gagal diubah.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
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
