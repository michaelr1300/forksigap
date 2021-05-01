<?php defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Book_stock extends Warehouse_sales_controller
{
    public $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_stock";
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book/book_model', 'book');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
    }

    public function index($page = NULL){
        //all filter
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'published_year'    => $this->input->get('published_year', true),
            'warehouse_present' => $this->input->get('warehouse_present', true),
            'excel'             => $this->input->get('excel', true)
        ];
        //custom per page
        $this->book_stock->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->book_stock->filter_book_stock($filters, $page);

        $book_stocks= $get_data['book_stocks'];
        $total = $get_data['total'];
        $pagination = $this->book_stock->make_pagination(site_url('book_stock'), 2, $total);
        $pages      = $this->pages;
        $main_view  = 'book_stock/index_bookstock';
        $this->load->view('template', compact('pages', 'main_view', 'book_stocks', 'pagination', 'total'));

        if ($filters['excel'] == 1) {
            $this->generate_excel($filters);
        }
    }


    public function view($book_stock_id){
        // $book_stock = $this->book_stock->join('book')->where('book.book_id', $book_id)->get();
        $book_stock = $this->book_stock->get_book_stock($book_stock_id);
        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $input = (object) $book_stock;
        // $get_stock      = $this->book_stock->fetch_stock_by_id($book_stock_id);
        // $stock_history  = $get_stock['stock_history'];
        // $stock_last     = $get_stock['stock_last'];

        // $book_stocks                = $this->book_stock->get_stock_by_id($book_stock_id);
        $book_stock->revision      = $this->book_stock->get_stock_revision($book_stock->book_id);
        $book_stock->library_stock = $this->book_stock->get_library_stock($book_stock->book_stock_id);
        // $library_id = $book_stock->library_stock->library_id;
        // $book_stock->library_stock->library       = $this->book_stock->get_library($library_id);
        // var_dump($book_stock->library_stock);
        
        $pages                      = $this->pages;
        $main_view                  = 'book_stock/view_bookstock';
        $this->load->view('template', compact('pages', 'main_view', 'input', 'book_stock'));
        return;
    }

    public function edit($book_stock_id){
        if(!$this->_is_warehouse_admin()){
            redirect($this->pages);
        }

        $book_stock = $this->book_stock->get_book_stock($book_stock_id);
        // $input = (object) $book_stock;
        if(!$book_stock){
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if(!$_POST){
            $input = (object) $book_stock;
        }
        else{
            $input = (object) $this->input->post(null, true);
            // catat orang yang menginput stok buku
            $input->user_id = $_SESSION['user_id'];
            
        }

        // if(!$this->book_stock->validate() || $this->form_validation->error_array()){
            $pages = $this->pages;
            $main_view = 'book_stock/edit_bookstock';
            $form_action = "book_stock/edit/$book_stock_id";
            $this->load->view('template', compact('pages','main_view', 'input'));   
        //     return; 
        // }
    }

    public function edit_book_location(){
        if($this->_is_warehouse_admin() == TRUE && $this->input->method()=='post'){
            $book_title = $this->input->post('book_title');
            $book_stock_id = $this->input->post('book_stock_id');
            $new_location = $this->input->post('book_location');
            $book_stock = $this->book_stock->where('book_stock_id', $book_stock_id)->get();
            if (!$book_stock) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            }
            else {
                $book_stock->book_location = $new_location;
                if ($this->book_stock->where('book_stock_id', $book_stock_id)->update($book_stock)) {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                } else {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
                }
            }
        }
        else {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
        }
        redirect($this->pages);
    }
   
    public function delete($book_stock_id = null)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        $book_stock = $this->book_stock->where('book_stock_id', $book_stock_id)->get();
        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        $this->book_stock->where('book_stock_id', $book_stock_id)->delete();
            // $this->book_stock->delete_book_stock($book_stock_id);
            // $this->print_order->delete_print_order_file($print_order->print_order_file);
            // $this->print_order->delete_letter_file($print_order->letter_file);
            // $this->print_order->delete_preprint_file($print_order->delete_preprint_file);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }

        redirect($this->pages);
    }

    public function edit_book_stock(){
        if($this->_is_warehouse_admin() == TRUE && $this->input->method()=='post'){
            $type = $this->input->post('type');
            $revision_type = $this->input->post('revision_type');
            $book_id = $this->input->post('book_id');
            $revision_date = $this->input->post('date');
            $quantity = $this->input->post('warehouse_modifier');
            $notes = $this->input->post('notes');
            $book_stock = $this->book_stock->where('book_id', $book_id)->get();
            $book_stock_revision = (object) [
                'book_id'            => $book_id,
                'warehouse_past'     => $book_stock->warehouse_present,
                'type'               => $type,
                'warehouse_present'  => 0,
                'warehouse_revision' => $quantity,
                'revision_type'      => $revision_type,
                'notes'              => $notes,
                'revision_date'      => $revision_date
            ];
            if (!$book_stock) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            }
            else {
                if ($type == "revision"){
                    if ($revision_type=="add") $book_stock->warehouse_present += $quantity;
                    else $book_stock->warehouse_present -= $quantity;
                    $book_stock_revision->warehouse_present = $book_stock->warehouse_present;
                    $book_stock_revision->revision_date = now();
                }
                elseif ($type == "return"){
                    if ($revision_type=="sub") {
                        $book_stock->warehouse_present -= $quantity;
                        $book_stock->retur_stock += $quantity;
                    }
                    else {
                        $book_stock->retur_stock -= $quantity;
                    }
                    $book_stock_revision->warehouse_present = $book_stock->warehouse_present;
                    $input->revision_date = 'revision_date';
                    
                }
                
                if ($this->book_stock->where('book_id', $book_id)->update($book_stock) && $this->db->insert('book_stock_revision',$book_stock_revision)) {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                } else {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
                }
            }
        }
        else {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
        }
        redirect('book_stock/view/'.$book_stock->book_stock_id);
    }

    public function api_chart_data($book_stock_id,$year){
        $book_transaction = $this->book_transaction->get_transaction_data($book_stock_id,$year);
        for ($i=1;$i<=12;$i++){
            $chart_data['stock_in']['month_'.$i]=0;
            $chart_data['stock_out']['month_'.$i]=0;
        }
        foreach ($book_transaction as $data){
            for ($i=1;$i<=12;$i++){
                if (substr($data->date,5,2)==$i){
                    $chart_data['stock_in']['month_'.$i]+=$data->stock_in;
                    $chart_data['stock_out']['month_'.$i]+=$data->stock_out;
                }
            }
        }
        return $this->send_json_output(true, (object) $chart_data);
    }
    public function api_get_by_book_id($book_id){
        $book_stock = $this->book_stock->get_book_stock_by_book_id($book_id);
        return $this->send_json_output(true, $book_stock);
    }
    public function generate_excel($filters)
    {
        // $get_data = $this->book_stock->filter_excel($filters);
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'STOK BUKU GUDANG';

        // Column Title
        $sheet->setCellValue('A1', 'STOK BUKU GUDANG');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A1')
                    ->getFont()
                    ->setBold(true);
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Judul');
        $sheet->setCellValue('C3', 'Stok Gudang');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:C3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:C3')
                    ->getFont()
                    ->setBold(true);

        // Auto width
        // $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $get_data = $this->book_stock->filter_excel($filters);
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'C') as $v) {
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
                            $value = $data->warehouse_present;
                            if($value <=50){
                                $spreadsheet->getActiveSheet()
                                ->getStyle('C'.$i)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB('FFC000');            
                            }
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

    public function generate_retur()
    {
        // $get_data = $this->book_stock->filter_excel($filters);
        $spreadsheet = new Spreadsheet;
        $sheet_1 = $spreadsheet->getActiveSheet()->setTitle('stok retur');
        $filename = 'STOK RETUR_'.date('Y m d');

        // Column Title
        $sheet_1->setCellValue('A1', 'STOK RETUR');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A1')
                    ->getFont()
                    ->setBold(true);
        $sheet_1->setCellValue('A3', 'No');
        $sheet_1->setCellValue('B3', 'Judul');
        $sheet_1->setCellValue('C3', 'Stok Retur');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:C3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:C3')
                    ->getFont()
                    ->setBold(true);

        // Auto width
        // $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet_1->getColumnDimension('B')->setAutoSize(true);
        $sheet_1->getColumnDimension('C')->setAutoSize(true);

        $get_data = $this->book_stock->retur_stock();
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'C') as $v) {
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
                            $value = $data->retur_stock;
                            break;
                        }
                }
                $sheet_1->setCellValue($v . $i, $value);
            }
            $i++;
        }

        // Create new sheet
        $spreadsheet->createSheet();
        // Zero based, so set the second tab as active sheet
        $spreadsheet->setActiveSheetIndex(1);
        $sheet_2 = $spreadsheet->getActiveSheet()->setTitle('log retur');
        // Column Title
        $sheet_2->setCellValue('A1', 'LOG RETUR');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A1')
                    ->getFont()
                    ->setBold(true);
        $sheet_2->setCellValue('A3', 'No');
        $sheet_2->setCellValue('B3', 'Judul Buku');
        $sheet_2->setCellValue('C3', 'Jumlah Retur');
        $sheet_2->setCellValue('D3', 'Tanggal');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:D3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:D3')
                    ->getFont()
                    ->setBold(true);

        // Auto width
        // $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet_2->getColumnDimension('B')->setAutoSize(true);
        $sheet_2->getColumnDimension('C')->setAutoSize(true);
        $sheet_2->getColumnDimension('D')->setAutoSize(true);

        $get_data = $this->book_stock->log_retur();
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'D') as $v) {
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
                            $value = $data->warehouse_revision;
                            break;
                        }
                    case 'D': {
                        $value = format_datetime($data->revision_date);
                        break;
                        }
                }
                $sheet_2->setCellValue($v . $i, $value);
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

    private function _is_warehouse_admin()
    {
        if ($this->level == 'superadmin' || $this->level == 'admin_gudang') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang dan superadmin yang dapat mengakses.');
            return false;
        }
    }
}
