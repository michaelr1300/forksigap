<?php defined('BASEPATH') or exit('No direct script access allowed');

class Proforma extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'proforma';
        $this->load->model('proforma_model', 'proforma');
        $this->load->model('book/Book_model', 'book');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        echo ('proforma');
        // $filters = [
        //     'keyword'           => $this->input->get('keyword', true),
        //     'invoice_type'      => $this->input->get('invoice_type', true),
        //     'status'            => $this->input->get('status', true),
        //     'customer_type'     => $this->input->get('customer_type', true)
        // ];

        // $this->proforma->per_page = $this->input->get('per_page', true) ?? 10;

        // $get_data = $this->proforma->filter_invoice($filters, $page);

        // //data invoice
        // $proforma    = $get_data['invoice'];
        // $total      = $get_data['total'];
        // $pagination = $this->proforma->make_pagination(site_url('proforma'), 2, $total);

        // $pages      = $this->pages;
        // $main_view  = 'proforma/index_proforma';
        // $this->load->view('template', compact('pages', 'main_view', 'proforma', 'pagination', 'total'));
    }

    public function view($invoice_id)
    {
        $pages          = $this->pages;
        $main_view      = 'invoice/view_invoice';
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
        $invoice->customer = $this->invoice->get_customer($invoice->customer_id);

        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'invoice_books'));
    }

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
                'source_library_id' => $this->input->post('source-library-id'),
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
                'source_library_id' => $this->input->post('source-library-id'),
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
