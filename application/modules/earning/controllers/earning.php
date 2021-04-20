<?php defined('BASEPATH') or exit('No direct script access allowed');

class Earning extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'earning';
        $this->load->model('earning_model', 'earning');
        $this->load->helper('sales_helper');
    }

    public function index()
    {
        $filters = [
            'date_year'     => $this->input->get('date_year', true),
            'invoice_type'  => $this->input->get('invoice_type', true)
            // 'excel'         => $this->input->get('excel', true)
        ];
        if ($filters['date_year'] == NULL && $filters['invoice_type'] == NULL) {
            $filters['date_year'] = '2021';
        }

        if ($filters['invoice_type'] == NULL) {
            $filter_invoice_type = false;
            $invoice_type = ['cash', 'showroom', 'credit', 'online'];
            for ($i = 0; $i < 4; $i++) {
                $filters['invoice_type'] = $invoice_type[$i];
                for ($month = 1; $month <= 12; $month++) {
                    $filters['date_month'] = $month;
                    $monthly[$filters['invoice_type']][$month] = $this->earning->filter_total($filters);

                    $total_earning[$filters['invoice_type']][$month] = 0;
                    foreach ($monthly[$filters['invoice_type']][$month] as $value) {
                        $total_earning[$filters['invoice_type']][$month] += $value->earning;
                    }
                }
            }
        } else {
            $filter_invoice_type = $filters['invoice_type'];
            for ($month = 1; $month <= 12; $month++) {
                $filters['date_month'] = $month;
                $monthly[$filter_invoice_type][$month] = $this->earning->filter_total($filters);

                $total_earning[$filter_invoice_type][$month] = 0;
                foreach ($monthly[$filter_invoice_type][$month] as $value) {
                    $total_earning[$filter_invoice_type][$month] += $value->earning;
                }
            }
        }

        $pages      = $this->pages;
        $main_view  = 'earning/index_earning';
        $this->load->view('template', compact('main_view', 'pages', 'filter_invoice_type', 'total_earning'));
    }
    public function detail()
    {
        $filters = [
            'date_year'     => $this->input->get('date_year', true),
            // 'date_month'    => $this->input->get('date_month', true),
            'invoice_type'  => $this->input->get('invoice_type', true)
            // 'excel'         => $this->input->get('excel', true)
        ];
        if ($filters['date_year'] == NULL && $filters['invoice_type'] == NULL) {
            $filters['date_year'] = '2021';
        }

        $pages      = $this->pages;
        $main_view  = 'earning/detail_earning';
        $this->load->view('template', compact('main_view', 'pages'));
    }
}
