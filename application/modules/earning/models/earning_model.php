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
            ->where('MONTH(invoice.issued_date)', $filters['date_month'])
            ->where('invoice.type', $filters['invoice_type']);
        return $this->db->get()->result();
        //select sum((qty*price*(1-discount/100))) as earning from invoice right join invoice_book on invoice.invoice_id = invoice_book.invoice_id;
    }

    public function filter_detail($filters)
    {
        $this->db->select('SUM(qty*price*(1-discount/100)) AS earning')
            ->from('invoice')
            ->join('invoice_book', 'invoice.invoice_id = invoice_book.invoice_id', 'right')
            ->order_by('invoice.invoice_id', 'ASC')
            ->where('invoice.type', $filters['invoice_type'])
            ->where('YEAR(invoice.issued_date)', $filters['date_year']);
        if ($filters['date_month'] != '') {
            $this->db->where('MONTH(invoice.issued_date)', $filters['date_month']);
        }
        return $this->db->get()->row();
    }
}
