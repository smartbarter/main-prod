<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 02.05.2019
 * Time: 22:46
 */

class Menu extends CI_Controller
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
    }

    public function index()
    {
        //делаем запрос в БД, чтобы вытащить все данные для главной страницы

        $company_data
            = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $data = [
            'title' => 'Меню',
            'company_data' => $company_data,
        ];
        $this->load->model('all_areas/Cities_model', 'CityModel');


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
            //иначе вытаскиваем все данные, манагера, сделки и т.д.
            //делаем еще один запрос в БД, да тупо, но время, не до оптимизации!


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
                'category_list' => $category_list,
            ];
            $data += [
                'cities' => $this->CityModel->getAll(),
                'default_city' => $this->CityModel->getDefault((int) $_SESSION['ses_company_data']['company_id']),
            ];
            $data += [
                'all_news' => $this->CModel->get_all_news(),
                'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
                'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
                'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
            ];

            $this->load->view('company_area/mobile/header_view', $data);
            $this->load->view('company_area/mobile/menu_view');
            $this->load->view('company_area/mobile/footer_view');
        }
    }
}
