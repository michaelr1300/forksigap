<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Print_order_model extends MY_Model
{
    public $per_page = 10;
    private $preprintfile_directory = 'storage/preprintfile';
    private $printorderfile_directory = 'storage/printorderfile';


    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'order_number',
                'label' => $this->lang->line('form_print_order_number'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'order_code',
                'label' => $this->lang->line('form_print_order_code'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'type',
                'label' => $this->lang->line('form_print_order_type'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'total',
                'label' => $this->lang->line('form_print_order_total'),
                'rules' => 'trim|required|integer',
            ],
            [
                'field' => 'paper_content',
                'label' => $this->lang->line('form_print_order_paper_content'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'paper_cover',
                'label' => $this->lang->line('form_print_order_paper_cover'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'paper_size',
                'label' => $this->lang->line('form_print_order_paper_size'),
                'rules' => 'trim|required',
            ],
        ];

        return $validation_rules;
    }

    public function get_default_values()
    {
        return [
            'book_id'           => '',
            'category'          => '',
            'order_number'      => '',
            'order_code'        => '',
            'total'             => '',
            'paper_divider'     => '',
            'paper_estimation'  => '',
            'print_number'      => '',
            'paper_content'     => '',
            'paper_cover'       => '',
            'paper_size'        => '',
            'type'              => 'pod',
            'date_year'         => '',
            'date_month'        => '',
            'print_order_notes' => '',
            'name'              => '',
            'location_binding'  => 'inside',
            'location_binding_outside'  => '',
            'location_laminate' => 'inside',
            'location_laminate_outside'  => '',
            'print_mode'        => 'book',
            'deadline_date'     => '',
            'non_book_pages'    => ''
        ];
    }

    public function get_print_order($print_order_id)
    {
        return $this->select(['CONCAT_WS(" - ", NULLIF(print_order.name,""), book.book_title) AS title', 'print_order.book_id', 'book.draft_id', 'stock_warehouse', 'book_title', 'book_file', 'book_file_link', 'cover_file', 'cover_file_link', 'book_notes',  'is_reprint', 'book_edition', 'nomor_hak_cipta', 'status_hak_cipta', 'file_hak_cipta', 'file_hak_cipta_link', 'harga', 'theme_id', 'book.draft_id', 'print_order.*'])
            ->join('book')
            ->join_table('draft', 'book', 'draft')
            ->where('print_order_id', $print_order_id)
            ->get();
    }

    public function get_book($book_id)
    {
        return $this->select('book.*')
            ->where('book_id', $book_id)
            ->get('book');
    }

    public function filter_print_order($filters, $page)
    {
        $print_orders = $this->select(['print_order_id', 'print_order.book_id', 'book.draft_id', 'CONCAT_WS(" - ", NULLIF(print_order.name,""), book.book_title) AS title', 'category_name', 'draft.is_reprint', 'print_order.*'])
            ->when('keyword', $filters['keyword'])
            ->when('category', $filters['category'])
            ->when('type', $filters['type'])
            ->when('print_order_status', $filters['print_order_status'])
            ->when('date_year', $filters['date_year'])
            ->when('date_month', $filters['date_month'])
            ->join_table('book', 'print_order', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            // ->order_by("CASE WHEN print_order.print_order_status = 'finish' THEN 1
            //                  ELSE 0 END, print_order.print_order_status", "ASC")
            ->order_by("CASE WHEN print_order.print_order_status = 'waiting' THEN 1
                             WHEN print_order.print_order_status = 'preprint' THEN 2
                             WHEN print_order.print_order_status = 'preprint_approval' THEN 3
                             WHEN print_order.print_order_status = 'preprint_finish' THEN 4
                             WHEN print_order.print_order_status = 'print' THEN 5
                             WHEN print_order.print_order_status = 'print_approval' THEN 6
                             WHEN print_order.print_order_status = 'print_finish' THEN 7
                             WHEN print_order.print_order_status = 'postprint' THEN 8
                             WHEN print_order.print_order_status = 'postprint_approval' THEN 9
                             WHEN print_order.print_order_status = 'postprint_finish' THEN 10
                             WHEN print_order.print_order_status = 'reject' THEN 11
                             WHEN print_order.print_order_status = 'finish' THEN 12
                             ELSE 13 END, print_order.print_order_status", "ASC")
            ->order_by('CASE WHEN UNIX_TIMESTAMP(deadline_date) IS NOT NULL THEN UNIX_TIMESTAMP(deadline_date) ELSE "str" END', 'ASC')
            ->paginate($page)
            ->get_all();

        $total = $this->select('draft.draft_id')
            ->when('keyword', $filters['keyword'])
            ->when('category', $filters['category'])
            ->when('type', $filters['type'])
            ->when('print_order_status', $filters['print_order_status'])
            ->when('date_year', $filters['date_year'])
            ->when('date_month', $filters['date_month'])
            // ->where('print_order.print_order_status !=', 'finish')
            ->join_table('book', 'print_order', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->count();

        return [
            'print_orders' => $print_orders,
            'total'        => $total,
        ];
    }

    public function filter_excel($filters)
    {
        return $this->select(['print_order_id AS id', 'category', 'total', 'CONCAT_WS(" - ", NULLIF(print_order.name,""), book.book_title) AS title', '(CASE WHEN total_postprint IS NOT NULL THEN total_postprint ELSE total_print END) AS total_new', 'entry_date', 'finish_date', 'type', 'preprint_start_date', 'preprint_end_date', 'print_start_date', 'print_end_date', 'postprint_start_date', 'postprint_end_date'])
            ->when('category', $filters['category'])
            ->when('type', $filters['type'])
            ->when('date_year', $filters['date_year'])
            ->when('date_month', $filters['date_month'])
            ->where('print_order.print_order_status', 'finish')
            ->join_table('book', 'print_order', 'book')
            ->order_by('CASE WHEN UNIX_TIMESTAMP(finish_date) IS NOT NULL THEN UNIX_TIMESTAMP(finish_date) ELSE "str" END', 'ASC')
            ->get_all();
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data) {
            if ($params == 'category') {
                $this->where('category', $data);
            }

            if ($params == 'type') {
                $this->where('type', $data);
            }

            if ($params == 'date_year') {
                $this->where('YEAR(print_order.entry_date)', $data);
            }

            if ($params == 'date_month') {
                $this->where('MONTH(print_order.entry_date)', $data);
            }

            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('name', $data);
                $this->or_like('book_title', $data);
                $this->or_like('order_number', $data);
                $this->or_like('order_code', $data);
                $this->group_end();
            }

            if ($params == 'print_order_status') {
                if ($data == 'preprint' || $data == 'print' || $data == 'postprint') {
                    $this->where('print_order_status', $data);
                    $this->or_where('print_order_status', "{$data}_approval");
                    $this->or_where('print_order_status', "{$data}_finish");
                } else {
                    $this->where('print_order_status', $data);
                }
            }
        }
        return $this;
    }

    public function start_progress($print_order_id, $progress)
    {
        // transaction data agar konsisten
        $this->db->trans_begin();

        $input = [
            'print_order_status' => $progress,
            "{$progress}_start_date" => date('Y-m-d H:i:s')
        ];

        $this->print_order->where('print_order_id', $print_order_id)->update($input);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function finish_progress($print_order_id, $progress)
    {
        $input = [
            'print_order_status' => "{$progress}_approval",
            "{$progress}_end_date" => date('Y-m-d H:i:s')
        ];

        $update_state = $this->print_order->where('print_order_id', $print_order_id)->update($input);

        if ($update_state) {
            return true;
        } else {
            return false;
        }
    }

    public function finish_print_postprint($print_order_id)
    {
        $date   =   date('Y-m-d H:i:s');
        $input = [
            'print_order_status' => "print_approval",
            "print_end_date" => $date,
            "postprint_end_date" => $date
        ];

        $update_state = $this->print_order->where('print_order_id', $print_order_id)->update($input);

        if ($update_state) {
            return true;
        } else {
            return false;
        }
    }

    public function upload_print_order_file($field_name, $file_name)
    {
        if (!is_dir($this->printorderfile_directory)) {
            mkdir($this->printorderfile_directory, 0777, TRUE);
        }

        $config = [
            'upload_path'      => $this->printorderfile_directory,
            'file_name'        => $file_name,
            'allowed_types'    => get_allowed_file_types('print_order_file')['types'],
            'max_size'         => 51200,                                           // 50MB
            'overwrite'        => true,
            'file_ext_tolower' => true,
        ];
        $this->load->library('upload', $config);
        if ($this->upload->do_upload($field_name)) {
            // Upload OK, return uploaded file info.
            return $this->upload->data();
        } else {
            // Add error to $_error_array
            $this->form_validation->add_to_error_array($field_name, $this->upload->display_errors('', ''));
            return false;
        }
    }

    public function delete_print_order_file($print_order_file)
    {
        if ($print_order_file && file_exists("$this->printorderfile_directory/$print_order_file")) {
            unlink("$this->printorderfile_directory/$print_order_file");
        }
    }

    public function delete_letter_file($letter_file)
    {
        if ($letter_file) {
            if (file_exists("./printorderletter/$letter_file")) {
                unlink("./printorderletter/$letter_file");
                return true;
            }
            return false;
        }
    }

    public function delete_preprint_file($preprint_file)
    {
        if ($preprint_file && file_exists("$this->preprintfile_directory/$preprint_file")) {
            unlink("$this->preprintfile_directory/$preprint_file");
        }
    }

    public function upload_preprint_file($field_name, $print_order_file_name)
    {
        if (!is_dir($this->preprintfile_directory)) {
            mkdir($this->preprintfile_directory, 0777, TRUE);
        }

        $config = [
            'upload_path'      => $this->preprintfile_directory,
            'file_name'        => $print_order_file_name,
            'allowed_types'    => get_allowed_file_types('preprint_file')['types'],
            'max_size'         => 51200,                                           // 50MB
            'overwrite'        => true,
            'file_ext_tolower' => true,
        ];

        $this->load->library('upload', $config);
        if ($this->upload->do_upload($field_name)) {
            // Upload OK, return uploaded file info.
            return $this->upload->data();
        } else {
            // Add error to $_error_array
            $this->form_validation->add_to_error_array($field_name, $this->upload->display_errors('', ''));
            return false;
        }
    }

    public function get_staff_percetakan()
    {
        return $this->select(['user_id', 'username', 'level', 'email'])
            ->where('level', 'staff_percetakan')
            ->where('is_blocked', 'n')
            ->order_by('username', 'ASC')
            ->get_all('user');
    }

    public function get_staff_percetakan_by_progress($progress, $print_order_id)
    {
        return $this->db->select(['print_order_user_id', 'print_order_user.user_id', 'print_order_id', 'progress', 'username', 'email'])
            ->from('user')
            ->join('print_order_user', 'user.user_id = print_order_user.user_id')
            ->where('print_order_id', $print_order_id)
            ->where('progress', $progress)
            ->get()->result();
    }

    public function check_row_staff_percetakan($print_order_id, $user_id, $progress)
    {
        return $this->db
            ->where(['print_order_id' => $print_order_id, 'user_id' => $user_id, 'progress' => $progress])
            ->get('print_order_user')
            ->num_rows();
    }

    // catatan
    // where('YEAR(entry_date)', $year)->get_all('draft')
}

/* End of file Print_order_model.php */
