<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Roles {

    //в БД заложена структура ролей RBAC, но нет времени писать функционал

    private $ci;
    
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model('admin_cabinet/roles_model', 'RModel');
    }

    public function test() {
        $this->ci->RModel->test();
    }

}