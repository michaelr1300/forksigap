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
        $filters = [
            'keyword'           => $this->input->get('keyword', true)
            // 'invoice_type'      => $this->input->get('invoice_type', true),
            // 'status'            => $this->input->get('status', true),
            // 'customer_type'     => $this->input->get('customer_type', true)
        ];

        $this->invoice->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->royalty->filter_royalty($filters, $page);

        //data invoice
        $royalty    = $get_data['royalty'];
        $total      = $get_data['total'];
        $pagination = $this->invoice->make_pagination(site_url('royalty'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'royalty/index_royalty';
        $this->load->view('template', compact('pages', 'main_view', 'royalty', 'pagination', 'total'));
    }

    public function view($author_id)
    {
        $pages          = $this->pages;
        $main_view      = 'invoice/view_royalty';
        $royalty        = $this->invoice->fetch_royalty_id($author_id);

        $this->load->view('template', compact('pages', 'main_view', 'royalty'));
    }
}