<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //запрещаем доступ к любому методу, если чел не авторизован!
        if (!isset($_SESSION['ses_company_data']) && !isset($_SESSION['ses_worker_data'])) {
            redirect(base_url(), 'location', 301);
        }

        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load->model('admin_cabinet/Admin_model', 'AModel');
        $this->load->model('all_areas/Category_model', 'CATModel');
        $this->load->library('categories');
        $this->load->library('user_agent');

    }

    public function index()
    {

        if (!isset($_GET['id'])) {
            redirect(base_url('company/cabinet'), 'location', 301);
        }

        $cat_id = (int)$_GET['id'];

        //Разбираем аргументы сортировки
        $sort_type = 0;
        $order = 'd';
        if (isset($_GET['sort'])) {
            $sort_type = $_GET['sort'];
        }
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }

        //если чел пытается сунуть в строку какую-то хрень, перекидываем его на главную стр. кабинета
        if (!$cat_id) redirect(base_url('company/cabinet'), 'location', 301);

        $categories = $this->
        CATModel->
        get_categories();

        $this->load->helper('cookie');
        $cookie = get_cookie('category_' . $cat_id);
        if ($cookie === null) {
            if ($this->CModel->update_category_statistics($_SESSION['ses_company_data']['company_id'], $cat_id)) {
                set_cookie('category_' . $cat_id, '1', 43200);
            }
        }

        /*Постраничную навигацию формируем*/
        $this->load->library('pagination'); //Подключаем пагинацию

        $config['base_url'] = base_url() . 'company/category/index';

        $this->load->model('all_areas/Cities_model', 'CityModel');
        $data_json = $this->CityModel->get_json_data((int)$cat_id);
        $city = null;
        $to_main_page = [];
        if (!empty($_SESSION['ses_company_data']['company_id'])) {
            $city = $this->CityModel->getDefault(intval($_SESSION['ses_company_data']['company_id']))['for_search'];
            if ($city == 0) {
                $city = null;
            }
            $to_main_page['for_search'] = $city;
        }

        if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
        $config['first_url'] = $config['base_url'] . '?' . http_build_query($_GET);

        $config['total_rows'] = $this->CATModel->count_all_companies_in_category($cat_id, $city);
        $config['per_page'] = 30;
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

        /*Конец постранично навигации*/

        // $line_categories_arr = $this->categories->change_id_in_array($categories);

        $result_tree_cats = $this->categories->update_array_identificator($categories);

        // $bread_crumbs = $this->categories->breadcrumbs($line_categories_arr, $cat_id);
        //если пытаются сунуть не существующую категорию, делаем редирект на главную стр.кабинета
        // if(empty($bread_crumbs)) redirect(base_url('company/cabinet'), 'location', 301);

        if ($result_tree_cats) {//лишняя проверка в работе приложения, но на старте она нужна, чтоб ошибки не выбивало
            $category_list = $this->categories->categories_to_string($result_tree_cats);
        } else {
            $category_list = FALSE;
        }

        if (isset($_SESSION['ses_company_data'])) {

            $company_data = $this->
            CModel->
            find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

            $data = [
                'title' => 'Категории',
                'company_data' => $company_data,
                'category_list' => $category_list,
                'fave_company_array' => $this->CModel->get_faves_company_for_me($_SESSION['ses_company_data']['company_id']),
                // 'breadcrumbs'             => $bread_crumbs,
                // 'cat_ids'                 => $cat_ids,
                'companies_from_category' => $this->CATModel->get_active_companies_category(
                    $cat_id,
                    $config['per_page'],
                    $this->uri->segment(4),
                    $city,
                    $sort_type,
                    $order),
                'order' => ['type' => $sort_type, 'order' => $order],
            ];


            $data += [
                'cities' => $this->CityModel->getAll(),
                'default_city' => $to_main_page,
                'data_json' => $data_json,
                'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
                'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
                'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
            ];

            if ($this->agent->is_mobile()) {
                $this->load->view('company_area/mobile/header_view', $data );
                $this->load->view('company_area/mobile/category_view');
                $this->load->view('company_area/mobile/footer_view');

            } else {
                $this->load->view('company_area/header_view', $data);
                $this->load->view('company_area/category_view');
                $this->load->view('company_area/footer_view');
            }
        } elseif (isset($_SESSION['ses_worker_data'])) {

            $worker_data = $this->AModel->find_worker_by_id($_SESSION['ses_worker_data']['worker_id']);

            $data = [
                'title' => 'Категории',
                'worker_data' => $worker_data,
                'category_list' => $category_list,
                'companies_from_category' => $this->CATModel->get_active_companies_category(
                    $cat_id,
                    $config['per_page'],
                    $this->uri->segment(4),
                    null,
                    $order_str),
                'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
                'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
                'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),

            ];

            $this->load->view('admin_area/header_view', $data);
            $this->load->view('company_area/category_view');
            $this->load->view('admin_area/footer_view');

        }

    }
    public function detail()
    {
        $company_data
            = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $data = [
            'title' => 'Категори',
            'company_data' => $company_data,
        ];
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
            //иначе вытаскиваем все данные, манагера, сделки и т.д.
            //делаем еще один запрос в БД, да тупо, но время, не до оптимизации!

            $categories = $this->CATModel->get_categories();

            $result = $this->categories->update_array_identificator($categories);


            if ($result) {
                //$goods = $this->categories->cat_to_string([$result[24]]);
                $goods = $this->categories->cat_to_string($result[24]["childs"], 24);
                $services = $this->categories->cat_to_string($result[25]["childs"], 25);
            } else {
                $goods = false;
                $services = false;
            }
            $data += [
                'cities' => $this->CityModel->getAll(),
                'default_city' => $this->CityModel->getDefault((int) $_SESSION['ses_company_data']['company_id']),
            ];


            $data += [
                'goods' => $goods,
                'services' => $services,
                'cats' => $result,
            ];

                $this->load->view('company_area/mobile/header_view', $data );
                $this->load->view('company_area/mobile/category_mobile_view');
                $this->load->view('company_area/mobile/footer_view');
                //echo json_encode($result);
        }

    }
}