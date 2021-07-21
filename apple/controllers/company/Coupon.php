<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Coupon extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        //запрещаем доступ к любому методу, если чел не авторизован!
        if (! isset($_SESSION['ses_company_data'])) {
            redirect(base_url(), 'location', 301);
        }

        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load->model('all_areas/Category_model', 'CATModel');
        $this->load->library('categories');
        $this->load->library('user_agent');
    }

    public function index()
    {
        //делаем запрос в БД, чтобы вытащить все данные для главной страницы
        $company_data
            = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);


        $this->load->model('all_areas/Cities_model', 'CityModel');
        $city = null;
        if (! empty($_SESSION['ses_company_data']['company_id'])) {
            $city
                = $this->CityModel->getDefault((int) $_SESSION['ses_company_data']['company_id'])['for_search'];
            if ($city == 0) {
                $city = null;
            }
        }

        if (
            $company_data['status'] == 0
            || //компания заблокирована
            $company_data['status'] == 4//на расторжении договора
        ) {
            $this->load->view('company_area/without_active_bot_view', $data);
        } elseif ($company_data['status'] == 5) {
            //если компания удалена, делаем редирект и выкидываем из кабинета
            redirect(base_url() . 'publics/logout', 'location', 301);
        } else {

            $data = [
                'title' => 'Купоны',
                'company_data' => $company_data,
                'coupons' => $this->CModel->get_company_coupons((int) $_SESSION['ses_company_data']['company_id']),
            ];

            $categories = $this->CATModel->get_categories($city);

            $result
                = $this->categories->update_array_identificator($categories);

            if ($result) {
                $category_list
                    = $this->categories->categories_to_string($result);
            } else {
                $category_list = false;
            }
            $data += [
                'cities' => $this->CityModel->getAll(),
                'default_city' => $this->CityModel->getDefault((int) $_SESSION['ses_company_data']['company_id']),
                'category_list' => $category_list,
            ];

            if ($this->agent->is_mobile()) {
                $this->load->view('company_area/mobile/header_view', $data);
                $this->load->view('company_area/mobile/coupon_view');
                $this->load->view('company_area/mobile/footer_view');

            } else {
                $this->load->view('company_area/header_view', $data);
                $this->load->view('company_area/coupon_view');
                $this->load->view('company_area/footer_view');
            }
        }
    }

}