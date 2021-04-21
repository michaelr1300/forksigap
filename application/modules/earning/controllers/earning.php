<?php defined('BASEPATH') or exit('No direct script access allowed');

class earning extends MY_Controller
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
            'date_year'     => $this->input->get('date_year', true),
            'excel'         => $this->input->get('excel', true)
        ];
        // $model = [];
        // for ($month = 1; $month <= 12; $month++) {
        //     $filters['date_month'] = $month;
        //     $monthly = $this->production_report->filter_total($filters);

        //     $count_total = 0;
        //     foreach ($monthly as $value) {
        //         if (isset($value->total)) {
        //             $count_total += $value->total;
        //         }
        //     }

        //     $count_total_new = 0;
        //     foreach ($monthly as $value) {
        //         if (isset($value->total_new)) {
        //             $count_total_new += $value->total_new;
        //         }
        //     }

        //     $count_order = count($monthly);

        //     array_push($model, [
        //         'month'             => $month,
        //         'data'              => $monthly,
        //         'count_total'       => $count_total,
        //         'count_total_new'   => $count_total_new,
        //         'count_order'       => $count_order
        //     ]);
        // }

        $pages      = $this->pages;
        $main_view  = 'earning/index_earning';
        $this->load->view('template', compact('main_view', 'pages'));

        // if ($filters['excel'] == 1) {
        //     $this->generate_excel($filters, 'total');
        // }
    }
}
