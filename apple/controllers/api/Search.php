<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }
        $this->load->library('user_agent');

    }

    public function search_company()
    {

        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load->model('all_areas/Cities_model', 'CityModel');

        $search_data = (string)$this->input->post('search_field');
        $type_search = (string)$this->input->post('type_search');

        $city = null;
        if (!empty($_SESSION['ses_company_data']['company_id'])) {
            $city = $this->CityModel->getDefault((int)$_SESSION['ses_company_data']['company_id'])['for_search'];
            if ($city == 0) {
                $city = null;
            }
        }
        switch ($type_search) {
            case 'worker':
                $result = $this->CModel->search_company($search_data);
                break;
            case 'company':
                $result = $this->CModel->search_company($search_data, $city);
                break;
            default:
                $result = $this->CModel->search_company($search_data, $city);
                break;
        }


        if ($result) {

            switch ($type_search) {
                case "worker":
                    $link = "admin/";
                    break;
                case "company":
                    $link = "company/cabinet/";
                    break;
            }

            $search = '';

            if ($this->agent->is_mobile()) {
                //Мобильная версия
                foreach ($result as $row) {
                    $search .= '
                    <div  class="content" data-menu="menu-instant-3" data-height="220" onclick="open_company_detail(' . $row["company_id"] . '); document.getElementById(\'comp_data_manual\').click();">
                        <div class="content content-box round-medium shadow-small">
                            <div class="company__card">
                                <div class="company__company___img">
                                    <img class="company__img" src="https://barter-business.ru/uploads/companys_logo/' . $row["logo"] . '" alt="">
                                </div>
                                <div class="company__company__title">
                                    <span>' . mb_substr($row["company_name"], 0, 60, "UTF-8") . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                //Версия для ПК
                foreach ($result as $row) {
                    $search .= '
                    <div class="data col-lg-12 col-dark-gray">
                        <a href="' . base_url() . 'company/cabinet/company_detail?company_id=' . $row["company_id"] . '">
                            <div class="company_logo col-lg-2" style="float: left">
                                <img src="' . base_url() . 'uploads/companys_logo/' . $row["logo"] . '" class="img-circle" height="50" width="50">
                            </div>
                            <div class="company_name col-lg-10 col-dark-gray">
                                <h5>' . mb_substr($row["company_name"], 0, 60, "UTF-8") . '</h5>
                            </div>
                            <div class="company_description col-dark-gray">
                             <p>' . mb_substr($row["description_company"], 0, 160, "UTF-8") . '...</p>
                            </div>
                            <div class="company_city col-lg-12 col-dark-gray">
                            <span>г. ' . mb_convert_case($row["city_name"], MB_CASE_TITLE, "UTF-8") . '</span>
                            </div>
                            <div class="clearfix"></div>
                        </a>
                        
                    </div>
                    ';
                }
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'success',
                    'search_result' => $search
                )));

        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'fail',
                    'search_result' => '<p style="margin-left: 10px; margin-top: 10px; font-size: 14px;">Ничего не найдено...</p>'
                )));
        }
    }


}