<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.11.2019
 * Time: 19:28
 */



class Statistics extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['ses_company_data'])) {
            redirect(base_url(), 'location', 301);
        }

        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load->model('all_areas/Category_model', 'CATModel');
        $this->load->model('all_areas/Referral_model', 'RModel');
        $this->load->library('categories');
        $this->load->library('user_agent');
    }

    public function index()
    {
        $company_data = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->CATModel->get_categories($city);

        $result = $this->categories->update_array_identificator($categories);

        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }


        $data = [
            'title' => 'Приглашенные компании',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'referrals' => $this->RModel->getReferrals($company_data['company_id']),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
            'month_buy' => $this->CModel->total_month_buy($_SESSION['ses_company_data']['deals_id']),
            'month_count_buy' => $this->CModel->total_month_count_buy($_SESSION['ses_company_data']['deals_id']),
            'month_count_sell' => $this->CModel->total_month_count_sell($_SESSION['ses_company_data']['deals_id']),
            'total_all_month_sales' => $this->CModel->total_all_month_sales($_SESSION['ses_company_data']['deals_id']),
            'total_all_month_buy' => $this->CModel->total_all_month_buy($_SESSION['ses_company_data']['deals_id']),
            'total_all_month_count_buy' => $this->CModel->total_all_month_count_buy($_SESSION['ses_company_data']['deals_id']),
            'total_all_month_count_sell' => $this->CModel->total_all_month_count_sell($_SESSION['ses_company_data']['deals_id']),

        ];

        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data );
            $this->load->view('company_area/mobile/statistics_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/statistics_view');
            $this->load->view('company_area/footer_view');
        }
    }
}