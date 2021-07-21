<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Search extends CI_Controller
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
        if($this->agent->is_mobile()) {
            $data = array();
            $this->load->view('company_area/mobile/header_view', $data);
            $this->load->view('company_area/mobile/company_search_view');
            $this->load->view('company_area/mobile/footer_view');
        }
        else {
            $company_data
                = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

            $categories = $this->CATModel->get_categories($city);

            $result = $this->categories->update_array_identificator($categories);

            if ($result) {
                $category_list = $this->categories->categories_to_string($result);
            } else {
                $category_list = false;
            }
            $this->load->library('pagination'); //Подключаем пагинацию
            $this->load->helper('url');

            $config['base_url'] = base_url().'company/search';

            $config['first_url'] = $config['base_url'];
            $config['enable_query_strings'] = true;
            $config['query_string_segment'] = 'page';
            $config['page_query_string'] = true;
            $config['use_page_numbers'] = true;
            $config['reuse_query_string'] = true;
            $config['per_page'] = 30;
            $config['num_links'] = 7;

            $config['total_rows'] = $this->CModel->get_all_companies(true);
            $config['full_tag_open']
                = "<ul class=\"pagination\">";
            $config['full_tag_close'] = "</ul>";
            $config['next_link'] = "&raquo;";
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_link'] = "&laquo;";
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';

            $config['first_link'] = false;
            $config['last_link'] = false;

            $config['cur_tag_open'] = "<li class=\"active\"><a href=\"#\">";
            $config['cur_tag_close'] = "</a></li>";

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            if (!empty($this->input->get('page'))) {
                $offset = $this->input->get('page') * $config['per_page'];
            } else {
                $offset = null;
            }

            $this->pagination->initialize($config);
            $data = [
                'title' => 'Компании',
                'company_data' => $company_data,
                'category_list' => $category_list,
            ];
            $default_city = $this->CityModel->getDefault($_SESSION['ses_company_data']['company_id']);
            $data += [
                'cities' => $this->CityModel->getAll(),
                'default_city' => $default_city,
                'links' => $this->pagination->create_links(),
                'companies' => $this->CModel->get_all_companies(false, $config['per_page'], $offset, $default_city['for_search']),
                'fave_company_array' => $this->CModel->get_faves_company_for_me($_SESSION['ses_company_data']['company_id']),
                'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
                'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
                'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
            ];

            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/all_companies_view');
            $this->load->view('company_area/footer_view');
        }

    }
    public function all ()
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
        $this->load->library('pagination'); //Подключаем пагинацию
        $this->load->helper('url');

        $config['base_url'] = base_url() . 'company/search';

        $config['first_url'] = $config['base_url'];
        $config['enable_query_strings'] = true;
        $config['query_string_segment'] = 'page';
        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['reuse_query_string'] = true;
        $config['per_page'] = 30;
        $config['num_links'] = 7;

        $config['total_rows'] = $this->CModel->get_all_companies(true);
        $config['full_tag_open']
            = "<ul class=\"pagination\">";
        $config['full_tag_close'] = "</ul>";
        $config['next_link'] = "&raquo;";
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = "&laquo;";
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['first_link'] = false;
        $config['last_link'] = false;

        $config['cur_tag_open'] = "<li class=\"active\"><a href=\"#\">";
        $config['cur_tag_close'] = "</a></li>";

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        if (!empty($this->input->get('page'))) {
            $offset = $this->input->get('page') * $config['per_page'];
        } else {
            $offset = null;
        }

        $this->pagination->initialize($config);
        $data = [
            'title' => 'Компании',
            'company_data' => $company_data,
            'category_list' => $category_list,
        ];
        $default_city = $this->CityModel->getDefault($_SESSION['ses_company_data']['company_id']);
        $data += [
            'cities' => $this->CityModel->getAll(),
            'default_city' => $default_city,
            'links' => $this->pagination->create_links(),
            'companies' => $this->CModel->get_all_companies(false, $config['per_page'], $offset, $default_city['for_search']),
            'fave_company_array' => $this->CModel->get_faves_company_for_me($_SESSION['ses_company_data']['company_id']),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
        ];
        if ($this->agent->is_mobile()) {
            $data = array();
            $this->load->view('company_area/mobile/header_view', $data);
            $this->load->view('company_area/mobile/all_companies_view');
            $this->load->view('company_area/mobile/footer_view');
        } else {
        $this->load->view('company_area/header_view', $data);
        $this->load->view('company_area/all_companies_view');
        $this->load->view('company_area/footer_view');
        }
    }


}