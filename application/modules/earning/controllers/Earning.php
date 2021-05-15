<?php defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Earning extends Sales_Controller
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
            'invoice_type'  => $this->input->get('invoice_type', true),
            'excel'         => $this->input->get('excel', true)
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
        $year = $filters['date_year'];
        $pages      = $this->pages;
        $main_view  = 'earning/index_earning';
        $this->load->view('template', compact('main_view', 'pages', 'filter_invoice_type', 'total_earning', 'year'));

        //generate excel
        if ($filters['excel'] == 1) {
            $this->generate_excel($filters, 'index');
        }
    }
    public function detail()
    {
        $filters = [
            'date_year'     => $this->input->get('date_year', true),
            'date_month'    => $this->input->get('date_month', true),
            'invoice_type'  => $this->input->get('invoice_type', true),
            'excel'         => $this->input->get('excel', true)
        ];
        if ($filters['date_year'] == NULL && $filters['invoice_type'] == NULL) {
            $filters['date_year'] = '2021';
        }

        $invoice_type = ['cash', 'showroom', 'credit', 'online'];
        for ($i = 0; $i < 4; $i++) {
            $filters['invoice_type'] = $invoice_type[$i];
            $details[$filters['invoice_type']] = $this->earning->filter_detail($filters)->earning;
        }
        $pages      = $this->pages;
        $main_view  = 'earning/detail_earning';
        $this->load->view('template', compact('main_view', 'pages', 'details'));

        //generate excel
        if ($filters['excel'] == 1) {
            $this->generate_excel($filters, 'detail');
        }
    }

    public function api_get_invoice($year, $month, $invoice_type)
    {
        $filters = [
            'date_year'     => $year,
            'date_month'    => $month + 1,
            'invoice_type'  => $invoice_type
        ];
        $result = $this->earning->get_invoice($filters);
        return $this->send_json_output(true, $result);
    }

    public function generate_excel($filters, $menu)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if ($menu == 'index') {
            $filename = 'Order_Cetak_Tahun_' . $filters['date_year'];
            $get_data = $this->earning->filter_excel_total($filters);
        } else {
            $filename = 'Order_Cetak_' . date('F', mktime(0, 0, 0, $filters['date_month'], 10)) . '_' . $filters['date_year'];
            $get_data = $this->earning->filter_excel_detail($filters);
        }
        $i = 2;
        $no = 1;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'Q') as $v) {
                switch ($v) {
                    case 'A': {
                            $value = $no++;
                            break;
                        }
                    case 'B': {
                            $value = $data->title;
                            break;
                        }
                    case 'C': {
                            $value = get_print_order_category()[$data->category];
                            break;
                        }
                    case 'D': {
                            $value = strtoupper($data->type);
                            break;
                        }
                    case 'E': {
                            $value = date('d F Y H:i:s', strtotime($data->entry_date));
                            break;
                        }
                    case 'F': {
                            $value = date('d F Y H:i:s', strtotime($data->finish_date));
                            break;
                        }
                    case 'G': {
                            $value = $data->total;
                            break;
                        }
                    case 'H': {
                            $value = $data->total_new;
                            break;
                        }
                    case 'I': {
                            $staff = "";
                            $staff_percetakan   = $this->production_report->get_staff_percetakan_by_progress("preprint", $data->id);
                            foreach ($staff_percetakan as $val) {
                                $staff .= $val->username . ", ";
                            }
                            $value = $staff;
                            break;
                        }
                    case 'J': {
                            $value = date('d F Y H:i:s', strtotime($data->preprint_start_date));
                            break;
                        }
                    case 'K': {
                            $value = date('d F Y H:i:s', strtotime($data->preprint_end_date));
                            break;
                        }
                    case 'L': {
                            $staff = "";
                            $staff_percetakan   = $this->production_report->get_staff_percetakan_by_progress("print", $data->id);
                            foreach ($staff_percetakan as $val) {
                                $staff .= $val->username . ", ";
                            }
                            $value = $staff;
                            break;
                        }
                    case 'M': {
                            $value = date('d F Y H:i:s', strtotime($data->print_start_date));
                            break;
                        }
                    case 'N': {
                            $value = date('d F Y H:i:s', strtotime($data->print_end_date));
                            break;
                        }
                    case 'O': {
                            $staff = "";
                            $staff_percetakan   = $this->production_report->get_staff_percetakan_by_progress("postprint", $data->id);
                            foreach ($staff_percetakan as $val) {
                                $staff .= $val->username . ", ";
                            }
                            $value = $staff;
                            break;
                        }
                    case 'P': {
                            $value = date('d F Y H:i:s', strtotime($data->postprint_start_date));
                            break;
                        }
                    case 'Q': {
                            $value = date('d F Y H:i:s', strtotime($data->postprint_end_date));
                            break;
                        }
                }
                $sheet->setCellValue($v . $i, $value);
            }
            $i++;
        }
        // Column Title
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Judul');
        $sheet->setCellValue('C1', 'Kategori');
        $sheet->setCellValue('D1', 'Tipe Cetak');
        $sheet->setCellValue('E1', 'Tanggal Mulai');
        $sheet->setCellValue('F1', 'Tanggal Selesai');
        $sheet->setCellValue('G1', 'Jumlah Pesanan');
        $sheet->setCellValue('H1', 'Jumlah Hasil Cetak');
        $sheet->setCellValue('I1', 'PIC Pracetak');
        $sheet->setCellValue('J1', 'Tanggal Mulai Pracetak');
        $sheet->setCellValue('K1', 'Tanggal Selesai Pracetak');
        $sheet->setCellValue('L1', 'PIC Cetak');
        $sheet->setCellValue('M1', 'Tanggal Mulai Cetak');
        $sheet->setCellValue('N1', 'Tanggal Selesai Cetak');
        $sheet->setCellValue('O1', 'PIC Jilid');
        $sheet->setCellValue('P1', 'Tanggal Mulai Jilid');
        $sheet->setCellValue('Q1', 'Tanggal Selesai Jilid');
        // Auto width
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        die();
    }
}
