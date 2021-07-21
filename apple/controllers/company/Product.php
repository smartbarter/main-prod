<?php


class Product extends CI_Controller
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
        $this->load->library('categories');
        $this->load->library('user_agent');
    }

    public function index()
    {
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
        $this->load->library('pagination'); //Подключаем пагинацию
        $this->load->helper('url');

        $config['base_url'] = base_url().'company/product';

        $config['first_url'] = $config['base_url'];
        $config['enable_query_strings'] = true;
        $config['query_string_segment'] = 'page';
        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['reuse_query_string'] = true;
        $config['total_rows'] = $this->CModel->get_count_company_products($_SESSION['ses_company_data']['company_id']);
        $config['per_page'] = 12;
        $config['num_links'] = 2;

        $config['full_tag_open'] = "<div class=\"pagination\">";
        $config['full_tag_close'] = "</div>";
        $config['next_link'] = "&raquo;";
        $config['next_tag_open'] = '<div>';
        $config['next_tag_close'] = '</div>';

        $config['prev_link'] = "&laquo;";
        $config['prev_tag_open'] = '<div>';
        $config['prev_tag_close'] = '</div>';

        $config['first_link'] = false;
        $config['last_link'] = false;

        $config['cur_tag_open'] = "<div class=\"bg-highlight color-white\"><a href=\"#\">";
        $config['cur_tag_close'] = "</a></div>";

        $config['num_tag_open'] = '<div>';
        $config['num_tag_close'] = '</div>';

        if (!empty($this->input->get('page'))) {
            $offset = ($this->input->get('page') - 1) * $config['per_page'];
        } else {
            $offset = null;
        }


        $this->pagination->initialize($config);
        $data = [
            'title' => 'Мои товары',
            'company_data' => $company_data,
            'category_list' => $category_list,
        ];

        $this->load->model('all_areas/Cities_model', 'CityModel');

        $data += [
            'cities' => $this->CityModel->getAll(),
            'default_city' => $this->CityModel->getDefault($_SESSION['ses_company_data']['company_id']),
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_value' => $this->security->get_csrf_hash(),
            'links' => $this->pagination->create_links(),
            'products' => $this->CModel->get_products($_SESSION['ses_company_data']['company_id'], $config['per_page'],
                $offset, false),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
        ];
        if ($this->agent->is_mobile()) {
            $data += [
                'product_categories' => $this->CATModel->get_product_categories()
            ];
        }
        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data);
            $this->load->view('company_area/mobile/products_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/products_view');
            $this->load->view('company_area/footer_view');
        }
    }

    public function add()
    {
        if (empty($this->input->post())) {
            redirect('/company/product', 'refresh', '301');
        }
        $image = null;
        $company_id = [
            'company_id' => $_SESSION['ses_company_data']['company_id'],
        ];
        if (!empty($_FILES)) {
            $config['upload_path'] = './uploads/products_image/';//куда грузим
            $config['allowed_types'] = 'gif|jpg|jpeg|png';//допустимые форматы
            $config['max_size'] = 10240;//указываем в KB
            $config['file_ext_tolower'] = true;//расширение файла в нижнем регистре
            $config['remove_spaces'] = true;//меняем пробелы в имени на нижнее подчеркивание
            $config['encrypt_name'] = true;//хешируем имя файлы, чтоб не переписать чужой логотип

            $this->load->library('upload', $config)
            ;
            if ($this->upload->do_upload('product_image')) {

                $image = $this->upload->data('file_name');
                $resize = [
                    'image_library' => 'gd2',
                    'width' => 250,
                    'height' => 250,
                    'maintain_ratio' => true,
                    'source_image' => './uploads/products_image/'.$image,
                    'quality' => 100,
                    //'new_image' => './uploads/products_image/' . $image,
                ];
                $this->load->library('image_lib', $resize);
                $this->image_lib->resize();
                //$this->image_lib->clear();
            }
        }

        $this->CModel->add_product(array_merge($this->input->post(), $company_id), $image);
        redirect('/company/product', 'refresh', '301');
    }

    public function all()
    {
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

        $data = [];

        $category_id = 0;
        if (isset($_GET['filter'])) {
            $category_id = $_GET['filter'];
        }

        /*Постраничную навигацию формируем*/
        $this->load->library('pagination'); //Подключаем пагинацию
        $this->load->helper('url');

        $config['base_url'] = base_url().'company/product/all';

        $config['first_url'] = $config['base_url'];
        $config['enable_query_strings'] = true;
        $config['query_string_segment'] = 'page';
        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['reuse_query_string'] = true;
        $config['total_rows'] = $this->CModel->get_all_products(true, $category_id);
        $config['per_page'] = 50;
        $config['num_links'] = 4;

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



        if (!empty($this->input->get('page'))) {
            $offset = ($this->input->get('page') - 1) * $config['per_page'];
        } else {
            $offset = null;
        }

        $this->pagination->initialize($config);

        $data += [
            'title' => 'Все товары/услуги',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'links' => $this->pagination->create_links(),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
            'products' => $this->CModel->get_all_products(false, $category_id, $config['per_page'], $offset),
        ];

        if ($this->agent->is_mobile()) {
            $data += [
                'product_categories' => $this->CATModel->get_product_categories()
            ];
        }

        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data);
            $this->load->view('company_area/mobile/all_products_view');
            $this->load->view('company_area/mobile/footer_view');
        }
        else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/all_products_view');
            $this->load->view('company_area/footer_view');
        }
    }

    public function search()
    {
        if (empty($this->input->get('search'))) {
            redirect(base_url(), 'location', 301);
        }
        $search = $this->input->get('search');
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

        /*Постраничную навигацию формируем*/
        $this->load->library('pagination'); //Подключаем пагинацию
        $this->load->helper('url');

        $config['base_url'] = base_url().'company/product/search';
        $config['first_url'] = $config['base_url'].'?search='.$search;
        $config['enable_query_strings'] = true;
        $config['query_string_segment'] = 'page';
        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['reuse_query_string'] = true;
        $config['total_rows'] = $this->CModel->search_product($search, true);
        $config['per_page'] = 12;
        $config['num_links'] = 2;
        $config['full_tag_open'] = "<ul class=\"pagination\">";
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

        if (!empty($this->input->get('page')) && $this->input->get('page') > 1) {
            $offset = ($this->input->get('page') - 1) * $config['per_page'];
        } else {
            $offset = null;
        }

        $this->pagination->initialize($config);
        $data = [
            'title' => 'Поиск товары/услуги',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'links' => $this->pagination->create_links(),
            'products' => $this->CModel->search_product($search, false, $config['per_page'], $offset),
            'get_back' => true,
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
        ];
        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data);
            $this->load->view('company_area/mobile/all_products_view');
            $this->load->view('company_area/mobile/footer_view');
        }
        else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/all_products_view');
            $this->load->view('company_area/footer_view');
        }
    }
}