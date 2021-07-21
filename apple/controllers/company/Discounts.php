<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Discounts extends CI_Controller {

    public function __construct(){
       parent::__construct();

       //запрещаем доступ к любому методу, если чел не авторизован!
       if(!isset($_SESSION['ses_company_data'])) {
           redirect(base_url(), 'location', 301);
       }

       $this->load->model('company_cabinet/Company_model', 'CModel');
       $this->load->model('all_areas/Category_model', 'CATModel');
       $this->load->model('all_areas/Discounts_model', 'DISCModel');
       $this->load->library('categories');

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

        //формируем постраничную навигацию
        $this->load->library('pagination'); //Подключаем пагинацию

        $config['base_url'] = base_url() . 'company/discounts/index';
        $config['total_rows'] = $this->DISCModel->count_all_discounts_today();
        $config['per_page'] = 9;
        $config['num_links'] = 2;

        $config['full_tag_open'] = "<div class=\"pagination\">";
        $config['full_tag_close'] = "</div>";
        $config['next_link'] = "&raquo;";
        $config['next_tag_open'] = '';
        $config['next_tag_close'] = '';

        $config['prev_link'] = "&laquo;";
        $config['prev_tag_open'] = '';
        $config['prev_tag_close'] = '';

        $config['first_link'] = false;
        $config['last_link'] = false;

        $config['cur_tag_open'] = "<a class='bg-highlight color-white ' href=\"#\">";
        $config['cur_tag_close'] = "</a>";


        $this->pagination->initialize($config);

        $data = [
            'title'           => 'Сегодняшние скидки',
            'company_data'    => $company_data,
            'category_list'   => $category_list,
            'discounts_today' => $this->DISCModel->find_all_discounts_with_company_data_today($config['per_page'], $this->uri->segment(4))
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
        $this->load->view('company_area/all_discounts_view');
        $this->load->view('company_area/footer_view');

    }

    public function my_discounts() {
        
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

        /*Постраничную навигацию формируем*/
        $this->load->library('pagination'); //Подключаем пагинацию
        
        $config['base_url'] = base_url() . 'company/discounts/my_discounts';
        
        $config['total_rows'] = $this->DISCModel->count_all_companies_dicsounts($_SESSION['ses_company_data']['company_id']);
        $config['per_page'] = 25;
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

        /*Конец постранично навигации*/

        $data = [
            'title'           => 'Управление скидками',
            'company_data'    => $company_data,
            'category_list'   => $category_list,
            'all_discounts'   => $this->DISCModel->all_discounts_company(
                                                            $_SESSION['ses_company_data']['company_id'],
                                                            $config['per_page'], $this->uri->segment(4)),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
        ];

        $this->load->model('all_areas/Cities_model','CityModel');
        $data += [
            'cities' => $this->CityModel->getAll(),
            'default_city' => $this->CityModel->getDefault($_SESSION['ses_company_data']['company_id'])
        ];

        $this->load->view('company_area/header_view', $data);
        $this->load->view('company_area/my_company_discounts_view');
        $this->load->view('company_area/footer_view');

    }

}