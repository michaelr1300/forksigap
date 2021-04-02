<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_model extends MY_Model
{
    public $per_page = 10;

    public function add_invoice()
    {
        $date_created       = date('Y-m-d H:i:s');

        //Nentuin customer id
        if (!empty($this->input->post('customer-id'))) {
            $customer_id = $this->input->post('customer-id');
        } else {
            $type = $this->input->post('new-customer-type');
            $add = [
                'name'          => $this->input->post('new-customer-name'),
                'type'          => $type
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
        $countsize = $this->input->post('invoice_book_id');

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
        return TRUE;
    }

    public function edit_invoice($invoice_id)
    {
        if (!empty($this->input->post('customer-id'))) {
            $customer_id = $this->input->post('customer-id');
        } else {
            $type = $this->input->post('new-customer-type');
            $add = [
                'name'          => $this->input->post('new-customer-name'),
                'type'          => $type
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
        $countsize = $this->input->post('invoice_book_id');

        // Masukkan buku di form faktur ke database
        $this->db->where('invoice_id', $invoice_id)->delete('invoice_book');
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
        return TRUE;
    }

    public function fetch_invoice_id($invoice_id)
    {
        return $this->db
            ->select('*')
            ->from('invoice')
            ->where('invoice_id', $invoice_id)
            ->get()
            ->row();
    }

    public function fetch_invoice_book($invoice_id)
    {
        return $this->db
            ->select('invoice_book.*, book.book_title, book.harga')
            ->from('invoice_book')
            ->join('book', 'book.book_id = invoice_book.book_id')
            ->where('invoice_id', $invoice_id)
            ->get()
            ->result();
    }

    public function fetch_book_info($book_id)
    {
        return $this->db
            ->select('book_title')
            ->from('book')
            ->where('book_id', $book_id)
            ->get()
            ->row();
    }

    public function get_ready_book_list()
    {
        $books = $this->db
            ->select('book_id, book_title')
            ->order_by('book_title', 'ASC')
            ->from('book')
            ->get()
            ->result();
        foreach ($books as $book) {
            // Tambahkan data stock ke buku
            $stock = $this->db
                ->select('warehouse_present')
                ->from('book_stock')
                ->where('book_id', $book->book_id)
                ->order_by("UNIX_TIMESTAMP(date)", "DESC")
                ->limit(1)
                ->get()
                ->row();
            if ($stock == NULL)
                $book->stock = 0;
            else
                $book->stock = $stock->warehouse_present;
        }

        // Buku stock 0 tidak ditampilkan
        foreach ($books as $key => $book) {
            if ($book->stock == 0) {
                unset($books[$key]);
            }
        }

        // Input buku ke array untuk dropdown
        $options = ['' => '-- Pilih --'];
        foreach ($books as $book) {
            $options += [$book->book_id => $book->book_title];
        }

        return $options;
    }

    public function get_book($book_id)
    {
        $book = $this->select('book.*')
            ->where('book_id', $book_id)
            ->get('book');

        $stock = $this->db->select('warehouse_present')->from('book_stock')->where('book_id', $book_id)->order_by("UNIX_TIMESTAMP(date)", "DESC")->limit(1)->get()->row();

        if ($stock == NULL) {
            $book->stock = 0;
        } else {
            $book->stock = $stock->warehouse_present;
        }
        return $book;
    }

    public function get_discount($type)
    {
        return $this->select('discount')->where('membership', $type)->get('discount');
    }

    public function get_customer($customer_id)
    {
        $this->db->select('customer_id, name, address, phone_number, type, discount');
        $this->db->from('customer');
        $this->db->join('discount', 'customer.type = discount.membership', 'left');
        $this->db->where('customer.customer_id', $customer_id);
        return $this->db->get()->row();
    }

    public function get_last_invoice_number($type)
    {
        $initial = '';
        switch ($type) {
            case 'credit':
                $initial = 'K';
                break;
            case 'cash':
                $initial = 'T';
                break;
            case 'online':
                $initial = 'O';
                break;
            case 'showroom':
                $initial = 'S';
                break;
        }
        $date_created       = substr(date('Ymd'), 2);
        $data = $this->db->select('*')->where('type', $type)->count_all_results('invoice') + 1;
        $invoiceNumber = $initial . $date_created . '-' . str_pad($data, 6, 0, STR_PAD_LEFT);
        return $invoiceNumber;
    }

    public function filter_invoice($filters, $page)
    {
        $invoice = $this->select(['invoice_id', 'number', 'issued_date', 'due_date', 'status', 'type'])
            ->when('keyword', $filters['keyword'])
            ->when('type', $filters['type'])
            ->when('status', $filters['status'])
            ->order_by('invoice_id', 'DESC')
            ->paginate($page)
            ->get_all();

        $total = $this->select(['invoice_id', 'number'])
            ->when('keyword', $filters['keyword'])
            ->when('type', $filters['type'])
            ->when('status', $filters['status'])
            ->order_by('invoice_id')
            ->count();

        return [
            'invoice'  => $invoice,
            'total' => $total
        ];
    }

    public function fetch_warehouse_stock($book_id)
    {

        $stock       = $this->db->select('warehouse_present')->from('book_stock')->where('book_id', $book_id)->order_by("UNIX_TIMESTAMP(date)", "DESC")->limit(1)->get()->row();
        return [
            'stock'    => $stock
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data != '') {
            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('number', $data);
                $this->group_end();
            } else {
                $this->group_start();
                $this->or_like('type', $data);
                $this->or_like('status', $data);
                $this->group_end();
            }
        }
        return $this;
    }
}
