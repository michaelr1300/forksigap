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
        $date_year = $this->input->get('date_year', true);
        $period_time = $this->input->get('period_time', true);
        $period_start = null;
        $period_end = null;
        if ($period_time != null) {
            if ($period_time == 1) {
                $period_start = $date_year . '/01/01';
                $period_end = $date_year . '/06/30 23:59:59.999';
            } else if ($period_time == 2) {
                $period_start = $date_year . '/06/01';
                $period_end = $date_year . '/12/31 23:59:59.999';
            }
        }

        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'period_start'      => $period_start,
            'period_end'        => $period_end
        ];
        $this->royalty->per_page = $this->input->get('per_page', true) ?? 10;

        $royalty = $this->royalty->author_earning($filters);
        var_dump($royalty);
        $total = count($royalty);
        $total_penjualan = 0;
        $total_royalty = 0;
        foreach ($royalty as $royalty_each) {
            $total_penjualan += $royalty_each->penjualan;
            $total_royalty += $royalty_each->earned_royalty;
        }

        $pagination = $this->royalty->make_pagination(site_url('royalty'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'royalty/index_royalty';
        $this->load->view('template', compact('pages', 'main_view', 'royalty', 'pagination', 'total', 'total_penjualan', 'total_royalty'));
    }

    public function view($author_id, $period_time = null, $date_year = null)
    {
        $period_start = null;
        $period_end = null;
        if ($period_time != null) {
            if ($period_time == 1) {
                $period_start = $date_year . '/01/01';
                $period_end = $date_year . '/06/30 23:59:59.999';
            } else if ($period_time == 2) {
                $period_start = $date_year . '/06/01';
                $period_end = $date_year . '/12/31 23:59:59.999';
            }
        }

        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'period_start'      => $period_start,
            'period_end'        => $period_end
        ];
        $author = $this->db->select('author_name')->from('author')->where('author_id', $author_id)->get()->row();
        $royalty_details = $this->royalty->author_details($author_id, $filters);
        $pages          = $this->pages;
        $main_view      = 'royalty/view_royalty';
        // var_dump($author);

        $this->load->view('template', compact('pages', 'main_view', 'author', 'royalty_details', 'period_time', 'date_year'));
    }
}
