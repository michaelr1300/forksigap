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
            $total_penjualan += $royalty_each->penjualan;
            $total_royalty += $royalty_each->earned_royalty;
        }

        $pagination = $this->royalty->make_pagination(site_url('royalty'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'royalty/index_royalty';
        $this->load->view('template', compact('pages', 'main_view', 'royalty', 'pagination', 'total', 'total_penjualan', 'total_royalty'));
    }

    public function view($author_id, $period_end = null)
    {
        $author = $this->db->select('author_id, author_name, last_paid_date')->from('author')->where('author_id', $author_id)->get()->row();
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'period_end'        => $period_end,
            'last_paid_date'    => $author->last_paid_date
        ];
        $royalty_details = $this->royalty->author_details($author_id, $filters);
        $pages          = $this->pages;
        $main_view      = 'royalty/view_royalty';
        // var_dump($author);

        $this->load->view('template', compact('pages', 'main_view', 'author', 'royalty_details', 'period_end'));
    }

    public function generate_pdf($author_id, $period_end = null)
    {
        $author = $this->db->select('author_id, author_name, last_paid_date')->from('author')->where('author_id', $author_id)->get()->row();
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'period_end'        => $period_end,
            'last_paid_date'    => $author->last_paid_date
        ];
        $royalty_details = $this->royalty->author_details($author_id, $filters);

        // PDF
        $this->load->library('pdf');

        $data = array(
            'author' => $author,
            'royalty_details' => $royalty_details,
            'period_end' => $period_end
        );

        // $html = $this->load->view('royalty/view_royalty_pdf', compact('author', 'royalty_details', 'period_time', 'date_year'));
        $html = $this->load->view('royalty/view_royalty_pdf', $data, true);

        $file_name = 'Royalti_' . $data['author']->author_name;

        $this->pdf->generate_pdf_a4_landscape($html, $file_name);
    }
}
