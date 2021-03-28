<?php defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Book_transaction extends MY_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_transaction";
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
    }

    public function index($page = NULL){
        // if($this->check_level_gudang_pemasaran() == TRUE):
        // all filter

        // custom per page
        // $this->book_transaction->per_page = $this->input->get('per_page', true) ?? 10;

        // $get_data = $this->book_transfer->filter_book_transfer($filters, $page);

        // $get_data = $this->book_transaction->filter_excel();
        // $book_transfer   = $get_data['book_transfer'];
        // $total          = $get_data['total'];
        // $pagination     = $this->book_transaction->make_pagination(site_url('book_transfer'), 2, $total);
        $pages          = $this->pages;
        $main_view      = 'book_transaction/index_book_transaction';
        $this->load->view('template', compact('pages', 'main_view', 
        // 'book_transaction',
        //  'pagination', 'total'
    ));
        // endif;
    }

    // public function generate_excel($filters)
    public function generate_excel()
    {
        // $get_data = $this->book_stock->filter_excel($filters);
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'Transaksi Buku';

        // Column Title
        $sheet->setCellValue('A1', 'Transaksi Buku');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A1')
                    ->getFont()
                    ->setBold(true);
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Judul Buku');
        $sheet->setCellValue('C3', 'Stok Awal');
        $sheet->setCellValue('D3', 'Perubahan');
        $sheet->setCellValue('E3', 'Jenis Transaksi');
        $sheet->setCellValue('F3', 'Tanggal Transaksi');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:F3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:F3')
                    ->getFont()
                    ->setBold(true);

        // Auto width
        // $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        // $get_data = $this->book_stock->filter_excel($filters);
        $get_data = $this->book_transaction->filter_excel();
        $no = 1;
        $i = 4;
        // Column Content
        // Menampilkan data (Nomor, judul buku, 
        // stok awal, perubahan (total), jenis transaksi ($book_receive_id==null: keluar. Sebaliknya gt), 
        // tanggal transaksi (finish_date faktur/book_receive))

        foreach ($get_data as $data) {
            foreach (range('A', 'F') as $v) {
                switch ($v) {
                    case 'A': {
                            $value = $no++;
                            break;
                        }
                    case 'B': {
                            $value = $data->book_title;
                            break;
                        }
                    case 'C': {
                            $value = $data->stock_initial;
                            break;
                        }
                    case 'D': {
                            if($data->book_faktur_id == null && !$data->book_receive_id==null){
                                $value = $data->stock_in;
                            }
                            else{
                                $value = $data->stock_out;
                            }
                            break;
                    }
                    case 'E': {
                            if($data->book_faktur_id == null  && !$data->book_receive_id==null){
                                $value = 'Buku Masuk';
                            }
                            else{
                                $value = 'Buku Keluar';
                            }
                            break;
                    }
                    case 'F': {
                            $value = $data->date;
                        break;
                    }
                }
                $sheet->setCellValue($v . $i, $value);
            }
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        die();
    }


}
