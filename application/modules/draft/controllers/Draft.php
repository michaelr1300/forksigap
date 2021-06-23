<?php defined('BASEPATH') or exit('No direct script access allowed');

use Carbon\Carbon;

class Draft extends Operator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'draft';

        // load other models
        $this->load->model('worksheet/worksheet_model', 'worksheet');
        $this->load->model('author/author_model', 'author');
        $this->load->model('reviewer/reviewer_model', 'reviewer');
        $this->load->model('user/user_model', 'user');
        $this->load->model('book/book_model', 'book');
        $this->load->model('draft_author_model', 'draft_author');
        $this->load->model('revision_model', 'revision');

        // load model
        $this->load->model('draft_model', 'draft');
    }

    public function index($page = null)
    {
        // all filter
        $filters = [
            'category' => $this->input->get('category', true),
            'reprint'  => $this->input->get('reprint', true),
            'progress' => $this->input->get('progress', true),
            'keyword'  => $this->input->get('keyword', true),
            'status'  => $this->input->get('status', true),
        ];

        // custom per page
        $this->draft->per_page = $this->input->get('per_page', true) ?? 10;

        if ($this->level == 'reviewer') {
            $get_data = $this->draft->filter_draft_for_reviewer($filters, $this->username, $page);

            // user yang sedang login merupakan reviewer 1 atau 2
            foreach ($get_data['drafts'] as $d) {
                $draft_reviewers = $this->draft->get_id_and_name('reviewer', 'draft_reviewer', $d->draft_id);
                $reviewer_key    = key(array_filter($draft_reviewers, function ($dr) {
                    return $dr->reviewer_id == $this->role_id;
                }));
                if ($reviewer_key == 0) {
                    // reviewer 1
                    $d->review_flag     = $d->review1_flag;
                    $d->review_deadline = $d->review1_deadline;
                } elseif ($reviewer_key == 1) {
                    // reviewer 2
                    $d->review_flag     = $d->review2_flag;
                    $d->review_deadline = $d->review2_deadline;
                } else {
                    $d->review_flag     = null;
                    $d->review_deadline = null;
                }

                $d->sisa_waktu = Carbon::parse(Carbon::today())->diffInDays($d->review_deadline, false);
            }
        } elseif ($this->level == 'editor') {
            $get_data = $this->draft->filter_draft_for_staff($filters, $this->username, $page);

            foreach ($get_data['drafts'] as $d) {
                $d->sisa_waktu = Carbon::parse(Carbon::today())->diffInDays($d->edit_deadline, false);
            }
        } elseif ($this->level == 'layouter') {
            $get_data = $this->draft->filter_draft_for_staff($filters, $this->username, $page);
            foreach ($get_data['drafts'] as $d) {
                $d->sisa_waktu = Carbon::parse(Carbon::today())->diffInDays($d->layout_deadline, false);
            }
        } elseif ($this->level == 'author') {
            $get_data = $this->draft->filter_draft_for_author($filters, $this->username, $page);
        } else {
            $get_data = $this->draft->filter_draft_for_admin($filters, $page);
        }

        $drafts     = $get_data['drafts'];
        $total      = $get_data['total'];
        $pagination = $this->draft->make_pagination(site_url('draft'), 2, $total);
        $pages      = $this->pages;
        $main_view  = 'draft/index_draft';
        $this->load->view('template', compact('pages', 'main_view', 'drafts', 'pagination', 'total'));
    }

    // public function ajax_reload_author()
    // {
    //     $data = $this->draft->select(['author_id', 'author_name'])->get_all('author');
    //     if ($data) {
    //         foreach ($data as $value) {
    //             $datax[$value->author_id] = $value->author_name;
    //         }
    //         echo json_encode($datax);
    //     }
    // }

    public function add(int $category_id = null)
    {
        // khusus admin
        if (!is_admin()) {
            redirect();
        }

        if ($this->level == 'author') {
            // author tidak boleh daftar draft tanpa kategori
            if (!$category_id) {
                $this->session->set_flashdata('error', $this->lang->line('form_draft_error_category_not_found'));
                redirect();
            }
            // hanya untuk user level author yang terdaftar sebagai author
            if ($this->role_id == 0) {
                $this->session->set_flashdata('error', $this->lang->line('form_draft_error_author_not_registered'));
                redirect();
            }

            // cek category tersedia dan aktif
            // untuk pendaftaran draft oleh author
            if ($category_id) {
                $category        = $this->draft->get_where(['category_id' => $category_id], 'category');
                $sisa_waktu_buka = Carbon::parse(Carbon::today())->diffInDays($category->date_open, false);

                if (!$category || $category->category_status == 'n') {
                    $this->session->set_flashdata('error', $this->lang->line('form_draft_error_category_not_found'));
                    redirect();
                } elseif ($sisa_waktu_buka >= 1) {
                    $this->session->set_flashdata('error', $this->lang->line('form_draft_error_category_not_opened'));
                    redirect();
                }
            }
        }

        if (!$_POST) {
            $input              = (object) $this->draft->get_default_values();
            $input->category_id = $category_id;
        } else {
            $input = (object) $this->input->post(null, true);
            // catat orang yang menginput draft
            $input->input_by = $this->username;

            // repopulate draft_file ketika validasi form gagal
            if (!isset($input->draft_file)) {
                $input->draft_file = null;
            }
            if (!isset($input->author_id)) {
                $input->author_id = [];
            }

            $this->session->set_flashdata('draft_file_no_data', $this->lang->line('form_error_file_no_data'));
        }

        if ($this->draft->validate()) {
            if (!empty($_FILES) && $df = $_FILES['draft_file']['name']) {
                $draft_file_name = strip_disallowed_char($this->_generate_draft_file_name($df, $input->draft_title));
                $upload          = $this->draft->upload_draft_file('draft_file', $draft_file_name);
                if ($upload) {
                    $input->draft_file = $draft_file_name;
                }
            }
        }

        if (!$this->draft->validate() || $this->form_validation->error_array()) {
            $pages       = $this->pages;
            $main_view   = 'draft/form_draft_add';
            $form_action = 'draft/add';
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        // insert draft
        $draft_id = $this->draft->insert($input);

        // insert draft author
        foreach ($input->author_id as $key => $value) {
            // hanya author pertama yang boleh edit draft
            // author lain hanya bisa view only
            $this->draft_author->insert([
                'author_id'           => $value,
                'draft_id'            => $draft_id,
                'draft_author_status' => $key == 0 ? 1 : 0, // author pertama, flag 1, artinya boleh edit draft
            ]);
        }

        // insert ke worksheet
        $this->worksheet->insert([
            'draft_id'         => $draft_id,
            'worksheet_num'    => $this->_generate_worksheet_number(),
            'worksheet_status' => 0,
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        }

        redirect('draft/view/' . $draft_id);
    }

    public function view($draft_id = null)
    {
        if ($draft_id == null) {
            redirect($this->pages);
        }

        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$this->draft->is_authorized($this->level, $this->username, $draft_id)) {
            $this->session->set_flashdata('error', $this->lang->line('toast_error_not_authorized'));
            redirect($this->pages);
        }

        // ambil tabel worksheet
        $desk = $this->draft->get_where(['draft_id' => $draft_id], 'worksheet');
        // ambil tabel books
        // $books = $this->draft->get_where(['draft_id' => $draft_id], 'book');
        $book = $this->book->get_book_from_draft($draft_id);

        // pecah data nilai, csv jadi array
        // hitung bobot nilai
        if ($draft->review1_score) {
            $draft->review1_score       = explode(",", $draft->review1_score);
            $draft->review1_total_score = 35 * $draft->review1_score[0] + 25 * $draft->review1_score[1] + 10 * $draft->review1_score[2] + 30 * $draft->review1_score[3];
        } else {
            $draft->review1_total_score = '';
        }
        if ($draft->review2_score) {
            $draft->review2_score       = explode(",", $draft->review2_score);
            $draft->review2_total_score = 35 * $draft->review2_score[0] + 25 * $draft->review2_score[1] + 10 * $draft->review2_score[2] + 30 * $draft->review2_score[3];
        } else {
            $draft->review2_total_score = '';
        }

        // if ($draft->review1_score) {
        //     $draft->nilai_total_reviewer1 = 35 * $draft->review1_score[0] + 25 * $draft->review1_score[1] + 10 * $draft->review1_score[2] + 30 * $draft->review1_score[3];
        // } else {
        // }
        // if ($draft->review2_score) {
        //     $draft->nilai_total_reviewer2 = 35 * $draft->review2_score[0] + 25 * $draft->review2_score[1] + 10 * $draft->review2_score[2] + 30 * $draft->review2_score[3];
        // } else {
        //     $draft->nilai_total_reviewer2 = '';
        // }

        $input = (object) $draft;

        // ambil author
        $authors = $this->author->get_draft_authors($draft_id);
        // cek author yang login
        // author pertama adalah jika author_order = 0
        if ($this->level == 'author') {
            $author_order = array_search($this->role_id, array_column($authors, 'author_id')) == 0 ? true : false;
        } else {
            $author_order = null;
        }

        // ambil reviewer
        $reviewers = $this->reviewer->get_draft_reviewers($draft_id);
        // cek reviewer yang sedang login
        if ($this->level == 'reviewer') {
            $reviewer_order = array_search($this->role_id, array_column($reviewers, 'reviewer_id'));
        } else {
            $reviewer_order = null;
        }

        // ambil editor dan layouter
        $editors   = $this->user->get_draft_staffs($draft_id, 'editor');
        $layouters = $this->user->get_draft_staffs($draft_id, 'layouter');

        // hitung jumlah revisi
        $revision_total['editor']   = $this->revision->count_revision($draft_id, 'editor');
        $is_revision_in_progress['editor']   = $this->revision->is_revision_in_progress($draft_id, 'editor');
        $revision_total['layouter'] = $this->revision->count_revision($draft_id, 'layouter');
        $is_revision_in_progress['layouter']   = $this->revision->is_revision_in_progress($draft_id, 'layouter');

        $is_final = $input->draft_status == 14 ? true : false;

        $pages       = $this->pages;
        $main_view   = 'draft/view/overview';
        $form_action = "draft/edit/$draft_id";
        $this->load->view('template', compact('revision_total', 'is_revision_in_progress', 'book', 'author_order', 'draft', 'reviewer_order', 'desk', 'pages', 'main_view', 'form_action', 'input', 'authors', 'reviewers', 'editors', 'layouters', 'is_final'));
    }

    public function api_start_progress($draft_id)
    {
        // apakah draft tersedia
        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan draft ini
        if (!$this->draft->is_authorized($this->level, $this->username, $draft_id)) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        $this->db->trans_begin();
        if ($input->progress == 'review') {
            $this->draft->edit_draft_date($draft_id, 'review_start_date');
            $this->draft->update_draft_status($draft_id, ['draft_status' => 4]);
        } elseif ($input->progress == 'edit') {
            $this->draft->edit_draft_date($draft_id, 'edit_start_date');
            $this->draft->update_draft_status($draft_id, ['draft_status' => 6]);
        } elseif ($input->progress == 'layout') {
            $this->draft->edit_draft_date($draft_id, 'layout_start_date');
            $this->draft->update_draft_status($draft_id, ['draft_status' => 8]);
        } elseif ($input->progress == 'proofread') {
            $this->draft->edit_draft_date($draft_id, 'proofread_start_date');
            $this->draft->update_draft_status($draft_id, ['draft_status' => 12]);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        }
    }

    public function api_finish_progress($draft_id)
    {
        // apakah draft tersedia
        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan draft ini
        if (!$this->draft->is_authorized($this->level, $this->username, $draft_id)) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        $this->db->trans_begin();
        if ($input->progress == 'review') {
            $this->draft->edit_draft_date($draft_id, 'review_end_date');
        } elseif ($input->progress == 'edit') {
            $this->draft->edit_draft_date($draft_id, 'edit_end_date');
        } elseif ($input->progress == 'layout') {
            $this->draft->edit_draft_date($draft_id, 'layout_end_date');
        } elseif ($input->progress == 'proofread') {
            $this->draft->edit_draft_date($draft_id, 'proofread_end_date');
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        }
    }

    public function api_upload_progress($draft_id)
    {
        // apakah draft tersedia
        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk author pertama
        if ($this->level == 'author') {
            $draft_author_status = $this->_get_author_permission($this->role_id, $draft_id);
            if ($draft_author_status == 0) {
                $message = $this->lang->line('toast_error_not_authorized');
                return $this->send_json_output(false, $message);
            }
        }

        $input = (object) $this->input->post(null, true);
        $progress = $input->progress;

        // tiap upload, update upload date
        $this->draft->edit_draft_date($draft_id, $progress . '_upload_date');
        $upload_by_field         = $progress . '_upload_by';
        $input->$upload_by_field = $this->username;

        $column = "{$progress}_file";

        if (!empty($_FILES) && $file_name = $_FILES[$column]['name']) {
            $draft_file_name = strip_disallowed_char($this->_generate_draft_file_name($file_name, $draft->draft_title, $column));
            $upload = $this->draft->upload_file($column, $draft_file_name);
            if ($upload) {
                $input->$column = $draft_file_name;
                // Delete old draft file
                if ($draft->$column) {
                    $this->draft->delete_file($draft->$column);
                }
            }

            // validasi jenis file sesuai model
            if ($this->upload->display_errors()) {
                return $this->send_json_output(false, $this->upload->display_errors(), 422);
            }
        }

        // unset unnecesary data
        unset($input->progress);

        if ($this->draft->where('draft_id', $draft_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // hapus progress draft
    public function api_delete_progress($draft_id)
    {
        // apakah draft tersedia
        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, true);
        $progress = $input->progress;
        $file_type = $input->file_type;
        if ($file_type == 'file') {
            if (!$this->draft->delete_file($draft->{$input->progress . "_file"})) {
                return $this->send_json_output(false, $this->lang->line('toast_delete_fail'));
            }

            // ketika hapus file, update upload date, update upload by, delete progress file
            $draft->{$progress . '_file'} = '';
        } else  if ($file_type == 'link') {
            $draft->{$progress . '_file_link'} = '';
        }
        $draft->{$progress . '_upload_date'} = date('Y-m-d H:i:s');
        $upload_by_field         = $progress . '_upload_by';
        $draft->$upload_by_field = $this->username;

        if ($this->draft->where('draft_id', $draft_id)->update($draft)) {
            return $this->send_json_output(true, $this->lang->line('toast_delete_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_delete_fail'));
        }
    }

    // update draft, kirim update via post
    public function api_action_progress($draft_id)
    {
        // apakah draft tersedia
        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan draft ini
        if (!$this->draft->is_authorized($this->level, $this->username, $draft_id)) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $input = (object) $this->input->post(null, false);

        // cek status apakah akan direvert
        if ($input->revert) {
            $input->{"is_$input->progress"} = 'n';

            // kembali ke status 'sedang diproses'
            if ($input->progress == 'review') {
                $input->draft_status = 4;
            } elseif ($input->progress == 'edit') {
                $input->draft_status = 6;
            } elseif ($input->progress == 'layout') {
                $input->draft_status = 8;
            } elseif ($input->progress == 'proofread') {
                $input->draft_status = 12;
            }
        } else {
            $input->{"is_$input->progress"} = filter_boolean($input->accept) ? 'y' : 'n';

            // update draft status ketika selesai progress
            if ($input->progress == 'review') {
                $input->draft_status = filter_boolean($input->accept) ? 5 : 99;
            } elseif ($input->progress == 'edit') {
                $input->draft_status = filter_boolean($input->accept) ? 7 : 99;
            } elseif ($input->progress == 'layout') {
                $input->draft_status = filter_boolean($input->accept) ? 9 : 99;
            } elseif ($input->progress == 'proofread') {
                $input->draft_status = filter_boolean($input->accept) ? 13 : 99;
            }
        }

        // fill end date ketika aksi, jika kosong
        if (!$draft->{"{$input->progress}_end_date"}) {
            $input->{"{$input->progress}_end_date"} = now();
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);
        unset($input->accept);
        unset($input->revert);

        if ($this->draft->where('draft_id', $draft_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update draft, kirim update via post
    public function api_update_draft($draft_id = null)
    {
        // apakah draft tersedia
        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan draft ini
        if (!$this->draft->is_authorized($this->level, $this->username, $draft_id)) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // hanya untuk author pertama
        if ($this->level == 'author') {
            $draft_author_status = $this->_get_author_permission($this->role_id, $draft_id);
            if ($draft_author_status == 0) {
                $message = $this->lang->line('toast_error_not_authorized');
                return $this->send_json_output(false, $message);
            }
        }

        $input = (object) $this->input->post(null, false);
        // if (empty($input->files)) {
        //     unset($input->files);
        // }



        // if ($this->draft->validate($input) == false) {
        //     return $this->send_json_output('validation errrors', false, 422);
        // }

        // gabungkan array menjadi csv
        // if ($this->level == 'reviewer') {
        //     if ($rev == 1) {
        //         $input->review1_score = implode(",", $input->review1_score);
        //     } elseif ($rev == 2) {
        //         $input->review2_score = implode(",", $input->review2_score);
        //     } else {
        //         unset($input->review1_score);
        //         unset($input->review2_score);
        //     }
        // }

        if (!empty($this->input->post('edit_notes_date'))) {
            if ($this->level == 'editor') {
                $input->edit_notes_date  = date('Y-m-d H:i:s');
                $data['edit_notes_date'] = format_datetime($input->edit_notes_date);
            }
        }

        if (!empty($this->input->post('layout_notes_date'))) {
            if ($this->level == 'layouter') {
                $input->layout_notes_date  = date('Y-m-d H:i:s');
                $data['layout_notes_date'] = format_datetime($input->layout_notes_date);
            }
        }

        if ($this->draft->where('draft_id', $draft_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function edit($draft_id = null)
    {
        if (!is_admin()) {
            redirect($this->pages);
        }

        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object) $draft;
            $authors = $this->author->get_draft_authors($draft_id);
            $input->author_id = array_map(function ($a) {
                return $a->author_id;
            }, $authors);
        } else {
            $input = (object) $this->input->post(null, false);
        }

        if ($this->draft->validate()) {
            if (!empty($_FILES) && $df = $_FILES['draft_file']['name']) {
                $draft_file_name = strip_disallowed_char($this->_generate_draft_file_name($df, $input->draft_title));
                $upload          = $this->draft->upload_draft_file('draft_file', $draft_file_name);
                if ($upload) {
                    $input->draft_file = $draft_file_name;
                    if ($draft->draft_file) {
                        $this->draft->delete_draft_file($draft->draft_file);
                    }
                }
            }
        }

        // If something wrong
        if (!$this->draft->validate() || $this->form_validation->error_array()) {
            $pages       = $this->pages;
            $main_view   = 'draft/form_draft_edit';
            $form_action = "draft/edit/$draft_id";
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        //  hapus buku jika check delete_draft
        if (isset($input->delete_draft) && $input->delete_draft == 1) {
            $this->draft->delete_draft_file($draft->draft_file);
            $this->draft->delete_draft_file($input->draft_file);
            $input->draft_file = null;
            unset($input->delete_draft);
        }

        // update draft
        $this->draft->where('draft_id', $draft_id)->update($input);

        // update draft author
        $this->draft_author->where('draft_id', $draft_id)->delete();
        foreach ($input->author_id as $key => $value) {
            // hanya author pertama yang boleh edit draft
            // author lain hanya bisa view only
            $this->draft_author->insert([
                'author_id'           => $value,
                'draft_id'            => $draft_id,
                'draft_author_status' => $key == 0 ? 1 : 0, // author pertama, flag 1, artinya boleh edit draft
            ]);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect("$this->pages/view/$draft_id");
    }

    public function delete($draft_id = null)
    {
        if (!is_admin()) {
            redirect($this->pages);
        }

        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        $this->draft_author->where('draft_id', $draft_id)->delete();
        if ($this->draft->where('draft_id', $draft_id)->delete()) {
            $this->draft->delete_draft_file($draft->draft_file);
            $this->draft->delete_draft_file($draft->review1_file);
            $this->draft->delete_draft_file($draft->review2_file);
            $this->draft->delete_draft_file($draft->edit_file);
            $this->draft->delete_draft_file($draft->layout_file);
            $this->draft->delete_draft_file($draft->cover_file);
            $this->draft->delete_draft_file($draft->proofread_file);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }

        redirect($this->pages);
    }

    public function finish_draft($draft_id)
    {
        // memastikan konsistensi data
        $this->db->trans_begin();

        // update status draft
        $this->draft->where('draft_id', $draft_id)->update([
            'draft_status' => 14,
            'finish_date' => now()
        ]);

        $book = $this->book->get_book_from_draft($draft_id);
        $book_id = 0;

        // cek apakah buku sudah pernah dibuat dari draft ini
        if (!isset($book)) {
            $draft   = $this->draft->get_where(['draft_id' => $draft_id]);

            // jika file cetak tidak ada
            if (!$draft->proofread_file && !$draft->proofread_file_link) {
                $this->session->set_flashdata('warning', 'File cetak tidak ada');
                redirect("draft/view/$draft_id");
            }

            // copy file proofread, ke book
            $book_file_name = null;
            if ($draft->proofread_file) {
                $book_file_name = $this->_generate_draft_file_name($draft->proofread_file, $draft->draft_title, 'book');
                $this->copy_file('draftfile', 'bookfile', $draft->proofread_file, $book_file_name);
            }

            $this->book->insert([
                'draft_id' => $draft_id,
                'book_title' => $draft->draft_title,
                'book_file' =>  $book_file_name,
                'book_file_link' => $draft->proofread_file_link,
                'published_date' => empty_to_null()
            ]);
            $book_id = $this->db->insert_id();
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        } else {
            $this->db->trans_commit();
            if ($book_id != 0) {
                $this->session->set_flashdata('warning', 'Lengkapi data buku berikut');
                redirect("book/edit/$book_id");
            } else {
                $this->session->set_flashdata('error', 'Buku telah dibuat menggunakan draft ini');
                redirect("book/view/{$book->book_id}");
            }
        }
    }

    public function reprint($draft_id = null, $is_revise = false)
    {
        $draft = $this->draft->where('draft_id', $draft_id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if ($draft->draft_status != 14) {
            $this->session->set_flashdata('warning', 'Draft belum final, tidak bisa dicetak ulang');
            redirect('draft/view/' . $draft_id);
        }

        // set default data untuk draft cetak ulang
        $input             = new stdClass();
        $input->draft_title = $draft->draft_title . " (cetak ulang)";
        $input->is_reprint = 'y';
        $input->input_by = $this->username;
        $input->entry_date = now();
        $input->is_review = 'y';
        $input->is_edit = 'y';
        $input->is_layout = 'y';
        $input->is_proofread = 'y';

        // copy data dari draft yang direprint
        $copied_values = ['category_id', 'theme_id', 'draft_file', 'draft_file_link'];
        foreach ($copied_values as $value) {
            $input->{$value} = $draft->{$value};
        }

        // get array penulis
        $authors = array_map(function ($author) {
            return $author->author_id;
        }, $this->author->get_draft_authors($draft_id));

        // memastikan konsistensi data
        $this->db->trans_begin();

        // insert draft
        $insert_id   = $this->draft->insert($input);

        // insert draft author
        foreach ($authors as $key => $value) {
            // hanya author pertama yang boleh edit draft
            // author lain hanya bisa view only
            $this->draft_author->insert([
                'author_id'           => $value,
                'draft_id'            => $draft_id,
                'draft_author_status' => $key == 0 ? 1 : 0, // author pertama, flag 1, artinya boleh edit draft
            ]);
        }

        // insert ke worksheet
        $this->worksheet->insert([
            'draft_id'         => $insert_id,
            'worksheet_num'    => $this->_generate_worksheet_number(),
            'worksheet_status' => 1,
            'is_reprint'       => 'y'
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        }

        redirect('draft/view/' . $insert_id);
    }

    public function print_data($draft_id)
    {
        $print = $this->draft->select(['is_print', 'print_start_date', 'print_end_date', 'print_file', 'print_file_link', 'print_notes', 'print_status'])
            ->where('draft_id', $draft_id)
            ->get();

        $this->send_json_output(true, $print);
    }

    private function _generate_worksheet_number()
    {
        // format nomor worksheet
        // 2020-03-44 >> tahun-bulan-urutan
        $date  = date('Y-m');
        $query = $this->worksheet->like('worksheet_num', $date)->order_by('draft_id', 'desc')->get();

        if ($query) {
            $worksheet_num = explode("-", $query->worksheet_num);
            $num           = ((int) $worksheet_num[2]) + 1; // ambil digit belakang sendiri, lalu di increment
            $num           = str_pad($num, 2, '0', STR_PAD_LEFT);
        } else {
            $num = '01';
        }
        return $date . '-' . $num;
    }

    private function _generate_draft_file_name($draft_file_name, $draft_title, $progress = null)
    {
        $get_extension = explode(".", $draft_file_name)[1];
        if ($progress) {
            return str_replace(" ", "_", $draft_title . '_' . $progress . '_' . date('YmdHis') . '.' . $get_extension); // progress file name
        } else {
            return str_replace(" ", "_", $draft_title . '_' . date('YmdHis') . '.' . $get_extension); // draft file name
        }
    }

    private function _get_author_permission($author_id, $draft_id)
    {
        // author pertama return 1, true,, otherwise 0
        $result = $this->draft->get_where(['author_id' => $author_id, 'draft_id' => $draft_id], 'draft_author');
        return $result->draft_author_status ?? 0;
    }

    public function unique_data($str, $data_key)
    {
        $draft_id = $this->input->post('draft_id');
        if (!$str) {
            return true;
        }
        $this->draft->where($data_key, $str);
        !$draft_id || $this->draft->where_not('draft_id', $draft_id);
        $draft = $this->draft->get();
        if ($draft) {
            $this->form_validation->set_message('unique_data', $this->lang->line('toast_data_duplicate'));
            return false;
        }
        return true;
    }

    public function valid_url($str)
    {
        if (!$str) {
            return true;
        }
        return (bool) filter_var($str, FILTER_VALIDATE_URL);
    }
}
