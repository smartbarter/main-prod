<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_job extends CI_Controller {

    public function __construct(){
       parent::__construct();

        //запрещаем доступ к любому методу, если нет ключа
        if(!isset($_GET['key']) || (string)$_GET['key'] !== CRON_JOB_KEY) {
            redirect(base_url('publics/page_not_found'), 'location', 301);
        }       

       $this->load->model('company_cabinet/Payments_model','PModel');
       $this->load->model('admin_cabinet/Advert_model', 'AdvModel');
        $this->load->model('admin_cabinet/Company_model', 'CModel');
    }

    public function update_status_company() {
        $result = $this->PModel->cron_job_update_month_limit();
        echo $result;
    }

    public function update_advert_company() {
        $result = $this->AdvModel->auto_advert();
        echo $result;
    }

    public function send_ap_notifications() {
        $result = $this->CModel->send_ap_expire_notifications();
        echo $result;
    }

    // public function test() {
    //     $result = $this->PModel->update_rub_balance();
    //     echo $result;
    // }

}