<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fave extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['ses_company_data'])) {
            redirect(base_url(), 'location', 301);
        }

        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load->model('all_areas/Category_model', 'CATModel');
        $this->load->library('categories');
        $this->load->library('user_agent');
    }

    public function index()
    {
        $user = (int)$_SESSION['ses_company_data']['company_id'];

        // die('123');
        $faves = $this->CModel->get_faves($user);

        $company_data = $this->
        CModel->
        find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->
        CATModel->
        get_categories();

        $result = $this->categories->update_array_identificator($categories);

        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }


        $data = [
            'title' => 'Избранное',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'faves' => $faves,
        ];

        $this->load->model('all_areas/Cities_model', 'CityModel');
        $data += [
            'cities' => $this->CityModel->getAll(),
            'default_city' => $this->CityModel->getDefault($_SESSION['ses_company_data']['company_id']),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
        ];
        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data );
            $this->load->view('company_area/mobile/faves_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/faves_view');
            $this->load->view('company_area/footer_view');
        }
    }
}
