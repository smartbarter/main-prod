<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publics extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //подгружаем публичную модель компании
        // $this->load->model('publics/Company_model','PUB_Comp_Model');
        $this->load->library('user_agent');
    }

    public function index()
    {
        if (isset($_SESSION['ses_company_data'])) {

            redirect(base_url().'company/cabinet', 'location', 301);//делаем редирект

        } elseif (isset($_SESSION['ses_worker_data'])) {

            redirect(base_url().'admin/cabinet', 'location', 301);//делаем редирект

        }

        $this->load->model('publics/Company_model', 'CModel');



        $data = [
            'title' => 'Главная',
//            'last_new_company' => $this->CModel->find_last_three_company_for_home_page()
            'count_deals' => $this->CModel->count_success_deals(),
            'count_users' => $this->CModel->count_active_companies(),
            'previous_month_cash' => number_format($this->CModel->get_previous_month_deals_money()['cash'] / 100,
                '0',
                '.', ' '),
        ];

            $this->load->view('public/home_view', $data);
        

    }

    public function reg()
    {

        if (isset($_SESSION['ses_company_data'])) {

            redirect(base_url().'company/cabinet', 'location', 301);//делаем редирект

        } elseif (isset($_SESSION['ses_worker_data'])) {

            redirect(base_url().'admin/cabinet', 'location', 301);//делаем редирект

        }

        $ref = $this->input->get('ref');
        if ($ref !== null) {
            $this->session->set_userdata('referal', $ref);
            redirect(base_url());
        }
    }

    public function logout()
    {
        if (isset($_COOKIE[session_name()]))//проверяем - есть ли кука относящаяся к этой сессии в браузере
        {
            setcookie(session_name(), '', time() - 42000, '/'); //удаляем куку с именем сессии
        }
        // рвем сессию
        $this->session->sess_destroy();

        redirect(base_url(), 'location', 301);//делаем редирект
    }

    //404-я ошибка
    public function page_not_found()
    {
        $this->output->set_status_header('404');
        $this->load->view('public/404_view');
    }

    public function accept_payment()
    {
        $this->load->model('publics/Company_model', 'CModel');
        $secret = YANDEX_HTTP_NOTIFICATION;
        $notification_type = $_POST["notification_type"];
        $operation_id = $_POST["operation_id"];
        $amount = $_POST["amount"];
        $currency = $_POST["currency"];
        $datetime = $_POST["datetime"];
        $sender = $_POST["sender"];
        $codepro = $_POST["codepro"];
        $label = $_POST["label"];
        $sha1_hash = $_POST["sha1_hash"];
        $hash = $notification_type.'&'.$operation_id.'&'.$amount.'&'.$currency.'&'.$datetime.'&'.$sender.'&'.$codepro.'&'.$secret.'&'.$label; //формируем хеш

        $sha1 = sha1($hash); //кодируем в SHA1
        if ($sha1 == $sha1_hash) {
            if ($codepro !== true && $_POST['unaccepted'] !== true) {
                $res = $this->CModel->update_balance($label, $amount);
                if ($res) {
                    $this->output->set_status_header(200)
                        ->set_output(json_encode(['status' => 'ok'],
                            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                        ->_display();
                }
            }
        } else {
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/apple/logs/transaction.log',
                date(DATE_RFC822).": Ошибка хэша: ".json_encode($_POST).PHP_EOL, FILE_APPEND);
        }
    }
}
