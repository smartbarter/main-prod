<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Support extends CI_Controller
{


    public function __construct()
    {

        parent::__construct();

        //запрещаем доступ к любому методу, если чел не авторизован!
        if (!isset($_SESSION['ses_company_data'])) {
            redirect(base_url(), 'location', 301);
        }
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load->model('all_areas/Category_model', 'CATModel');
        $this->load->model('all_areas/Cities_model', 'CityModel');
        $this->load->library('categories');
        $this->load->library('user_agent');

    }

    public function index()
    {
        $company_data
            = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->CATModel->get_categories($city);

        $result = $this->categories->update_array_identificator($categories);

        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }
        $data = [
            'title' => 'Помощь',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'company_cout_deal' => $this->CModel->get_prize_count_deal_companies(),
            'company_sum_deal' => $this->CModel->get_prize_sum_deal_companies(),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
            'cities' => $this->CityModel->getAll(),
            'default_city' => $this->CityModel->getDefault($_SESSION['ses_company_data']['company_id']),
        ];
        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data );
            $this->load->view('support/main_mview');
            $this->load->view('company_area/mobile/footer_view');

        } else {
        $this->load->view('company_area/header_view', $data);
        $this->load->view('support/main_view');
        $this->load->view('company_area/footer_view');
        }
    }
}