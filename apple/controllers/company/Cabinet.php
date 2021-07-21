<?php

use Barter\Payment;

defined('BASEPATH') or exit('No direct script access allowed');

class Cabinet extends CI_Controller
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

        $data = [
            'title' => 'Главная',
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

            //Обновляем время онлайна
            $this->CModel->update_company_data((int)$_SESSION['ses_company_data']['company_id'], ['was_online' => 'NOW()'], false);

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
            ];

            //$recommended_companies = $this->CModel->recommended_company($city);
            $recommended_companies = $this->CModel->recommended_company_ext($_SESSION['ses_company_data']['company_id']);
            $last_new_companies = $this->CModel->find_last_new_companies($city);

            $data += [
                'last_new_companies' => $last_new_companies,
                'last_new_companies_categories' => $this->CModel->companies_categories(array_column($last_new_companies, 'company_id')),
                //'company_categories' => $this->CATModel->get_categories(), //find_company_categories('123')
                'recommended_company' => $recommended_companies,
                'recommended_companies_categories' => $this->CModel->companies_categories(array_column($recommended_companies, 'company_id')),
                'fave_company_array' => $this->CModel->get_faves_company_for_me((int) $_SESSION['ses_company_data']['company_id']),
                'category_list' => $category_list,
                'ref_link' => $this->CModel->getRefHref($_SESSION['ses_company_data']['company_id']),
                'last_products' => $this->CModel->get_latest_products(),
                'taxi_cars' => count(\Barter\Taxi::availableUsers()),
                'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
                'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
                'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
                'all_news' => $this->CModel->get_all_news_profile(),
            ];

                $this->load->view('company_area/header_view', $data);
                $this->load->view('company_area/home_view');
                $this->load->view('company_area/footer_view');
                
        }
    }

    public function company_detail()
    {
        if($this->agent->is_mobile()) {
            if (empty($this->input->post('company_id'))) {
                $data = [
                    'text_message' => 'Ошибка получения id',
                ];
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'data' => $data,
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));
            }

            $company_id = (int) $this->input->post('company_id');
            //если чел пытается что-то нам сунуть непонятное, обрываем все и перекидываем на главную стр.кабинета
            if (! $company_id) {
                $data = [
                    'text_message' => 'Неверный id',
                ];
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'data' => $data,
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));
            }
        }
        else {
            if (! isset($_GET['company_id']) || $_GET['company_id'] == '') {
                redirect(base_url() . 'company/cabinet', 'location', 301);
            }

            $company_id = (int) $_GET['company_id'];
            //если чел пытается что-то нам сунуть непонятное, обрываем все и перекидываем на главную стр.кабинета
            if (! $company_id) {
                redirect(base_url('company/cabinet'), 'location', 301);
            }
        }

        $company_data
            = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->CATModel->get_categories();
        $this->load->helper('cookie');
        $result = $this->categories->update_array_identificator($categories);
        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }

        $cookie = get_cookie('view_' . $company_id);
        if ($cookie === null) {
            set_cookie('view_' . $company_id, '1', 43200);
            $this->CModel->update_view($company_id);
        }

        $company_categories = $this->CATModel->find_company_categories($company_id);
        foreach ($company_categories as $cat) {
            $cookie = get_cookie('category_' . $cat['category_id']);
            if ($cookie === null) {
                if ($this->CModel->update_category_statistics($_SESSION['ses_company_data']['company_id'], $cat['category_id'])) {
                    set_cookie('category_' . $cat['category_id'], '1', 43200);
                }
            }
        }

        $company_detail = $this->CModel->find_company_detail_by_id($company_id);
        //$company_detail['was_online'] = (new DateTime())
        $fordealsID = $company_detail['for_deals_id'];
        //unset($company_detail['for_deals_id']);
        $views = $this->CModel->views($company_id);
        $data = [
            'title' => 'Компания &laquo;' . $company_detail['company_name']
                . '&raquo;',
            //'company_data' => ['barter_balance' => $company_data['barter_balance']],
            'isliked' => $this->CModel->isLiked($_SESSION['ses_company_data']['company_id'], $company_id),
            'sum_all_orders' => $this->CModel->sum_all_orders($_SESSION['ses_company_data']['deals_id']),
            'category_list' => $category_list,
            'company_detail' => $company_detail,
            'company_data' => [
                'barter_balance' => $company_data['barter_balance'],
                'credit_balance' => $company_data['credit_balance'],
                'month_limit' => $company_data['month_limit'],
                'status' => $company_data['status'],
                'logo' => $company_data['logo'],
                'sverh_limit'=>$company_data['sverh_limit'],
                'sub_status' =>$company_data['sub_status'],
                'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
                ],
            'fave_company_array' => $this->CModel->get_faves_company_for_me((int) $company_id),
            'company_categories' => $this->CATModel->find_company_categories($company_id),
            'views' => $views,
            'products' => $this->CModel->get_products($company_id, null, false), //Товары
            'fave_count' => $this->CModel->likes($company_id),

            'reviews' => \Barter\Review::getAll($company_detail['company_id']),
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
            'month_sales_detail' => $this->CModel->total_month_sales($fordealsID),

            'coupons' => $result = $this->CModel->get_company_coupons((int) $_SESSION['ses_company_data']['company_id'], 0),
        ];
        if ($company_detail) {
            $data += [
                'vk_chat_id' => $this->CModel->find_vk_chat_id($company_detail['for_deals_id']),
            ];
        }

        $this->load->model('all_areas/Cities_model', 'CityModel');
        $data += [
            'cities' => $this->CityModel->getAll(),
            'default_city' => $this->CityModel->getDefault($_SESSION['ses_company_data']['company_id']),
        ];

        if($this->agent->is_mobile()) {
            $data += [
                'text_message' => 'Данные успешно получены',
            ];
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'data' => $data,
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));
        }
        else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/company_detail_view');
            $this->load->view('company_area/footer_view');
        }
    }

    public function getcomp()
    {
        echo implode(',', $this->CModel->getwithoutcategory());
    }

    public function checkout(): void
    {
        $payment = Payment::getInstance()->createPayment(
            COST_SERVICE,
            MONTHLY_PAYMENT,
            sprintf('Оплата аккаунта: ID %s', $_SESSION['ses_company_data']['company_id']),
            ['company_id' => $_SESSION['ses_company_data']['company_id']]);

        redirect($payment->getConfirmation()->confirmationUrl);
    }
}
