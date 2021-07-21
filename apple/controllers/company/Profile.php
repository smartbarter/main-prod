<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
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

        $company_data = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->
        CATModel->
        get_categories();

        $result = $this->categories->update_array_identificator($categories);

        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }

        $taxi = new \Barter\Taxi($_SESSION['ses_company_data']['company_id']);

        $data = [
            'title' => 'Настройки профиля',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'has_taxi' => $taxi->hasTaxi(),
            'can_change_description' => $this->CModel->can_change_description($_SESSION['ses_company_data']['company_id']),
        ];
        if ($data['has_taxi']) {
            $data += ['taxi_info' => $taxi->info()];
        }
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
            $this->load->view('company_area/mobile/profile_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/profile_view');
            $this->load->view('company_area/footer_view');
        }
    }
    public function advert()
    {
        $this->load->library('session');
        $company_id = $this->input->post('company_id');
        $from = $this->input->post('from_advert');

        $res = $this->CModel->store($company_id, $from);

        if ($this->agent->is_mobile()) {
            if ($res) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => "Заявка на рекламу успешно отправлена!",
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => "Увы, но нам не удалось отправить заявку!",
                    ]));
            }
            return;
        }

        if ($res) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Успешно';
        } else {
            $_SESSION['status'] = 'danger';
            $_SESSION['message'] = 'Что-то пошло не так. Скорее всего вы уже отправили заявку.';
        }
        $this->session->mark_as_flash(['status', 'message']);
        redirect($this->agent->referrer());
    }

}