<?php defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'customer';
        $this->load->model('customer_model', 'customer');
    }

    public function index($page = null)
    {
        $filters = [
            'keyword' => $this->input->get('keyword', true),
            'type'  => $this->input->get('type', true)
        ];

        $customer_type = array(
            'Distributor'      => 'Distributor',
            'Reseller'      => 'Reseller',
            'Penulis'        => 'Penulis',
            'Member'        => 'Member',
            'Biasa'        => ' - '
        );

        // custom per page
        $this->customer->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->customer->filter_data($filters, $page);
        $discount = $this->customer->get_discount();

        $customers  = $get_data['customers'];
        $total      = $get_data['total'];
        $pages      = $this->pages;
        $main_view  = 'customer/index_customer';
        $pagination = $this->customer->make_pagination(site_url('customer'), 2, $total);
        $this->load->view('template', compact('pagination', 'pages', 'main_view', 'customers', 'total', 'customer_type', 'discount'));
    }

    public function api_customer_info($customer_id)
    {
        $customer =  $this->customer->get_customer($customer_id);
        $data = json_encode($customer);
        echo $data;
    }

    public function add_customer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nama Customer', 'required');
        $this->form_validation->set_rules('type', 'Tipe Customer', 'required');
        $this->form_validation->set_rules('phone-number', 'Nomor HP Customer', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Customer gagal ditambah.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->customer->add_customer();
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Customer berhasil ditambah.');
                redirect('customer');
            } else {
                $this->session->set_flashdata('error', 'Customer gagal ditambah.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
    }

    public function update_customer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('editName', 'Nama Customer', 'required');
        $this->form_validation->set_rules('editType', 'Tipe Customer', 'required');
        $this->form_validation->set_rules('editPhone-number', 'Nomor HP Customer', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Customer gagal diupdate.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->customer->update_customer();
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Customer berhasil diupdate.');
                redirect('customer');
            } else {
                $this->session->set_flashdata('error', 'Customer gagal diupdate.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
        redirect('customer');
    }

    public function delete($id = null)
    {
        $this->customer->delete_customer($id);
        redirect('customer');
    }

    public function edit_discount()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('distributor', 'Distributor', 'required');
        $this->form_validation->set_rules('reseller', 'Reseller', 'required');
        $this->form_validation->set_rules('penulis', 'Penulis', 'required');
        $this->form_validation->set_rules('member', 'Member', 'required');
        $this->form_validation->set_rules('biasa', 'Biasa', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Diskon gagal diupdate.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->customer->edit_discount();
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Diskon berhasil diupdate.');
                redirect('customer');
            } else {
                $this->session->set_flashdata('error', 'Diskon gagal diupdate.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
        redirect('customer');
    }
}
