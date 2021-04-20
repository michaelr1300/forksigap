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
        $model = [];
        for ($month = 1; $month <= 12; $month++) {
            $filters['date_month'] = $month;
            // if ($filters['invoice_type'] == NULL) {
            //     $invoice_type = ['cash', 'showroom', 'credit', 'online'];
            //     for ($i = 0; $i < 4; $i++) {
            //         $filters['invoice_type'] = $invoice_type[$i];
            //         $monthly[$i] = $this->earning->filter_total($filters);
            //     }
            // } else {
            //     $monthly = $this->earning->filter_total($filters);
            // }
            $monthly = $this->earning->filter_total($filters);

            $total_earning = 0;
            foreach ($monthly as $value) {
                if (isset($value->earning)) {
                    $total_earning += $value->earning;
                }
            }

            array_push($model, [
                'month'                 => $month,
                'data'                  => $monthly,
                'total_earning'         => $total_earning
            ]);
        }
        var_dump($model[3]['data']);

        $pages      = $this->pages;
        $main_view  = 'earning/index_earning';
        // $this->load->view('template', compact('main_view', 'pages', 'model'));
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
