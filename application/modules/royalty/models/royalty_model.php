<?php defined('BASEPATH') or exit('No direct script access allowed');

class Royalty_model extends MY_Model
{
    public $per_page = 10;

    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'user_id',
                'label' => $this->lang->line('form_user_name'),
                'rules' => 'trim|callback_unique_data[user_id]',
            ],
            [
                'field' => 'work_unit_id',
                'label' => $this->lang->line('form_work_unit_name'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'institute_id',
                'label' => $this->lang->line('form_institute_name'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'author_name',
                'label' => $this->lang->line('form_author_name'),
                'rules' => 'trim|required|min_length[1]|max_length[256]',
            ],
            [
                'field' => 'author_nip',
                'label' => $this->lang->line('form_author_nip'),
                'rules' => 'trim|required|numeric|min_length[3]|max_length[256]|callback_unique_data[author_nip]',
            ],
            [
                'field' => 'author_degree_front',
                'label' => $this->lang->line('form_author_degree_front'),
                'rules' => 'trim|min_length[2]|max_length[256]',
            ],
            [
                'field' => 'author_degree_back',
                'label' => $this->lang->line('form_author_degree_back'),
                'rules' => 'trim|min_length[2]|max_length[256]',
            ],
            [
                'field' => 'author_latest_education',
                'label' => $this->lang->line('form_author_latest_education'),
                'rules' => 'trim',
            ],
            [
                'field' => 'author_address',
                'label' => $this->lang->line('form_author_address'),
                'rules' => 'trim|max_length[256]',
            ],
            [
                'field' => 'author_contact',
                'label' => $this->lang->line('form_author_contact'),
                'rules' => 'trim|max_length[20]|callback_unique_data[author_contact]',
            ],
            [
                'field' => 'author_email',
                'label' => $this->lang->line('form_author_email'),
                'rules' => 'trim|valid_email|callback_unique_data[author_email]',
            ],
        ];

        return $validation_rules;
    }

    public function get_default_values()
    {
        return [
            'work_unit_id'            => null,
            'institute_id'            => null,
            'author_nip'              => null,
            'author_name'             => null,
            'author_degree_front'     => null,
            'author_degree_back'      => null,
            'author_latest_education' => null,
            'author_address'          => null,
            'author_contact'          => null,
            'author_email'            => null,
            'bank_id'                 => null,
            'author_saving_num'       => null,
            'heir_name'               => null,
            'user_id'                 => null,
            'author_ktp'              => null,
        ];
    }

    public function get_data($keywords, $page = null)
    {
        $query = $this->select('author_id,author_nip,author_name,author_degree_front,author_degree_back,work_unit_name,institute_name,username,author.user_id')
            ->like('work_unit_name', $keywords)
            ->or_like('institute_name', $keywords)
            ->or_like('author_nip', $keywords)
            ->or_like('author_name', $keywords)
            ->or_like('username', $keywords)
            ->join('work_unit')
            ->join('institute')
            ->join('bank')
            ->join('user')
            ->order_by('author.work_unit_id')
            ->order_by('author.institute_id')
            ->order_by('author_name');

        return [
            'data'  => $query->paginate($page)->get_all(),
            'count' => $this
                ->like('work_unit_name', $keywords)
                ->or_like('institute_name', $keywords)
                ->or_like('author_nip', $keywords)
                ->or_like('author_name', $keywords)
                ->or_like('username', $keywords)
                ->join('work_unit')
                ->join('institute')
                ->join('bank')
                ->join('user')
                ->count(),
        ];
    }
    // public function validate_invoice()
    // {
    //     $data = array();
    //     $data['input_error'] = array();
    //     $data['status'] = TRUE;

    //     if ($this->input->post('type') == '') {
    //         $data['input_error'][] = 'error-type';
    //         $data['status'] = FALSE;
    //     } else if ($this->input->post('type') == 'cash') {
    //         if ($this->input->post('source') == '') {
    //             $data['input_error'][] = 'error-source';
    //             $data['status'] = FALSE;
    //         }
    //     } else if ($this->input->post('type') == 'credit' || $this->input->post('type') == 'online') {
    //         if ($this->input->post('due-date') == '') {
    //             $data['input_error'][] = 'error-due-date';
    //             $data['status'] = FALSE;
    //         }
    //     }

    //     if ($this->input->post('source') == 'library') {
    //         if ($this->input->post('source-library-id') == '') {
    //             $data['input_error'][] = 'error-source-library';
    //             $data['status'] = FALSE;
    //         }
    //     }

    //     if ($this->input->post('customer-id') == '') {
    //         if ($this->input->post('new-customer-name') == '' && $this->input->post('new-customer-phone-number') == '') {
    //             $data['input_error'][] = 'error-customer-info';
    //             $data['status'] = FALSE;
    //         } else {
    //             if ($this->input->post('new-customer-name') == '') {
    //                 $data['input_error'][] = 'error-new-customer-name';
    //                 $data['status'] = FALSE;
    //             }
    //             if ($this->input->post('new-customer-phone-number') == '') {
    //                 $data['input_error'][] = 'error-new-customer-phone-number';
    //                 $data['status'] = FALSE;
    //             }
    //             if ($this->input->post('new-customer-type') == '') {
    //                 $data['input_error'][] = 'error-new-customer-type';
    //                 $data['status'] = FALSE;
    //             }
    //         }
    //     }

    //     if (empty($this->input->post('invoice_book_id'))) {
    //         $data['input_error'][] = 'error-no-book';
    //         $data['status'] = FALSE;
    //     }

    //     if ($data['status'] === FALSE) {
    //         echo json_encode($data);
    //         exit();
    //     }
    // }

    // public function fetch_invoice_book($invoice_id)
    // {
    //     return $this->db
    //         ->select('invoice_book.*, book.book_title, book.harga')
    //         ->from('invoice_book')
    //         ->join('book', 'book.book_id = invoice_book.book_id')
    //         ->where('invoice_id', $invoice_id)
    //         ->get()
    //         ->result();
    // }

    // public function fetch_book_info($book_id)
    // {
    //     return $this->db
    //         ->select('book_title')
    //         ->from('book')
    //         ->where('book_id', $book_id)
    //         ->get()
    //         ->row();
    // }

    //     // Input buku ke array untuk dropdown
    //     $options = ['' => '-- Pilih --'];
    //     foreach ($books as $book) {
    //         $options += [$book->book_id => $book->book_title];
    //     }

    //     return $options;
    // }

    //     return $options;
    // }

    // public function get_book($book_id)
    // {
    //     $book = $this->select('book.*')
    //         ->where('book_id', $book_id)
    //         ->get('book');

    //     $stock = $this->fetch_warehouse_stock($book_id);

    //     if ($stock == NULL) {
    //         $book->stock = 0;
    //     } else {
    //         $book->stock = $stock->warehouse_present;
    //     }
    //     return $book;
    // }


    // public function filter_invoice($filters, $page)
    // {
    //     $invoice = $this->select(['invoice_id', 'number', 'issued_date', 'due_date', 'invoice.customer_id', 'name as customer_name', 'customer.type as customer_type', 'status', 'invoice.type as invoice_type'])
    //         ->join('customer', 'invoice.customer_id = customer.customer_id', 'left')
    //         ->when('keyword', $filters['keyword'])
    //         ->when('invoice_type', $filters['invoice_type'])
    //         ->when('customer_type', $filters['customer_type'])
    //         ->when('status', $filters['status'])
    //         ->order_by('invoice_id', 'DESC')
    //         ->paginate($page)
    //         ->get_all();

    //     $total = $this->select(['invoice_id', 'number', 'name'])
    //         ->join('customer', 'invoice.customer_id = customer.customer_id', 'left')
    //         ->when('keyword', $filters['keyword'])
    //         ->when('invoice_type', $filters['invoice_type'])
    //         ->when('customer_type', $filters['customer_type'])
    //         ->when('status', $filters['status'])
    //         ->order_by('invoice_id')
    //         ->count();

    //     return [
    //         'invoice'  => $invoice,
    //         'total' => $total
    //     ];
    // }

    // public function when($params, $data)
    // {
    //     // jika data null, maka skip
    //     if ($data != '') {
    //         if ($params == 'keyword') {
    //             $this->group_start();
    //             $this->or_like('number', $data);
    //             $this->or_like('name', $data);
    //             $this->group_end();
    //         } else {
    //             $this->group_start();
    //             $this->or_like('invoice.type', $data);
    //             $this->or_like('customer.type', $data);
    //             $this->or_like('status', $data);
    //             $this->group_end();
    //         }
    //     }
    //     return $this;
    // }

    
}
