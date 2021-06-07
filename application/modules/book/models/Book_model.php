<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_model extends MY_Model
{
    public $per_page = 10;
    private $bookfile_directory = 'storage/bookfile';
    private $hakcipta_directory = 'storage/hakcipta';

    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'from_outside',
                'label' => 'Asal Buku',
                'rules' => 'trim|required',
            ],
            [
                'field' => 'draft_id',
                'label' => 'Draft ID',
                'rules' => 'trim|callback_unique_data[draft_id]|callback_required_draft_id',
            ],
            [
                'field' => 'book_code',
                'label' => 'Book Code',
                'rules' => 'trim',
            ],
            [
                'field' => 'book_title',
                'label' => 'Book Title',
                'rules' => 'trim|required|min_length[1]|max_length[256]|callback_unique_data[book_title]',
            ],
            [
                'field' => 'book_edition',
                'label' => 'Book Edition',
                'rules' => 'trim|min_length[1]|max_length[256]',
            ],
            [
                'field' => 'book_pages',
                'label' => 'Book pages',
                'rules' => 'trim|min_length[1]|max_length[256]',
            ],
            [
                'field' => 'isbn',
                'label' => 'ISBN',
                'rules' => 'trim|min_length[1]|max_length[256]',
            ],
            [
                'field' => 'eisbn',
                'label' => 'EISBN',
                'rules' => 'trim|min_length[1]|max_length[256]',
            ],
            [
                'field' => 'published_date',
                'label' => 'Published Date',
                'rules' => 'trim|callback_is_date_format_valid',
            ],
            [
                'field' => 'harga',
                'label' => 'Harga',
                'rules' => 'trim',
            ],
            [
                'field' => 'weight',
                'label' => 'Berat',
                'rules' => 'trim',
            ],
            [
                'field' => 'book_notes',
                'label' => 'Book Note',
                'rules' => 'trim',
            ],
            [
                'field' => 'is_reprint',
                'label' => 'Is Reprint',
                'rules' => 'trim',
            ],
            [
                'field' => 'nomor_hak_cipta',
                'label' => 'Nomor Hak Cipta',
                'rules' => 'trim',
            ],
            [
                'field' => 'status_hak_cipta',
                'label' => 'Status Hak Cipta',
                'rules' => 'trim',
            ],

        ];

        return $validation_rules;
    }

    public function get_default_values()
    {
        return [
            'from_outside'        => 0,
            'draft_id'            => '',
            'book_code'           => '',
            'book_title'          => '',
            'book_edition'        => '',
            'book_file_link'      => '',
            'book_pages'          => '',
            'isbn'                => '',
            'eisbn'               => '',
            'book_file'           => '',
            'published_date'      => '',
            'harga'               => '',
            'weight'              => 0,
            'book_notes'          => '',
            'is_reprint'          => 'n',
            'nomor_hak_cipta'     => '',
            'status_hak_cipta'    => '',
            'file_hak_cipta_link' => '',
        ];
    }

    public function filter_book($filters, $page)
    {
        $books = $this->select(['book_id', 'book.draft_id', 'book_title', 'category_name', 'published_date', 'work_unit_name', 'book_code', 'isbn', 'status_hak_cipta', 'is_reprint', 'author_name', 'from_outside'])
            ->when('keyword', $filters['keyword'])
            ->join('draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->join_table('work_unit', 'author', 'work_unit')
            ->when('category', $filters['category'])
            ->when('status', $filters['status'])
            ->when('reprint', $filters['reprint'])
            ->when('published_year', $filters['published_year'])
            ->when('from_outside', $filters['from_outside'])
            ->order_by('status_hak_cipta')
            ->order_by('published_date')
            ->order_by('book_title')
            ->group_by('draft.draft_id')
            ->paginate($page)
            ->get_all();

        $total = $this->select('draft.draft_id')
            ->when('keyword', $filters['keyword'])
            ->join('draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->join_table('work_unit', 'author', 'work_unit')
            ->when('category', $filters['category'])
            ->when('status', $filters['status'])
            ->when('reprint', $filters['reprint'])
            ->when('published_year', $filters['published_year'])
            ->when('from_outside', $filters['from_outside'])
            ->order_by('status_hak_cipta')
            ->order_by('published_date')
            ->order_by('book_title')
            ->group_by('draft.draft_id')
            ->count();

        // get authors
        foreach ($books as $b) {
            if ($b->draft_id) {
                $b->authors = $this->get_id_and_name('author', 'draft_author', $b->draft_id, 'draft');
            } else {
                $b->authors = [];
            }
        }

        return [
            'books' => $books,
            'total'  => $total,
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data) {
            if ($params == 'reprint') {
                $this->where('is_reprint', $data);
            }

            if ($params == 'category') {
                $this->where('draft.category_id', $data);
            }

            if ($params == 'status') {
                $this->where('book.status_hak_cipta', $data == 'done' ? 2 : 1);
            }

            if ($params == 'published_year') {
                $this->where('year(published_date)', $data);
            }

            if ($params == 'keyword') {
                $this->group_start();
                // $this->like('draft_title', $data);
                $this->or_like('book_title', $data);
                // if ($this->session->userdata('level') != 'reviewer') {
                $this->or_like('author_name', $data);
                // }
                $this->group_end();
            }

            if ($params == 'from_outside') {
                $this->where('from_outside', $data);
            }

            // if ($params == 'status') {
            //     if ($data == 'y') {
            //         $this->where_not('edit_notes', '');
            //     } elseif ($data == 'n') {
            //         $this->where('edit_notes', '');
            //     }
            // }
        }
        return $this;
    }

    public function get_book_from_draft($draft_id)
    {
        return $this->select('book_id,book_title,nomor_hak_cipta,status_hak_cipta,file_hak_cipta,file_hak_cipta_link,from_outside')
            ->where('book.draft_id', $draft_id)
            ->join_table('draft', 'book', 'draft')
            ->get();
    }

    public function upload_book_file($field_name, $book_file_name)
    {
        if (!is_dir($this->bookfile_directory)) {
            mkdir($this->bookfile_directory, 0777, TRUE);
        }

        $config = [
            'upload_path'      => $this->bookfile_directory,
            'file_name'        => $book_file_name,
            'allowed_types'    => get_allowed_file_types('book_file')['types'],
            'max_size'         => 51200, // 15MB
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

    public function delete_book_file($book_file_name)
    {
        if ($book_file_name && file_exists("$this->bookfile_directory/$book_file_name")) {
            unlink("$this->bookfile_directory/$book_file_name");
        }
    }

    public function uploadHCfile($field_name, $hakcipta_file_name)
    {
        if (!is_dir($this->hakcipta_directory)) {
            mkdir($this->hakcipta_directory, 0777, TRUE);
        }

        $config = [
            'upload_path'      => $this->hakcipta_directory,
            'file_name'        => $hakcipta_file_name,
            'allowed_types'    => get_allowed_file_types('hakcipta_file')['types'], // file types allowed
            'max_size'         => 15360, // 15MB
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

    public function delete_hak_cipta_file($hakcipta_file_name)
    {
        if ($hakcipta_file_name && file_exists("$this->hakcipta_directory/$hakcipta_file_name")) {
            unlink("$this->hakcipta_directory/$hakcipta_file_name");
        }
    }

    //     public function uploadCover($coverfieldname, $coverFileName)
    //    {
    //        $config = [
    //            'upload_path'      => './cover/',
    //            'file_name'        => $coverFileName,
    //            'allowed_types'    => 'jpg|jpeg|png',    //  *.jpg only
    //            'max_size'         => 10240,     // 10MB
    //            'max_width'        => 0,
    //            'max_height'       => 0,
    //            'overwrite'        => true,
    //            'file_ext_tolower' => true,
    //        ];
    //
    //        $this->load->library('upload', $config);
    //        if ($this->upload->do_upload($coverfieldname)) {
    //            // Upload OK, return uploaded file info.
    //            return $this->upload->data();
    //        } else {
    //            // Add error to $_error_array
    //            $this->form_validation->add_to_error_array($coverfieldname, $this->upload->display_errors('', ''));
    //            return false;
    //        }
    //    }
    //
    //
    //    public function coverResize($coverfieldname, $source_path, $width, $height)
    //    {
    //        $config = [
    //            'image_library'  => 'gd2',
    //            'source_image'   => $source_path,
    //            'maintain_ratio' => true,
    //            'width'          => $width,
    //            'height'         => $height,
    //        ];
    //
    //        $this->load->library('image_lib', $config);
    //
    //        if ($this->image_lib->resize()) {
    //            return true;
    //        } else {
    //            $this->form_validation->add_to_error_array($coverfieldname, $this->image_lib->display_errors('', ''));
    //            return false;
    //        }
    //    }
    //
    //    public function deleteCover($imgFile)
    //    {
    //        if (file_exists("./cover/$imgFile")) {
    //            unlink("./cover/$imgFile");
    //        }
    //    }

    // private function _get_draft_authors_and_status(array $books)
    // {
    //     foreach ($books as $b) {
    //         $b->authors = $this->get_id_and_name('author', 'draft_author', $b->draft_id, 'draft');
    //     }

    //     return $books;
    // }

}
