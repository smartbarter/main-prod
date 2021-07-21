<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discounts_model extends CI_Model {

    public function find_all_discounts_with_company_data_today($num, $offset) {

        $this->db->select('
                        d.summa_skidki,
                        c.company_id,
                        c.company_name,
                        c.description_company,
                        c.logo
                    ');
        $this->db->from('discounts d');
        $this->db->join('companies c', 'c.company_id=d.company_id', 'left');
        $this->db->where('TIMESTAMPDIFF(DAY, end_date, NOW()) = 0');
        $this->db->where('c.status', 2);
        $this->db->order_by('d.summa_skidki', 'DESC');
        $this->db->limit($num, $offset);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {//если нам вернулось како-то значение, то возвращаем массив значений
            return $query->result_array();
        }
        else
        {
            return FALSE;
        }

    }
    //для пагинации считаем количество скидок на сегодня
    public function count_all_discounts_today() {
        $this->db->join('companies c', 'c.company_id=d.company_id', 'left');
        $this->db->where('TIMESTAMPDIFF(DAY, end_date, NOW()) = 0');
        $this->db->where('c.status', 2);
        $this->db->from('discounts d');
        $result = $this->db->count_all_results();
        return $result;
    }

    //ищем действующую скидку компании
    //используется в company/Discounts/my_discounts
    public function find_work_discount($company_id) {

        $this->db->where('company_id', $company_id);
        $this->db->where('TIMESTAMPDIFF(DAY, end_date, NOW()) = 0');
        $this->db->limit(1);
        $query = $this->db->get('discounts');

        if($query->num_rows() > 0)
        {//если нам вернулось како-то значение, то возвращаем массив значений
            return $query->row_array();
        }
        else
        {
            return FALSE;
        }
    }

    public function find_discount_on_date($company_id, $date) {
        $this->db->where('company_id', $company_id);
        $this->db->where('end_date', $date);
        $this->db->limit(1);
        $query = $this->db->get('discounts');

        if($query->num_rows() > 0)
        {//если нам вернулось како-то значение, то возвращаем массив значений
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function count_all_companies_dicsounts($company_id) {

        $this->db->where('company_id', $company_id);
        $this->db->from('discounts');
        $result = $this->db->count_all_results();
        return $result;

    }

    //ищем все скидки компании
    public function all_discounts_company($company_id, $num, $offset) {

        $this->db->where('company_id', $company_id);
        $this->db->order_by('end_date', 'DESC');
        $query = $this->db->get('discounts', $num, $offset);

        if($query->num_rows() > 0)
        {//если нам вернулось како-то значение, то возвращаем массив значений
            return $query->result_array();
        }
        else
        {
            return FALSE;
        }
    }

    //сохраняем новую скидку
    public function save_new_discount($data) {

        $result = $this->db->insert('discounts', $data);

        if($result) {
            return TRUE;
        } else {
            return FALSE;
        } 

    }

    //функция удаления скидки
    public function delete_discount($discount_id, $company_id) {

        $this->db->where('id_skidki', $discount_id);
        $this->db->where('company_id', $company_id);
        if($this->db->delete('discounts')) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

}