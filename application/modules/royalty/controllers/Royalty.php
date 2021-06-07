<?php defined('BASEPATH') or exit('No direct script access allowed');

class Royalty extends Sales_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'royalty';
        $this->load->model('royalty_model', 'royalty');
        // $this->load->model('invoice/invoice_model', 'invoice');
        $this->load->model('book/book_model', 'book');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'period_end'        => $this->input->get('end_date', true)
        ];
        $this->royalty->per_page = $this->input->get('per_page', true) ?? 10;

        $royalty = $this->royalty->author_earning($filters);
        $total = count($royalty);
        $total_penjualan = 0;
        $total_royalty = 0;
        foreach ($royalty as $royalty_each) {
            $total_penjualan += $royalty_each->total_sales;
            $total_royalty += $royalty_each->earned_royalty;
        }

        $pagination = $this->royalty->make_pagination(site_url('royalty'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'royalty/index_royalty';
        $this->load->view('template', compact('pages', 'main_view', 'royalty', 'pagination', 'total', 'total_penjualan', 'total_royalty'));
    }

    public function view($author_id, $period_end = null)
    {
        $author = $this->db->select('author_id, author_name')->from('author')->where('author_id', $author_id)->get()->row();
        $royalty_payment = $this->db->select('last_paid_date, last_request_date, status')->from('royalty_payment')->where('author_id', $author->author_id)->get()->row();
        if ($royalty_payment == NULL) $last_paid_date = '2021/01/01';
        else $last_paid_date = $royalty_payment->last_paid_date;
        $filters = [
            'period_end'        => $period_end,
            'last_paid_date'    => $last_paid_date
        ];
        $royalty_details = $this->royalty->author_details($author_id, $filters);

        $royalty_history = $this->royalty->fetch_royalty_history($author_id);
        foreach ($royalty_history as $history) {
            $history_filter = [
                'last_paid_date'    => $history->start_date,
                'period_end'        => $history->end_date
            ];
            $history->details = $this->royalty->author_details($author_id, $history_filter)[0];
        }
        
        $pages          = $this->pages;
        $main_view      = 'royalty/view_royalty';
        $this->load->view('template', compact('pages', 'main_view', 'author', 'royalty_payment', 'royalty_details', 'royalty_history', 'period_end'));
    }

    public function view_detail($royalty_id)
    {
        $royalty = $this->royalty->where('royalty_id', $royalty_id)->get();
        $filters = [
            'last_paid_date'        => $royalty->start_date,
            'period_end'            => $royalty->end_date,
        ];
        $author = $this->db->select('author_id, author_name')->from('author')->where('author_id', $royalty->author_id)->get()->row();
        $royalty_details = $this->royalty->author_details($royalty->author_id, $filters);
        $pages          = $this->pages;
        $main_view      = 'royalty/view_royalty_detail';
        $this->load->view('template', compact('pages', 'main_view', 'author', 'royalty', 'royalty_details'));
    }

    public function generate_pdf($author_id, $period_end = null)
    {
        $author = $this->db->select('author_id, author_name')->from('author')->where('author_id', $author_id)->get()->row();
        $filters = [
            'last_paid_date'    => $this->input->get('start_date'),
            'period_end'        => $period_end,
        ];
        $royalty_details = $this->royalty->author_details($author_id, $filters);
        $current_stock = $this->royalty->stocks_info($author_id, $filters);
        // var_dump($current_stock[0]->WP);
        // PDF
        $this->load->library('pdf');

        $data = array(
            'author' => $author,
            'royalty_details' => $royalty_details,
            'period_end' => $period_end,
            'current_stock' => $current_stock
        );

        // $html = $this->load->view('royalty/view_royalty_pdf', compact('author', 'royalty_details', 'period_time', 'date_year'));
        $html = $this->load->view('royalty/view_royalty_pdf', $data, true);

        $file_name = 'Royalti_' . $data['author']->author_name;

        ob_end_clean();
        $this->pdf->generate_pdf_a4_landscape($html, $file_name);
    }

    public function pay()
    {
        $author_id = $this->input->post('author_id');
        $royalty_payment = $this->db->select('last_paid_date, last_request_date, status')->from('royalty_payment')->where('author_id', $author_id)->get()->row();
        //jika belum ada data royalti
        if ($royalty_payment == NULL) {
            $last_paid_date = '2021/01/01';

            //tambahkan data royalti author
            $add = [
                'author_id ' => $author_id,
                'last_paid_date' => $last_paid_date,
                'last_request_date' => $this->input->post('paid_date'),
                'status' => 'requested'
            ];
            $this->db->insert('royalty_payment', $add);

            $data = [
                'author_id' => $author_id,
                'start_date' => $last_paid_date,
                'end_date' => $this->input->post('paid_date'),
                'status' => 'requested'
            ];
            $this->db->insert('royalty', $data);

        } else {
            

            //jika sudah ada dan sedang diajukan
            if ($royalty_payment->status == 'requested') {
                $last_paid_date = $royalty_payment->last_request_date;
                $edit = [
                    'last_paid_date' => $last_paid_date,
                    'last_request_date' => NULL,
                    'status' => NULL
                ];
                $this->db->set($edit)->where('author_id', $author_id)->update('royalty_payment');

                $data = [
                    'paid_date' => now(),
                    'status' => 'paid'
                ];
                $this->db->set($data)->where('author_id', $author_id)->where('status', 'requested')->update('royalty'); 
            }
            //jika sudah ada dan belum diajukan
            else if ($royalty_payment->status == NULL) {
                $last_paid_date = $royalty_payment->last_paid_date;
                $edit = [
                    'last_request_date' => $this->input->post('paid_date'),
                    'status' => 'requested'
                ];
                $this->db->set($edit)->where('author_id', $author_id)->update('royalty_payment');

                $data = [
                    'author_id' => $author_id,
                    'start_date' => $last_paid_date,
                    'end_date' => $this->input->post('paid_date'),
                    'status' => 'requested'
                ];
                $this->db->insert('royalty', $data);    
            }
        }

        

        echo json_encode(['status' => true]);
    }


    public function debug($author_id) {

        $royalty_history = $this->royalty->fetch_royalty_history($author_id);
        foreach ($royalty_history as $history) {
            $history_filter = [
                'last_paid_date'    => $history->start_date,
                'period_end'        => $history->end_date
            ];
            $history->details = $this->royalty->author_details($author_id, $history_filter)[0];    
            var_dump($history->details->total_sales);
        }
        
    }
}
