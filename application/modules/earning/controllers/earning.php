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
        redirect('earning/summary?date_year=' . date('Y'));
    }

    public function summary()
    {
        $filters = [
            'date_year'     => $this->input->get('date_year', true)
            // 'excel'         => $this->input->get('excel', true)
        ];
        $model = [];
        for ($month = 0; $month <= 12; $month++) {
            $filters['date_month'] = $month;
            $monthly = $this->earning->filter_total($filters);

            $total_earning = 0;
            foreach ($monthly as $value) {
                if (isset($value->earning)) {
                    $total_earning += $value->earning;
                }
            }

            $count_invoice = count($monthly);

            array_push($model, [
                'month'             => $month,
                'data'              => $monthly,
                'total_earning'     => $total_earning,
                'count_order'       => $count_invoice
            ]);
        }

        //print data april
        var_dump($model[4]['total_earning']);
        var_dump($model[4]['count_order']);


        $pages      = $this->pages;
        $main_view  = 'earning/index_earning';
        // $this->load->view('template', compact('main_view', 'pages'));

        // if ($filters['excel'] == 1) {
        //     $this->generate_excel($filters, 'total');
        // }
    }
}
