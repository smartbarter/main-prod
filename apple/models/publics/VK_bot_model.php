<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* Модель работает с Вконтакте
*/

class VK_bot_model extends CI_Model
{

    public function check_active_bot($user_type, $user_deals_id)
    {

        if ($user_type == 'comp') {
            $table = 'companies';
        } elseif ($user_type == 'work') {
            $table = 'workers';
        }

        $this->db->where('for_deals_id', $user_deals_id);
        $this->db->where('active_bot', 1);
        $query = $this->db->get($table);

        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }

    }

    public function find_confirm_bot_code($confirm_code)
    {

        $this->db->where('for_deals_id', $confirm_code);
        $query_company = $this->db->get('companies');

        if ($query_company->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query_company->row_array();
        } else {//иначе смотрим в таблице админов этот код

            $this->db->where('for_deals_id', $confirm_code);
            $query_workers = $this->db->get('workers');

            if ($query_workers->num_rows() > 0) {
                return $query_workers->row_array();
            } else {   //если мы не нашли ни в компании, ни в админах, значит этого кода вообще не существует
                return false;
            }

        }

    }

    public function add_new_vk_chat_id($user_type, $user_deals_id, $data)
    {
        //здесь должна быть транзакция, т.к. мы обновляем еще статус активации бота у юзера

        if ($user_type == "comp") {

            $table = "companies";

        } else if ($user_type == "work") {

            $table = "workers";

        }

        $user_data = [
            'active_bot' => 1
        ];

        //начинаем транзакцию
        $this->db->trans_start();

        $this->db->where('for_deals_id', $user_deals_id);
        $this->db->update($table, $user_data);

        $this->db->insert('vk_notify', $data);

        //говорим, что транзакция окончена
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            //если транзакция не удалась
            return false;
        } else {
            return true;
        }
    }

    public function update_vk_chat_id($user_deals_id, $data)
    {

        $this->db->where('user_deals_id', $user_deals_id);
        if ($this->db->update('vk_notify', $data)) {
            return true;
        } else {
            return false;
        }

    }

    //сюда передаем deals_id, т.к. это уникальный ключ
    //он не меняется и имеется у компании и админа с манагерами
    public function find_vk_chat_id($deals_id)
    {

        $this->db->where('user_deals_id', $deals_id);
        $query = $this->db->get('vk_notify');

        if ($query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        } else {//иначе возвращаем FALSE - т.е. телега не активирована
            return false;
        }

    }

    public function pong_detail($data, $new_status, $bal)
    {
        if (!$data or !$new_status) return;

        $sql = "UPDATE `companies` SET `barter_balance`=`barter_balance` + {$bal}, `status`={$new_status} WHERE `for_deals_id`='{$data}'";

        if ($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
}