<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends CI_Controller {

    public function __construct(){
       parent::__construct();

       //запрещаем доступ к любому методу, если чел не авторизован!
       if(!isset($_SESSION['ses_company_data'])) {
           redirect(base_url(), 'location', 301);
       }

       $this->load->model('company_cabinet/Company_model', 'CModel');
       $this->load->model('company_cabinet/Payments_model', 'PModel');
       $this->load->model('all_areas/Category_model', 'CATModel');
       $this->load->library('categories');
        $this->load->library('user_agent');
    }

    public function index() {

        $company_data = $this->
                        CModel->
                        find_company_and_manager($_SESSION['ses_company_data']['deals_id']);
        
        $categories = $this->
                        CATModel->
                        get_categories();
        
        $result = $this->categories->update_array_identificator($categories);

        if($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = FALSE;
        }

        $this->load->library('pagination'); //Подключаем пагинацию

        $config['base_url'] = base_url() . 'company/payments/index';
        $config['total_rows'] = $this->PModel->count_all_payments_company($_SESSION['ses_company_data']['company_id']); //передаем id юзера
        $config['per_page'] = 10;
        $config['num_links'] = 7;

        $config['full_tag_open'] = "<nav><ul class=\"pagination\">";
        $config['full_tag_close'] = "</ul></nav>";

        $config['next_link'] = "&raquo;";
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = "&laquo;";
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;

        $config['cur_tag_open'] = "<li class=\"active\"><a href=\"#\">";
        $config['cur_tag_close'] = "</a></li>";

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';


        $this->pagination->initialize($config);

        $data = [
            'title'          => 'История платежей',
            'company_data'   => $company_data,
            'category_list'  => $category_list,
            'payments'       => $this->PModel->find_payments_company(
                                                            $_SESSION['ses_company_data']['company_id'],
                                                            $config['per_page'], $this->uri->segment(4))
        ];

        $this->load->model('all_areas/Cities_model','CityModel');
        $data += [
            'cities' => $this->CityModel->getAll(),
            'default_city' => $this->CityModel->getDefault($_SESSION['ses_company_data']['company_id']),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
        ];

        $this->load->view('company_area/header_view', $data);
        $this->load->view('company_area/payments_view');
        $this->load->view('company_area/footer_view');

    }

    public function success_payment() {

        $company_data = $this->
                        CModel->
                        find_company_and_manager($_SESSION['ses_company_data']['deals_id']);
        
        $categories = $this->
                        CATModel->
                        get_categories();
        
        $result = $this->categories->update_array_identificator($categories);

        if($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = FALSE;
        }

        $data = [
            'title'          => 'Успешный платеж!',
            'company_data'   => $company_data,
            'category_list'  => $category_list
        ];
        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data );
            $this->load->view('company_area/mobile/success_yandex_payment_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/success_yandex_payment_view');
            $this->load->view('company_area/footer_view');
        }

    }

}