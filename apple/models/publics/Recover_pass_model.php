<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
* Модель работает на публичной стороне приложения
*/

class Recover_pass_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //функции для восстановления пароля
    // чтобы вернуть отправку кода по вк, первым параметром передать for_deals_id
    public function add_recovery_code($data)
    {

        return $this->db->insert('recovery_password', $data);

//        if($result) {
//
//            //если код сохранен, мы вытаскиваем ID чата в ВК
//            $this->db->where('user_deals_id', $user_deals_id);
//            $query = $this->db->get('vk_notify');
//
//            if($query->num_rows() > 0)
//            {//если нам вернулось како-то значение, то возвращаем строку значений
//                return $query->row_array();
//            }
//            else
//            {//иначе возвращаем FALSE - т.е. компании с таким номером нет
//                return FALSE;
//            }
//
//        } else {
//            return FALSE;
//        }
    }

    public function find_active_code($code)
    {


        $this->db->where('code', $code);
        $this->db->where('time >', time());
        $this->db->limit(1);
        $query = $this->db->get('recovery_password');

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {

            //иначе у нас время кода истекло - удаляем все устаревшие коды
            $this->delete_old_codes();

            return false;

        }

    }

    //функция удаления старых кодов для восстановления пароля
    public function delete_old_codes()
    {

        $this->db->where('time <', time());
        $this->db->delete('recovery_password');

    }

    public function update_password($user_type, $user_phone, $new_pass)
    {

        if ($user_type == "company") {

            $phone_field = "company_phone";
            $table = "companies";

        } elseif ($user_type == "worker") {

            $phone_field = "phone";
            $table = "workers";

        } else {
            return false;
        }

        $data = [
            'password' => $new_pass,
        ];

        $this->db->where($phone_field, $user_phone);
        $query = $this->db->update($table, $data);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}