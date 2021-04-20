<?php defined('BASEPATH') or exit('No direct script access allowed');

class Earning_model extends MY_Model
{
    //filter invoice
    public function filter_total($filters)
    {
        $this->db->select('(qty*price*(1-discount/100)) AS earning')
            ->from('invoice')
            ->join('invoice_book', 'invoice.invoice_id = invoice_book.invoice_id', 'right')
            ->order_by('invoice.invoice_id', 'ASC')
            ->where('YEAR(invoice.issued_date)', $filters['date_year'])
            ->where('MONTH(invoice.issued_date)', $filters['date_month']);
        if ($filters['invoice_type'] != '') {
            $this->db->where('invoice.type', $filters['invoice_type']);
        }
        return $this->db->get()->result();
        //select sum((qty*price*(1-discount/100))) as earning from invoice right join invoice_book on invoice.invoice_id = invoice_book.invoice_id;
    }

    // public function when($params, $data)
    // {
    //     // jika data null, maka skip
    //     if ($data) {
    //         if ($params == 'date_year') {
    //             $this->where('YEAR(invoice.issued_date)', $data);
    //         }

    //         if ($params == 'date_month') {
    //             $this->where('MONTH(invoice.issued_date)', $data);
    //         }
    //     }
    //     return $this;
    // }
}
