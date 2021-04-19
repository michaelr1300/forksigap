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
            // 'date_month'    => $this->input->get('date_month', true),
            'invoice_type'  => $this->input->get('invoice_type', true)
            // 'excel'         => $this->input->get('excel', true)
        ];
        if ($filters['date_year'] == NULL && $filters['invoice_type'] == NULL) {
            $filters['date_year'] = '2021';
        }
        $model = [];
        for ($month = 1; $month <= 12; $month++) {
            $filters['date_month'] = $month;
            $monthly = $this->earning->filter_total($filters);
            $count_invoice = $this->earning->filter_count($filters);

            $total_earning = 0;
            foreach ($monthly as $value) {
                if (isset($value->earning)) {
                    $total_earning += $value->earning;
                }
            }

            $count_invoice_book = count($monthly);

            array_push($model, [
                'month'                 => $month,
                'data'                  => $monthly,
                'total_earning'         => $total_earning,
                'count_invoice'         => $count_invoice[0]->count_invoice,
                'count_invoice_book'    => $count_invoice_book
            ]);
        }

        $pages      = $this->pages;
        $main_view  = 'earning/index_earning';
        $this->load->view('template', compact('main_view', 'pages', 'model'));
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
        echo ("Page Detail");
    }
}
