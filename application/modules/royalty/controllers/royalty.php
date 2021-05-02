<?php defined('BASEPATH') or exit('No direct script access allowed');

class Royalty extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'royalty';
        $this->load->model('royalty_model', 'royalty');
        $this->load->model('book/Book_model', 'book');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        // $filters = [
        //     'keyword'           => $this->input->get('keyword', true),
        //     'invoice_type'      => $this->input->get('invoice_type', true),
        //     'status'            => $this->input->get('status', true),
        //     'customer_type'     => $this->input->get('customer_type', true)
        // ];
        $total_array = [];
        $index = 0;
        $authors = $this->db->select('author_id, author_name')->from('author')->get()->result();
        foreach ($authors as $author) {
            $total_array[$index] = $this->royalty->author_earning($author->author_id)->total;
            // $books_array[$index_books] =  $this->royalty->get_book($author->author_id);
            $index++;
        }
        var_dump($total_array);

        // $total_array = [];
        // $index_total = 0;
        // foreach ($authors as $author) {
        //     $total_array[$index_total] = 0;
        //     foreach ($books_array[$index_total] as $book_array) {
        //         if ($this->royalty->sum_book($book_array->book_id)->total != null) {
        //             $total_array[$index_total] += intval($this->royalty->sum_book($book_array->book_id)->total);
        //         }
        //     }
        //     $index_total++;
        // }

        // $this->invoice->per_page = $this->input->get('per_page', true) ?? 10;

        // $get_data = $this->invoice->filter_invoice($filters, $page);

        // //data invoice
        // $invoice    = $get_data['invoice'];
        // $total      = $get_data['total'];
        // $pagination = $this->invoice->make_pagination(site_url('invoice'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'royalty/index_royalty';
        // $this->load->view('template', compact('pages', 'main_view'));
    }

    public function view()
    {
        $pages          = $this->pages;
        $main_view      = 'royalty/view_royalty';
        // $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        // $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
        // $invoice->customer = $this->invoice->get_customer($invoice->customer_id);

        $this->load->view('template', compact('pages', 'main_view'));
    }
}

//     public function edit($invoice_id)
//     {
//         //post edit invoice
//         if ($_POST) {
//             //validasi input edit
//             $this->invoice->validate_invoice();
//             //Nentuin customer id jika customer diambil dari database
//             if (!empty($this->input->post('customer-id'))) {
//                 $customer_id = $this->input->post('customer-id');
//             }
//             //Nentuin customer id jika customer dibuat baru
//             else {
//                 $add = [
//                     'name'          => $this->input->post('new-customer-name'),
//                     'address'       => $this->input->post('new-customer-address'),
//                     'phone_number'  => $this->input->post('new-customer-phone-number'),
//                     'type'          => $this->input->post('new-customer-type')
//                 ];
//                 $this->db->insert('customer', $add);
//                 $customer_id = $this->db->insert_id();
//             }

//             $edit = [
//                 'number'            => $this->input->post('number'),
//                 'customer_id'       => $customer_id,
//                 'due_date'          => $this->input->post('due-date'),
//                 'type'              => $this->input->post('type'),
//                 'source'            => $this->input->post('source'),
//                 'source_library_id' => $this->input->post('source-library-id'),
//                 'status'            => 'waiting'
//                 // 'date_edited'   => date('Y-m-d H:i:s'),
//                 // 'user_edited'   => $_SESSION['username']
//             ];

//             $this->db->set($edit)->where('invoice_id', $invoice_id)->update('invoice');

//             // Jumlah Buku di Faktur
//             $countsize = count($this->input->post('invoice_book_id'));

//             //hapus invoice_book yang sudah ada 
//             $this->db->where('invoice_id', $invoice_id)->delete('invoice_book');

//             // Masukkan buku di form faktur ke database
//             for ($i = 0; $i < $countsize; $i++) {
//                 $book = [
//                     'invoice_id'    => $invoice_id,
//                     'book_id'       => $this->input->post('invoice_book_id')[$i],
//                     'qty'           => $this->input->post('invoice_book_qty')[$i],
//                     'price'         => $this->input->post('invoice_book_price')[$i],
//                     'discount'      => $this->input->post('invoice_book_discount')[$i]
//                 ];
//                 $this->db->insert('invoice_book', $book);
//             }
//             echo json_encode(['status' => TRUE]);
//             $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
//         }
//         //view edit invoice
//         else {
//             $invoice        = $this->invoice->fetch_invoice_id($invoice_id);

//             //info customer dan diskon
//             $customer = $this->db->select('*')->from('customer')->where('customer_id', $invoice->customer_id)->get()->row();
//             $discount_data = $this->db->select('discount')->from('discount')->where('membership', $customer->type)->get()->row();
//             $discount = $discount_data->discount;

//             $invoice_type = array(
//                 'credit'      => 'Kredit',
//                 'online'      => 'Online',
//                 'cash'        => 'Tunai',
//                 'showroom'    => 'Showroom',
//             );

//             $source = array(
//                 'library'   => 'Perpustakaan',
//                 'showroom'  => 'Showroom',
//                 'warehouse' => 'Gudang'
//             );

//             $customer_type = get_customer_type();

//             $invoice_book = $this->invoice->fetch_invoice_book($invoice->invoice_id);

//             $dropdown_book_options = $this->invoice->get_ready_book_list();

//             $pages       = $this->pages;
//             $main_view   = 'invoice/edit_invoice';
//             $this->load->view('template', compact('pages', 'invoice', 'invoice_book', 'customer', 'discount', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
//         }
//     }

//     public function action($id, $invoice_status)
//     {
//         $invoice = $this->invoice->where('invoice_id', $id)->get();
//         if (!$invoice) {
//             $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
//             redirect($this->pages);
//         }

//         $this->db->trans_begin();

//         // Confirm Faktur
//         if ($invoice_status == 'confirm') {
//             // M T W T F S S
//             // 1 2 3 4 5 6 7
//             if (date('N') < 5) {
//                 $preparing_deadline = date("Y-m-d H:i:s", strtotime("+ 1 day"));
//             } else {
//                 $add_day = 8 - date('N');
//                 $preparing_deadline = date("Y-m-d H:i:s", strtotime("+ " . $add_day . "day"));
//             }
//             $this->invoice->where('invoice_id', $id)->update([
//                 'status' => $invoice_status,
//                 'confirm_date' => now(),
//                 'preparing_deadline' => $preparing_deadline
//             ]);
//         } else
//             // Cancel Faktur
//             if ($invoice_status == 'cancel') {
//                 $this->invoice->where('invoice_id', $id)->update([
//                     'status' => $invoice_status,
//                     'cancel_date' => now(),
//                 ]);
//             }

//         if ($this->db->trans_status() === false) {
//             $this->db->trans_rollback();
//             $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
//         } else {
//             $this->db->trans_commit();
//             $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
//         }

//         redirect($this->pages);
//     }


//     public function api_get_book($book_id)
//     {
//         $book = $this->invoice->get_book($book_id);
//         return $this->send_json_output(true, $book);
//     }

//     public function api_get_customer($customer_id)
//     {
//         $customer =  $this->invoice->get_customer($customer_id);
//         return $this->send_json_output(true, $customer);
//     }

//     // Auto fill diskon berdasar jenis customer
//     public function api_get_discount($customerType)
//     {
//         $discount = $this->invoice->get_discount($customerType);
//         return $this->send_json_output(true, $discount);
//     }

//     // Auto generate nomor faktur berdasar jenis faktur
//     public function api_get_last_invoice_number($type)
//     {
//         $number = $this->invoice->get_last_invoice_number($type);
//         return $this->send_json_output(true, $number);
//     }
// }
