<?php defined('BASEPATH') or exit('No direct script access allowed');

class earning_model extends MY_Model
{
    protected $table    = 'invoice';
    public function filter_total($filters)
    {
        return $this->select('(qty*price*(1-discount/100)) AS earning')
            ->join('invoice_book', 'invoice.invoice_id = invoice_book.invoice_id')
            ->order_by('invoice.invoice_id', 'ASC')
            ->when('date_year', $filters['date_year'])
            ->when('date_month', $filters['date_month'])
            ->where('invoice.status', 'confirm')
            ->get_all();
        //select sum((qty*price*(1-discount/100))) as earning from invoice right join invoice_book on invoice.invoice_id = invoice_book.invoice_id;
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data) {
            if ($params == 'date_year') {
                $this->where('YEAR(invoice.issued_date)', $data);
            }

            if ($params == 'date_month') {
                $this->where('MONTH(invoice.issued_date)', $data);
            }
        }
        return $this;
    }
}
