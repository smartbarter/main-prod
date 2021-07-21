<?php

use Barter\CompanyDeals;

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller
{

    /**
     * @var CompanyDeals
     */
    private $deals;

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

        $this->deals = new CompanyDeals($_SESSION['ses_company_data']['deals_id']);

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

        $this->load->library('pagination'); //Подключаем пагинацию

        $config['base_url'] = base_url() . 'company/orders/index';
        $config['total_rows'] = $this->deals->all()->count();
        $config['per_page'] = 25;
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
            'title' => 'Все сделки',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'deals_list' => $this->deals->allWithCompany()->paginate($config['per_page'],
                $this->uri->segment(4))->get(),
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
            $this->load->view('company_area/mobile/mdeals_list_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/deals_list_view');
            $this->load->view('company_area/footer_view');
        }

    }

    public function outbox()
    {
        $company_data = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->CATModel->get_categories($city);

        $result = $this->categories->update_array_identificator($categories);

        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }

        $this->load->library('pagination'); //Подключаем пагинацию

        $config['base_url'] = base_url() . 'company/orders/outbox';
        $config['total_rows'] = $this->deals->outboxing()->count();
        $config['per_page'] = 25;
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
            'title' => 'Исходящие сделки',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'deals_list' => $this->deals->outboxing()->withCompany('seller_deal_id')->paginate($config['per_page'],
                $this->uri->segment(4))->get(),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),

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
            $this->load->view('company_area/mobile/mdeals_list_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/deals_list_view');
            $this->load->view('company_area/footer_view');
        }

    }

    public function inbox()
    {

        $company_data = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->CATModel->get_categories($city);

        $result = $this->categories->update_array_identificator($categories);

        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }

        $this->load->library('pagination'); //Подключаем пагинацию

        $config['base_url'] = base_url() . 'company/orders/inbox';
        $config['total_rows'] = $this->deals->incoming()->count();
        $config['per_page'] = 25;
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
            'title' => 'Входящие сделки',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'deals_list' => $this->deals->incoming()->withCompany()->paginate($config['per_page'],
                $this->uri->segment(4))->get(),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
        ];

        $this->load->model('all_areas/Cities_model', 'CityModel');
        $data += [
            'cities' => $this->CityModel->getAll(),
            'default_city' => $this->CityModel->getDefault((int) $_SESSION['ses_company_data']['company_id']),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),

        ];

        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data );
            $this->load->view('company_area/mobile/mdeals_list_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/deals_list_view');
            $this->load->view('company_area/footer_view');
        }
    }

    public function unaccepted()
    {

        $company_data = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->CATModel->get_categories($city);

        $result = $this->categories->update_array_identificator($categories);

        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }

        $this->load->library('pagination'); //Подключаем пагинацию

        $config['base_url'] = base_url() . 'company/orders/inbox';
        $config['total_rows'] = $this->deals->incoming()->unaccepted()->count();
        $config['per_page'] = 25;
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
            'title' => 'Входящие сделки',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'deals_list' => $this->deals->incoming()->withCompany()->unaccepted()->paginate($config['per_page'],
                $this->uri->segment(4))->get(),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
        ];

        $this->load->model('all_areas/Cities_model', 'CityModel');
        $data += [
            'cities' => $this->CityModel->getAll(),
            'default_city' => $this->CityModel->getDefault((int) $_SESSION['ses_company_data']['company_id']),
        ];

        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data );
            $this->load->view('company_area/mobile/mdeals_list_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/deals_list_view');
            $this->load->view('company_area/footer_view');
        }
    }

    public function open_deal_detail()
    {
        $deal_id = $this->input->post('deal_id');
        $only_status = $this->input->post('stat');

        if (isset($only_status)) {
            $result = $this->CModel->get_deal_info($deal_id, true);
        } else {
            $result = $this->CModel->get_deal_info($deal_id);

            //Определяем id компании с которой была закоючена сделка
            if($result['seller_id'] == $_SESSION['ses_company_data']['company_id']) {
                $result['partner_id'] = $result['buyer_id'];
            }
            else {
                $result['partner_id'] = $result['seller_id'];
            }

            //Все лишнее удаляем
            unset($result['seller_id']);
            unset($result['buyer_id']);
        }

        if ($result) {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'data' => $result,
                    'text_message' => 'Данные по сделке успешно получены!',
                ]));

        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Данные по сделке получить не удалось!',
                ]));

        }
    }
}