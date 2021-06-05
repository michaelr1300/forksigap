<?php defined('BASEPATH') or exit('No direct script access allowed');

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Invoice extends Sales_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'invoice';
        $this->load->model('invoice_model', 'invoice');
        $this->load->model('book/book_model', 'book');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'invoice_type'      => $this->input->get('invoice_type', true),
            'status'            => $this->input->get('status', true),
            'customer_type'     => $this->input->get('customer_type', true)
        ];

        $this->invoice->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->invoice->filter_invoice($filters, $page);
        //data invoice
        $invoice    = $get_data['invoice'];
        foreach ($invoice as $each_invoice) {
            if ($each_invoice->customer_name == NULL) $each_invoice->customer_name = '';
            if ($each_invoice->customer_type == NULL) $each_invoice->customer_type = 'general';
        }
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
            else if (!empty($this->input->post('new-customer-name'))) {
                $add = [
                    'name'          => $this->input->post('new-customer-name'),
                    'address'       => $this->input->post('new-customer-address'),
                    'phone_number'  => $this->input->post('new-customer-phone-number'),
                    'email'         => $this->input->post('new-customer-email'),
                    'type'          => $this->input->post('new-customer-type')
                ];
                $this->db->insert('customer', $add);
                $customer_id = $this->db->insert_id();
            }
            //Customer null (showroom)
            else {
                $customer_id = null;
            }

            $type = $this->input->post('type');
            $status = 'waiting';
            $source = $this->input->post('source') ?? 'warehouse';

            if ($type == 'showroom') {
                $status = 'finish';
                $source = 'showroom';
            }
            $library_id = $this->input->post('source-library-id');
            $add = [
                'number'            => $this->invoice->get_last_invoice_number($type),
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'type'              => $type,
                'source'            => $source,
                'source_library_id' => $library_id,
                'status'            => $status,
                'issued_date'       => $date_created
                // 'user_created'      => $user_created
            ];
            $this->db->insert('invoice', $add);
            // ID faktur terbaru untuk diisi buku
            $invoice_id = $this->db->insert_id();

            // Jumlah Buku di Faktur
            $countsize = count($this->input->post('invoice_book_id'));
            // Total berat buku
            $total_weight = 0;
            // Masukkan buku di form faktur ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book_id = $this->input->post('invoice_book_id')[$i];
                $book = [
                    'invoice_id'    => $invoice_id,
                    'book_id'       => $book_id,
                    'qty'           => $this->input->post('invoice_book_qty')[$i],
                    'price'         => $this->input->post('invoice_book_price')[$i],
                    'discount'      => $this->input->post('invoice_book_discount')[$i],
                    'royalty'       => $this->invoice->get_book_royalty($book_id)
                ];
                $this->db->insert('invoice_book', $book);
                // Hitung berat buku
                $book_weight = $this->invoice->get_book($book['book_id'])->weight;
                $total_weight +=  $book_weight * $book['qty'];

                // Kurangi Stock Buku
                $book_stock = $this->book_stock->where('book_id', $book['book_id'])->get();
                if ($type == 'showroom') {
                    $book_stock->showroom_present -= $book['qty'];
                } else 
                if ($source == 'warehouse') {
                    $book_stock->warehouse_present -= $book['qty'];
                } else
                if ($source == 'library') {
                    // kurangi stock detail perpus
                    $library_stock = ($this->book_stock->get_one_library_stock($book_stock->book_stock_id, $library_id))->library_stock;
                    $book_stock->library_present -= $book['qty'];
                    $library_stock -= $book['qty'];

                    $this->db->set('library_stock', $library_stock)
                        ->where('book_stock_id', $book_stock->book_stock_id)
                        ->where('library_id', $library_id)
                        ->update('library_stock_detail');
                }
                $this->book_stock->where('book_id', $book['book_id'])->update($book_stock);

                // Faktur Showroom tidak mencatat transaksi (karena sumber buku bukan dari gudang)
                if ($type != 'showroom') {
                    // Masukkan transaksi buku
                    $this->book_transaction->insert([
                        'book_id'            => $book['book_id'],
                        'invoice_id'         => $invoice_id,
                        'book_stock_id'      => $book_stock->book_stock_id,
                        'stock_initial'      => $book_stock->warehouse_present + $book['qty'],
                        'stock_mutation'     => $book['qty'],
                        'stock_last'         => $book_stock->warehouse_present,
                        'date'               => $date_created
                    ]);
                }
            }
            $this->db->set('total_weight', $total_weight)->where('invoice_id', $invoice_id)->update('invoice');

            echo json_encode(['status' => TRUE]);
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        }

        //View add invoice
        else {
            $invoice_type = array(
                'credit'      => 'Kredit',
                'online'      => 'Online',
                'cash'        => 'Tunai',
            );

            $source = array(
                'library'   => 'Perpustakaan',
                'warehouse' => 'Gudang'
            );

            $customer_type = get_customer_type();

            $dropdown_book_options = $this->invoice->get_available_book_list('warehouse', '');

            $pages       = $this->pages;
            $main_view   = 'invoice/add_invoice';
            $this->load->view('template', compact('pages', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
        }
    }

    public function add_showroom()
    {
        $customer_type = get_customer_type();

        $dropdown_book_options = $this->invoice->get_available_book_list('showroom', '');

        $pages       = 'invoice/add_showroom';
        $main_view   = 'invoice/add_showroom';
        $this->load->view('template', compact('pages', 'main_view', 'customer_type', 'dropdown_book_options'));
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
                    'email'         => $this->input->post('new-customer-email'),
                    'type'          => $this->input->post('new-customer-type')
                ];
                $this->db->insert('customer', $add);
                $customer_id = $this->db->insert_id();
            }

            $edit = [
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'status'            => 'waiting'
                // 'date_edited'   => date('Y-m-d H:i:s'),
                // 'user_edited'   => $_SESSION['username']
            ];

            $this->db->set($edit)->where('invoice_id', $invoice_id)->update('invoice');

            $invoice = $this->invoice->fetch_invoice_id($invoice_id);
            // Kembalikan stock buku
            $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
            foreach ($invoice_books as $invoice_book) {
                if ($invoice->source == 'warehouse') {
                    $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                    $book_stock->warehouse_present += $invoice_book->qty;
                    $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
                } else 
                if ($invoice->source == 'library') {
                    $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                    $library_id = $invoice->source_library_id;
                    $library_stock = ($this->book_stock->get_one_library_stock($book_stock->book_stock_id, $library_id))->library_stock;
                    $book_stock->library_present += $invoice_book->qty;
                    $library_stock += $invoice_book->qty;
                    $this->db->set('library_stock', $library_stock)
                    ->where('book_stock_id', $book_stock->book_stock_id)
                    ->where('library_id', $library_id)
                    ->update('library_stock_detail');
                }
                $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
            }

            // Hapus invoice_book yang sudah ada 
            $this->db->where('invoice_id', $invoice_id)->delete('invoice_book');

            // Update stock_initial dan stock_last di transaksi yang lebih baru dengan stock setelah dikembalikan
            $book_transactions = $this->db->select('*')->from('book_transaction')->where('invoice_id', $invoice_id)->get()->result();
            foreach ($book_transactions as $book_transaction) {
                $mutation = $book_transaction->stock_mutation;
                $newer_transactions = $this->db->select('*')
                    ->from('book_transaction')
                    ->where('book_transaction_id >', $book_transaction->book_transaction_id)
                    ->where('book_id', $book_transaction->book_id)
                    ->get()->result();
                foreach ($newer_transactions as $newer_transaction) {
                    $newer_transaction->stock_initial += $mutation;
                    $newer_transaction->stock_last += $mutation;
                    $this->book_transaction->where('book_transaction_id', $newer_transaction->book_transaction_id)->update($newer_transaction);
                }
            }
            // Hapus Transaction yang sudah ada
            $this->db->where('invoice_id', $invoice_id)->delete('book_transaction');

            // Jumlah Buku di Faktur
            $countsize = count($this->input->post('invoice_book_id'));

            $total_weight = 0;
           // Masukkan buku di form faktur ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book_id = $this->input->post('invoice_book_id')[$i];
                $book = [
                    'invoice_id'    => $invoice_id,
                    'book_id'       => $book_id,
                    'qty'           => $this->input->post('invoice_book_qty')[$i],
                    'price'         => $this->input->post('invoice_book_price')[$i],
                    'discount'      => $this->input->post('invoice_book_discount')[$i],
                    'royalty'       => $this->invoice->get_book_royalty($book_id)
                ];
                $this->db->insert('invoice_book', $book);
                $book_weight = $this->invoice->get_book($book['book_id'])->weight;
                $total_weight +=  $book_weight * $book['qty'];

                $book_stock = $this->book_stock->where('book_id', $book['book_id'])->get();
                if ($invoice->source == 'warehouse') {
                    $book_stock->warehouse_present -= $book['qty'];
                } else
                if ($invoice->source == 'library') {
                    // kurangi stock detail perpus
                    $library_stock = ($this->book_stock->get_one_library_stock($book_stock->book_stock_id, $library_id))->library_stock;
                    $book_stock->library_present -= $book['qty'];
                    $library_stock -= $book['qty'];

                    $this->db->set('library_stock', $library_stock)
                        ->where('book_stock_id', $book_stock->book_stock_id)
                        ->where('library_id', $library_id)
                        ->update('library_stock_detail');
                }
                $this->book_stock->where('book_id', $book['book_id'])->update($book_stock);

                // Masukkan transaksi buku
                $this->book_transaction->insert([
                    'book_id'            => $book['book_id'],
                    'invoice_id'         => $invoice_id,
                    'book_stock_id'      => $book_stock->book_stock_id,
                    'stock_initial'      => $book_stock->warehouse_present + $book['qty'],
                    'stock_mutation'     => $book['qty'],
                    'stock_last'         => $book_stock->warehouse_present,
                    'date'               => date('Y-m-d H:i:s')
                ]);
            }
            $this->db->set('total_weight', $total_weight)->where('invoice_id', $invoice_id)->update('invoice');

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
            );

            $source = array(
                'library'   => 'Perpustakaan',
                'warehouse' => 'Gudang',
            );

            $customer_type = get_customer_type();

            $invoice_book = $this->invoice->fetch_invoice_book($invoice->invoice_id);


            $dropdown_book_options = $this->invoice->get_available_book_list($invoice->source, $invoice->source_library_id);

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

        if ($invoice->status == 'waiting') {
            // Confirm Faktur
            if ($invoice_status == 'confirm') {
                // M T W T F S S
                // 1 2 3 4 5 6 7
                if (date('N') < 5) {
                    $preparing_deadline = date("Y-m-d H:i:s", strtotime("+ 1 day"));
                } else {
                    $add_day = 8 - date('N');
                    $preparing_deadline = date("Y-m-d H:i:s", strtotime("+ " . $add_day . "day"));
                }
                if ($invoice->source != 'warehouse') {
                    $invoice_status = 'finish';
                }
                $this->invoice->where('invoice_id', $id)->update([
                    'status' => $invoice_status,
                    'confirm_date' => now(),
                    'preparing_deadline' => $preparing_deadline
                ]);
            } else
                // Cancel Faktur
                if ($invoice_status == 'cancel') {
                    $this->invoice->where('invoice_id', $id)->update([
                        'status' => $invoice_status,
                        'cancel_date' => now(),
                    ]);
                    $invoice_books  = $this->invoice->fetch_invoice_book($id);
                    // Harga dibuat 0 (Untuk bagian pendapatan)
                    foreach ($invoice_books as $invoice_book) {
                        $this->db->set('price', 0)->where('invoice_id', $id)->update('invoice_book');
                    }


                    if ($invoice->type != 'showroom') {
                        // Kembalikan stock buku
                        $invoice_books  = $this->invoice->fetch_invoice_book($id);
                        foreach ($invoice_books as $invoice_book) {
                            if ($invoice->source == 'warehouse') {
                                $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                                $book_stock->warehouse_present += $invoice_book->qty;
                                $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
                            } else 
                            if ($invoice->source == 'library') {
                                $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                                $library_id = $invoice->source_library_id;
                                $library_stock = ($this->book_stock->get_one_library_stock($book_stock->book_stock_id, $library_id))->library_stock;
                                $book_stock->library_present += $invoice_book->qty;
                                $library_stock += $invoice_book->qty;
                                $this->db->set('library_stock', $library_stock)
                                ->where('book_stock_id', $book_stock->book_stock_id)
                                ->where('library_id', $library_id)
                                ->update('library_stock_detail');
                            }
                            $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
                        }
                        if ($invoice->source == 'warehouse') {
                            // Update stock_initial dan stock_last di transaksi yang lebih baru dengan stock setelah dikembalikan
                            $book_transactions = $this->db->select('*')->from('book_transaction')->where('invoice_id', $id)->get()->result();
                            foreach ($book_transactions as $book_transaction) {
                                $mutation = $book_transaction->stock_mutation;
                                $newer_transactions = $this->db->select('*')
                                    ->from('book_transaction')
                                    ->where('book_transaction_id >', $book_transaction->book_transaction_id)
                                    ->where('book_id', $book_transaction->book_id)
                                    ->get()->result();
                                foreach ($newer_transactions as $newer_transaction) {
                                    $newer_transaction->stock_initial += $mutation;
                                    $newer_transaction->stock_last += $mutation;
                                    $this->book_transaction->where('book_transaction_id', $newer_transaction->book_transaction_id)->update($newer_transaction);
                                }
                            }
                        }
                        // Hapus Transaction yang sudah ada
                        $this->db->where('invoice_id', $id)->delete('book_transaction');
                    }
                }
        } else
        if ($invoice->status == 'preparing_finish') {
            // Finish Faktur
            if ($invoice_status == 'finish') {
                $this->invoice->where('invoice_id', $id)->update([
                    'status' => $invoice_status,
                    'finish_date' => now(),
                ]);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else if ($this->db->trans_status() != false && $invoice_status == 'confirm') {
            $this->db->trans_commit();
            $this->session->set_flashdata('confirm_invoice', 'Permintaan diteruskan ke gudang');
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages);
    }

    public function update_delivery_fee($invoice_id)
    {
        $edit = [
            'delivery_fee' => $this->input->post('delivery_fee'),
            'receipt' => $this->input->post('receipt')
        ];
        $this->db->set($edit)->where('invoice_id', $invoice_id)->update('invoice');
        echo json_encode(['status' => TRUE]);
    }

    public function generate_pdf($invoice_id)
    {
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        if ($invoice->status != 'waiting' && $invoice->status != 'cancel') {
            $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
            $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
            $customer       = $this->invoice->get_customer($invoice->customer_id);

            // PDF
            $this->load->library('pdf');
            $data_format['invoice'] = $invoice ?? '';
            $data_format['invoice_books'] = $invoice_books ?? '';
            $data_format['customer'] = $customer ?? '';

            $html = $this->load->view('invoice/view_invoice_pdf', $data_format, true);

            $file_name = $invoice->number . '_Invoice';

            $this->pdf->generate_pdf_a4_portrait($html, $file_name);
        }
    }

    public function print_showroom_receipt()
    {
        // me-load library escpos
        // $this->load->library('Escpose');
        // membuat connector printer ke shared printer bernama "printer_a"
        $connector = new WindowsPrintConnector("printer_a");

        // membuat objek $printer agar dapat di lakukan fungsinya
        $printer = new Printer($connector);

        // membuat fungsi untuk membuat 1 baris tabel, agar dapat dipanggil berkali-kali dengan mudah
        function buatBaris4Kolom($kolom1, $kolom2, $kolom3, $kolom4)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 12;
            $lebar_kolom_2 = 8;
            $lebar_kolom_3 = 8;
            $lebar_kolom_4 = 9;

            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);
            $kolom4 = wordwrap($kolom4, $lebar_kolom_4, "\n", true);

            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
            $kolom3Array = explode("\n", $kolom3);
            $kolom4Array = explode("\n", $kolom4);

            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array), count($kolom4Array));

            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();

            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {

                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");

                // memberikan rata kanan pada kolom 3 dan 4 karena akan kita gunakan untuk harga dan total harga
                $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);
                $hasilKolom4 = str_pad((isset($kolom4Array[$i]) ? $kolom4Array[$i] : ""), $lebar_kolom_4, " ", STR_PAD_LEFT);

                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3 . " " . $hasilKolom4;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n", $hasilBaris) . "\n";
        }

        // Membuat judul
        $printer->initialize();
        $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT); // Setting teks menjadi lebih besar
        $printer->setJustification(Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
        $printer->text("UGM Press\n");
        $printer->text("\n");

        // Data transaksi
        $printer->initialize();
        $printer->text("Kasir : Andrew\n");
        $printer->text("Waktu : 23-04-2021 19:23:22\n");

        // Membuat tabel
        $printer->initialize(); // Reset bentuk/jenis teks
        $printer->text("----------------------------------------\n");
        $printer->text(buatBaris4Kolom("Barang", "qty", "Harga", "Subtotal"));
        $printer->text("----------------------------------------\n");
        $printer->text(buatBaris4Kolom("Makaroni 250gr", "2pcs", "15.000", "30.000"));
        $printer->text(buatBaris4Kolom("Telur", "2pcs", "5.000", "10.000"));
        $printer->text(buatBaris4Kolom("Tepung terigu", "1pcs", "8.200", "16.400"));
        $printer->text("----------------------------------------\n");
        $printer->text(buatBaris4Kolom('', '', "Total", "56.400"));
        $printer->text("\n");

        // Pesan penutup
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Terima kasih telah berbelanja\n");
        $printer->text("UGM Press Yogyakarta\n");

        $printer->feed(5); // mencetak 5 baris kosong agar terangkat (pemotong kertas saya memiliki jarak 5 baris dari toner)
        $printer->close();
    }

    public function api_get_book($book_id)
    {
        $book = $this->invoice->get_book($book_id);
        return $this->send_json_output(true, $book);
    }

    public function api_get_book_dynamic_stock($book_id, $source, $library_id)
    {
        $book = $this->invoice->get_book_dynamic_stock($book_id, $source, $library_id);
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

    public function api_get_book_dropdown($type, $library_id='')
    {
        $data = $this->invoice->get_available_book_list($type, $library_id);
        return $this->send_json_output(true, $data);
    }

    public function debug($book_id) {
        $b = $this->invoice->get_book_royalty($book_id);
        $a = ($this->book_stock->get_one_library_stock(13, 1))->library_stock;
        var_dump($b);
    }
}
